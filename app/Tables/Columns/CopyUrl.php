<?php

namespace App\Tables\Columns;

use Closure;
use Filament\Tables\Columns\Column;
use Illuminate\Contracts\View\View;

class CopyUrl extends Column
{
    protected ?Closure $customUrlCallback = null;

    public function customUrl(Closure $callback): static
    {
        $this->customUrlCallback = $callback;

        return $this;
    }

    public function render(): View
    {
        $url = null;

        if ($this->customUrlCallback) {
            $record = $this->getRecord();
            $url = call_user_func($this->customUrlCallback, $record);
        }

        return view('tables.columns.copy-url', ['url' => $url]);
    }
}
