<?php

namespace Ffhs\FfhsTasks\Traits;

use Ffhs\FfhsTasks\Facades\FfhsTasks;
use UnitEnum;

trait IsTaskResource
{
    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return FfhsTasks::__('navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        /**@var \Ffhs\FfhsTasks\Traits\IsFfhsTaskModel $modelClass */
        $modelClass = static::getModel();
        return $modelClass::__('resource.navigation-label');
    }

    public static function getModel(): string
    {
        /**@var \Ffhs\FfhsTasks\Traits\IsFfhsTaskModel $modelClass */
        $modelClass = static::$model;
        return FfhsTasks::config('models.' . $modelClass::configKey()) ?: $modelClass;
    }


    public static function getTitleCaseModelLabel(): string
    {
        /**@var \Ffhs\FfhsTasks\Traits\IsFfhsTaskModel $modelClass */
        $modelClass = static::$model;
        return $modelClass::__('label.singular');
    }

    public static function getTitleCasePluralModelLabel(): string
    {
        /**@var \Ffhs\FfhsTasks\Traits\IsFfhsTaskModel $modelClass */
        $modelClass = static::$model;
        return $modelClass::__('label.plural');
    }

}
