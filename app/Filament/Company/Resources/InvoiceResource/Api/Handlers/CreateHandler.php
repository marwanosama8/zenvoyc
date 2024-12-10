<?php
namespace App\Filament\Company\Resources\InvoiceResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Company\Resources\InvoiceResource;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = InvoiceResource::class;
    protected static ?string $tenantOwnershipRelationshipName = 'company';
    
    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    public function handler(Request $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}