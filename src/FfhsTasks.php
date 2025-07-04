<?php

namespace Ffhs\FfhsTasks;

use Illuminate\Contracts\Translation\Translator;

class FfhsTasks
{
    public function config(string ...$key)
    {
        if (empty($key)) {
            return config('ffhs-tasks');
        }

        return config('ffhs-tasks.' . implode('.', $key));
    }

    public function __(string ...$keys): array|string|Translator|null
    {
        $key = implode('.', $keys);
        return __('filament-package_ffhs_tasks::' . $key);
    }

}
