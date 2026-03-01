<?php

namespace Ffhs\FfhsTasks;

use Illuminate\Database\Eloquent\Model;

/**
 * @template T of Model
 *
 * @param  class-string<T>  $class
 * @return class-string<T>
 */
function resolve_model_class(string $class): string
{
    $classes = config()->array('ffhs-tasks.models');

    return (string) ($classes[$class] ?? $class);
}
