<?php

namespace App\Models;

use App\Helpers\TenancyHelpers;
use App\Models\Scopes\PanelConfigScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ScopedBy([PanelConfigScope::class])]
class PanelConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    public function configable()
    {
        return $this->morphTo();
    }

    public static function get(string $key): ?string
    {
        $config = self::where('key', $key)->first();
        if ($config) {
            return $config->value;
        }

        return null;
    }

    public static function set(string $key, ?string $value): void
    {
        $config = self::where('key', $key)->first();
        if ($config) {
            $config->value = $value;
            $config->save();
        } else {
            self::create([
                'key' => $key,
                'value' => $value,
            ]);
        }
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (PanelConfig $model) {
            $currentTenant = TenancyHelpers::getTenant();

            if (empty($model->configable_type)) {
                $model->configable_type = is_null($currentTenant) ? 'App\Models\User' : 'App\Models\Company';
            }
            if (empty($model->configable_id)) {
                $model->configable_id = is_null($currentTenant) ? auth()->id() : $currentTenant->id;
            }
        });
    }
}
