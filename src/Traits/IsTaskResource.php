<?php

namespace Ffhs\FfhsTasks\Traits;

use Ffhs\FfhsTasks\Facades\FfhsTasks;
use Ffhs\FfhsTasks\Models\Task;
use UnitEnum;

trait IsTaskResource
{
    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return FfhsTasks::__('navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        /** @var class-string<Task> $modelClass */
        $modelClass = static::getModel();

        return $modelClass::__('resource.navigation-label');
    }

    /**
     * @return class-string<Task>
     */
    public static function getModel(): string
    {
        $modelClass = static::$model;

        /** @phpstan-ignore-next-line */
        return FfhsTasks::config('models.'.$modelClass::configKey()) ?: $modelClass;
    }

    public static function getTitleCaseModelLabel(): string
    {
        /** @var class-string<Task> $modelClass */
        $modelClass = static::getModel();

        return $modelClass::__('label.singular');
    }

    public static function getTitleCasePluralModelLabel(): string
    {
        /** @var class-string<Task> $modelClass */
        $modelClass = static::getModel();

        return $modelClass::__('label.plural');
    }
}
