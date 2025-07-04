<?php

namespace Ffhs\FfhsTasks;

class FfhsTasks {

    public function config(string ...$key)
    {
        if(empty($key)) {
            return config('ffhs-tasks');
        }

        return config('ffhs-tasks.' . implode('.', $key));
    }

}
