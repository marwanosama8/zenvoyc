<?php

namespace App\Services;

use App\Constants\ConfigConstants;
use App\Models\Config;
use App\Models\PanelConfig;

class PanelConfigManager
{
    public function loadConfigs()
    {
        $configs = cache()->many(ConfigConstants::OVERRIDABLE_CONFIGS);

        config($this->toKeyValueArray($configs));
    }

    public function set(string $key, $value): void
    {
        // if (! in_array($key, ConfigConstants::OVERRIDABLE_CONFIGS)) {
        //     throw new \Exception("Config key $key is not overridable");
        // }

        PanelConfig::set($key, $value);

        cache()->forever($key, $value);

        config([$key => $value]);
    }

    public function get(string $key, ?string $default = null): string|array|null
    {
        try {
            return PanelConfig::get($key) ?? $default;
        } catch (\Exception $e) {
            return $default;
        }
    }

    private function toKeyValueArray($configs): array
    { 
        $result = [];
        foreach ($configs as $key => $value) {
            if (is_null($value)) {
                continue;
            }

            $result[$key] = $value;
        }

        return $result;
    }
}
