<?php
namespace App\Filament\Company\Resources\InvoiceResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceTransformer extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->resource->toArray();
    }
}
