<?php

namespace App\Filament\Company\Resources\InvoiceResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Company\Resources\InvoiceResource;
use App\Http\Requests\CreateInvoiceRequest;
use App\Models\Company;
use App\Models\Customer;
use App\Rules\ValidRefNumber;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class CreateHandler extends Handlers
{
    public static string | null $uri = '/';
    public static string | null $resource = InvoiceResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel()
    {
        return static::$resource::getModel();
    }

    public function handler(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'ref_number' => [
                'required',
                'string',
                'max:13',
                new ValidRefNumber($request->tenant)
            ],
            'customer_address' => ['nullable', 'string', 'max:250'],
            'date_origin' => ['required', 'date'],
            'date_start' => ['required', 'date', 'after_or_equal:date_origin'],
            'date_end' => ['required', 'date', 'after_or_equal:date_start'],
            'date_pay' => ['required', 'date', 'after_or_equal:date_end'],
            'info' => ['nullable', 'string', 'max:250'],
            'has_vat' => ['required', 'boolean'],
            'invoice_items' => ['array', 'required'],
            'invoice_items.*.description' => ['required', 'string', 'max:250'],
            'invoice_items.*.amount' => ['required', 'integer'],
            'invoice_items.*.type' => ['required', 'integer', 'between:1,3'],
            'invoice_items.*.price' => ['required', 'decimal:2'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Bad Request', 'data' => $validator->messages()], 400);
        }

        try {
            DB::beginTransaction();

            $customer = Customer::withoutGlobalScopes()->whereReference($request->get('ref_number'))->first();
            $tenant = Company::whereSlug($request->tenant)->first();
            $model = new (static::getModel());

            $model->invoiceable_id = $tenant->id;
            $model->invoiceable_type = get_class($tenant);
            $model->customer_id = $customer->id;
            $model->customer_address = filled($request->get('customer_address')) ? $request->get('customer_address') : $customer->full_customer_address;
            $model->rate = $customer->rate;
            $model->info = $request->get('info');
            $model->vat_percent = $tenant->settings->vat_percent;
            $model->has_vat = $request->get('has_vat');
            $model->date_origin = $request->get('date_origin');
            $model->date_start = $request->get('date_start');
            $model->date_end = $request->get('date_end');
            $model->date_pay = $request->get('date_pay');

            $model->save();

            $invoiceItems = $request->get('invoice_items');

            foreach ($invoiceItems as $item) {
                $model->invoice_item()->create($item);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::channel('api_errors')->error('Failed to create invoice and its items.', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return response()->json(['error' => 'Failed to create invoice. Please try again.'], 500);
        }

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}
