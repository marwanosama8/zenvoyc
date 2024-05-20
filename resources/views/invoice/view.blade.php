<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>RG {{ $data->rgnr }} - {{ $data->Customer->name }}</title>


    <style>
        @import url('https://fonts.googleapis.com/css?family=Quantico|Roboto+Condensed&display=swap');

        body {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 15px;
            background-color: #EAEAEA;
        }
    </style>

    <script src="{{ asset('component/invoice/js/fontawesome.min.js') }}"></script>
    <link href="{{ asset('component/invoice/css/tailwind.css') }}" rel="stylesheet" />

</head>

<body class="container mx-auto">
    <div class="container min-h[1308px] bg-white">
        @if (!$print)
            <span class="">
                <a href="/invoices" class="inline-block p-4 text-blue-600 "><i class="far fa-home"></i> Index</a>
                <a href="{{ route('invoice.send', $data->id) }}" class="inline-block p-4 text-blue-600 blue"><i
                        class="far fa-paper-plane"></i>
                    Sende Rechnungen</a>
                <a href="{{ route('invoice.download', $data->id) }}" class="inline-block p-4 text-red-600 -red"><i
                        class="far fa-file-pdf"></i>
                    Download PDF</a>
                <a href="/invoices/{{ $data->id }}/edit" class="inline-block p-4 text-green-900"><i
                        class="far fa-pencil"></i>
                    Editieren</a>
                <a href="/invoices/{{ $data->id }}/create" class="inline-block p-4 text-green-900"><i
                        class="far fa-plus"></i>
                    Neue Rechnung</a>
            </span>
        @endif
        <div id="print-area">
            <div class="flex pt-8">
                <div class="float-left w-4/6 px-4 mr-4">
                    <img class="mt-5 ml-10" src="{{ asset('component/invoice/logo.png') }}" />
                </div>
                <div class="relative float-left w-1/3 px-4">
                    <br />
                    <strong>Solution-Work UG (haftungsbeschränkt)</strong>
                    <br />
                    Landsknechtweg 22
                    <br />
                    68163 Mannheim<br />
                    Deutschland/Germany<br />
                    UST-ID: DE278618936<br />
                    <br />
                </div>
            </div>
            <div class="h-0.5 mx-4 bg-black"></div>
            <br />
            <div class="flex flex-col items-center">
                <div style="text-align: center;" class="w-full text-center">

                    Diese Rechnung wurde elektronisch erstellt. Bei Fragen o der Problemen wenden Sie sich an <strong><a
                            class="text-blue-500"
                            href="mailto:buchhaltung@solution-work.de">buchhaltung@solution-work.de</a></strong>
                </div>
                <div class="h-0.5 my-2 bg-black"></div>
            </div>
            <div class="h-0.5 m-4 bg-black"></div>
            <br />

            {{-- neeew table  --}}
            <div class="flex flex-col mx-4">
                <div class="w-full overflow-x-auto">
                    <div class="inline-block min-w-full py-2 sm:px-6 lg:px-8">
                        <div class="overflow-hidden">
                            <table class="max-w-full mx-auto text-xs font-light text-center sm:text-sm">
                                <thead
                                    class="font-medium text-white bg-blue-800 border-b dark:border-neutral-500 dark:bg-neutral-900">
                                    <tr>
                                        <th scope="col" class="px-6 py-4"> # </th>
                                        <th scope="col" class="px-6 py-4 text-center"> Beschreibung </th>
                                        <th scope="col" class="px-6 py-4"> Einheiten </th>
                                        <th scope="col" class="px-6 py-4"> Preis in € </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($data->invoice_item as $item)
                                        <tr class="max-w-xs text-sm border-b dark:border-neutral-500">
                                            <td class="px-6 py-4 font-medium text-left whitespace-nowrap">
                                                {{ $loop->iteration }} </td>
                                            <td class="px-6 py-4 whitespace-normal"> {!! nl2br($item->description) !!} </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @switch($item->type)
                                                    @case(1)
                                                        pauschal
                                                    @break

                                                    @case(2)
                                                        @if ($item->amount > 1)
                                                            {{ $item->amount }} Stunden
                                                        @else
                                                            {{ $item->amount }} Stunde
                                                        @endif
                                                    @break

                                                    @default
                                                        pauschal
                                                @endswitch
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ number_format($item->price, 2, ',', '.') }} € </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


            <div class="h-0.5 m-4 bg-black"></div>
            {{-- end new table --}}

            <div class="flex flex-wrap">
                <div class="w-full md:w-9/12 md:text-right md:pr-8">
                    <h3 class="text-lg">Nettosumme:</h3>
                </div>
                <div class="w-full md:w-3/12 ">
                    <h3 class="text-lg font-bold">{{ number_format($data->getTotalNetto(), 2, ',', '.') }} €</h3>
                </div>
                @if ($data->ust)
                    <div class="w-full md:w-9/12 md:text-right md:pr-8">
                        <h3>Umsatzsteuer ({{ $data->getCurrentVat() }}%):</h3>
                    </div>
                    <div class="w-full md:w-3/12 ">
                        <h3 class="text-lg font-bold">{{ number_format($data->getTotalVat(), 2, ',', '.') }} €</h3>
                    </div>
                @endif
                <div class="w-full md:w-9/12 md:text-right md:pr-8"></div>
                <div class="w-full md:w-3/12 ">
                    <div class="w-full border border-black"></div>
                </div>
                <div class="relative block md:w-1/2 min-h-[1px] md:px-4 my-5">
                    <strong>Zahlbar bis zum {{ $data->date_pay }} ohne Abzüge</strong>
                </div>
                <div class="w-full md:w-3/12 md:text-right md:pr-8">
                    <h3 class="font-bold">Endpreis:</h3>
                </div>
                @if ($data->ust)
                    <div class="w-full md:w-3/12 ">
                        <h3 class="font-bold">{{ number_format($data->getTotalBrutto(), 2, ',', '.') }} €</h3>
                    </div>
                @else
                    <div class="w-full md:w-3/12 ">
                        <h3 class="font-bold">{{ number_format($data->getTotalNetto(), 2, ',', '.') }} €</h3>
                    </div>
                @endif
            </div>



            @if ($data->info !== null)
                <div class="h-0.5 m-4 bg-slate-300"></div>

                <div class="flex flex-wrap">
                    <div class="w-full md:w-2/12"></div>
                    <div class="w-full md:w-8/12">
                        <h3 class="font-bold">Hinweis:</h3>
                        <p>{!! nl2br($data->info) !!}</p>
                    </div>
                    <div class="w-full md:w-2/12"></div>
                </div>
            @endif
            <div class="h-0.5 m-4 bg-slate-300"></div>

            <div class="flex flex-wrap">
                <div class="relative w-full md:w-1/2 min-h-[1px] md:px-4">
                    <h5>
                        Solution-Work UG (haftungsbeschränkt)<br />
                        Geschäftsführer Michael Klein<br />
                        Gerichtsstand: Mannheim<br />
                        HRB-712586<br />
                        UST-Ident Nummer DE278618936<br />
                    </h5>
                </div>
                <div class="relative w-full md:w-1/2 min-h-[1px] md:px-4">
                    <h5>
                        Postbank<br />
                        Konto Nr.: 689414705<br />
                        BLZ: 60010070<br />
                        IBAN: DE88600100700689414705<br />
                        BIC: PBNKDEFF<br />
                    </h5>
                </div>
            </div>


        </div>
        <div class="h-0.5 m-4 bg-slate-300"></div>

        <div class="col-span-12 text-center">
            Unsere Allgemeinen Geschäftsbedingungen finden Sie unter <a href="https://www.solution-work.de/agb"
                class="underline">www.solution-work.de/agb</a>
        </div>

    </div>

</body>

</html>
