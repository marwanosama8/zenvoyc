@use('Illuminate\Support\Facades\Vite')
@use('App\Helpers\Helpers')
@use('App\Models\InvoiceTheme')
@use('App\Helpers\CalculationHelpers')
@use('App\Constants\InvoiceThemeConstants')

@php
    $currency = Helpers::getCurrancyData($data['provider']['currency_id']);
    $lang = $data['provider']['invoice_language'];
    $invoiceTheme = InvoiceTheme::find($data['provider']['invoice_theme_id']);
    if ($invoiceTheme && $invoiceTheme->is_active) {
        $invoiceThemeAliases = $invoiceTheme->aliases;
    } else {
        $invoiceThemeAliases = InvoiceTheme::find(InvoiceThemeConstants::DEFAULT_ID)->aliases;
    }
@endphp
<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        {!! Vite::content('resources/css/invoice/themes/' . $invoiceThemeAliases . '.css') !!}
    </style>
    <title>{{ __('invoice-template.invoice', locale: $lang) }}</title>
</head>

<body style="{{ !$print ? 'width: 1140px; margin: auto; padding: 20px;' : ''}}">

    <table class="w-full">
        <tr>
            <td class="w-half">
                <img src="{{ asset('storage/' . $data['provider']['avatar_url']) }}" alt="pro-tool" width="200" />
            </td>
            <td class="w-half">
                <div class="head">
                    <h2>{{ __('invoice-template.invoice_no', locale: $lang) }}:
                        {{ $data['invoice']->rgnr }}</h2>
                </div>
            </td>
        </tr>
    </table>
    <div class="margin-top">
        <table class="w-full">
            <tr>
                <td class="w-half">
                    <div>
                        <h4>{{ __('invoice-template.to', locale: $lang) }}:</h4>
                    </div>
                    <div>{{ $data['invoice']->customer->name }}</div>
                    <div>{{ $data['invoice']->customer_address }}</div>
                </td>
                <td class="w-half">
                    <div>
                        <h4>{{ __('invoice-template.from', locale: $lang) }}:</h4>
                    </div>
                    <div>
                        <strong>{{ $data['provider']['legal_name'] }}</strong>
                    </div>
                    <div>
                        {!! $data['provider']['address'] !!}
                    </div>
                    <div>
                        {{ __('invoice-template.ust_id', locale: $lang) }}:
                        {{ $data['provider']['vat_id'] }}<br />
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="margin-top">
        <div class="text-center">

            {{ __('invoice-template.invoice_message', locale: $lang) }}
            <strong><a class="text-blue-500"
                    href="mailto:{{ $data['provider']['contact_email'] }}">{{ $data['provider']['contact_email'] }}</a></strong>
        </div>
    </div>

    <div class="margin-top">
        <table class="products">
            <tr>
                <th>#</th>
                <th>{{ __('invoice-template.description', locale: $lang) }}
                </th>
                <th class="units">
                    {{ __('invoice-template.units', locale: $lang) }}</th>
                <th class="price">
                    {{ __('invoice-template.price_in', locale: $lang) . ' ' . $currency->symbol }}
                </th>
            </tr>
            @foreach ($data['invoice']->invoice_item as $item)
                <tr class="items">
                    <td class="number">{{ $loop->iteration }}</td>
                    <td class="description">{!! nl2br($item->description) !!}</td>
                    <td class="units">
                        @switch($item->type)
                            @case(1)
                                {{ __('invoice-template.price_in', locale: $lang) }}
                            @break

                            @case(2)
                                @if ($item->amount > 1)
                                    {{ $item->amount }}
                                    {{ __('invoice-template.hours', locale: $lang) }}
                                @else
                                    {{ $item->amount }}
                                    {{ __('invoice-template.hour', locale: $lang) }}
                                @endif
                            @break

                            @default
                                {{ __('invoice-template.pauschal', locale: $lang) }}
                        @endswitch
                    </td>
                    <td class="price">{{ number_format($item->price, 2, ',', '.') . ' ' . $currency->symbol }} </td>
                </tr>
            @endforeach
        </table>

    </div>

    <div class="total">
        {{ __('invoice-template.total_items_price', locale: $lang) }}:
        {{ $currency->symbol . number_format($data['invoice']->getTotalNetto(), 2, ',', '.') }}
    </div>

    @if ($data['invoice']->has_vat)
        <div class="total">
            {{ __('invoice-template.tax', locale: $lang) }}:
            {{ $currency->symbol . number_format(CalculationHelpers::getTotalVat($data['invoice']->getTotalNetto(), $data['invoice']->vat_percent), 2, ',', '.') }}
        </div>
        <div class="total-separator"></div>
        <table class="total-overall-table">
            <tr>

                <td class="left">
                    <h3>{{ __('invoice-template.payable_at', ['date' => $data['invoice']->date_pay]) }}</h3>
                </td>
                <td class="right" style="text-align: right;">
                    <h3>
                        {{ __('invoice-template.total', locale: $lang) }}:
                        {{ $currency->symbol . number_format(CalculationHelpers::getTotalBrutto($data['invoice']->getTotalNetto(), $data['invoice']->vat_percent), 2, ',', '.') }}
                    </h3>
                </td>
            </tr>
        </table>
    @else
        <div class="total-separator"></div>
        <table class="total-overall-table">
            <tr>
                <td class="left">
                    <h3>{{ __('invoice-template.payable_at', ['date' => $data['invoice']->date_pay]) }}</h3>
                </td>
                <td class="right" style="text-align: right;">
                    <h3>
                        {{ __('invoice-template.total', locale: $lang) }}:
                        {{ $currency->symbol . number_format($data['invoice']->getTotalNetto(), 2, ',', '.') }}
                    </h3>
                </td>
            </tr>
        </table>
    @endif



    <!-- Additional footer components from old file -->
    @if ($data['invoice']->info !== null)
        <div class="separator"></div>
        <div class="flex-wrap">
            <div class="one-sixth"></div>
            <div class="two-thirds">
                <h3 class="bold">
                    {{ __('invoice-template.notice', locale: $lang) }}:</h3>
                <p>{!! nl2br($data['invoice']->info) !!}</p>
            </div>
            <div class="one-sixth"></div>
        </div>
    @endif
    <div class="separator"></div>
    <table class="w-full">
        <tr>
            <td class="w-half">
                <h5>
                    @if (!is_null($data['provider']['legal_name']))
                        {{ $data['provider']['legal_name'] }}<br />
                    @endif
                    @if (!is_null($data['provider']['managing_director']))
                        {{ __('invoice-template.managing_director', locale: $lang) . ': ' . $data['provider']['managing_director'] }}<br />
                    @endif
                    @if (!is_null($data['provider']['place_of_jurisdiction']))
                        {{ __('invoice-template.place_of_jurisdiction', locale: $lang) . ': ' . $data['provider']['place_of_jurisdiction'] }}<br />
                    @endif
                    <br />
                    @if (!is_null($data['provider']['vat_id']))
                        {{ __('invoice-template.UST_no', locale: $lang) . ': ' . $data['provider']['vat_id'] }}<br />
                    @endif
                </h5>
            </td>
            <td class="w-half">
                <h5>
                    Postbank<br />
                    @if (!is_null($data['provider']['account_number']))
                        {{ __('invoice-template.account_no', locale: $lang) . ': ' . $data['provider']['account_number'] }}<br />
                    @endif
                    @if (!is_null($data['provider']['bank_code']))
                        {{ __('invoice-template.bank_code', locale: $lang) . ': ' . $data['provider']['bank_code'] }}<br />
                    @endif
                    @if (!is_null($data['provider']['iban']))
                        {{ __('invoice-template.iban', locale: $lang) . ': ' . $data['provider']['iban'] }}<br />
                    @endif
                    @if (!is_null($data['provider']['bic']))
                        {{ __('invoice-template.bic', locale: $lang) . ': ' . $data['provider']['bic'] }}<br />
                    @endif
                </h5>
            </td>
        </tr>
    </table>
    <div class="flex-wrap half-wrapper">


    </div>
    <div class="separator"></div>
    <div class="text-center">
        {{ __('invoice-template.general_terms', locale: $lang) }} <a href="{{ $data['provider']['website_url'] }}"
            class="underline">{{ $data['provider']['website_url'] }}</a>
    </div>
</body>


</html>
