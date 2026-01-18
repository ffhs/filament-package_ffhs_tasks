<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Schemas;

use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\TaskType\TaskType;
use Ffhs\FfhsUtils\Contracts\Type;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class TaskCreateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                self::getDefaultSection(),
                self::getSettingsSection()
            ]);
    }

    /**
     * @return Section
     */
    public static function getSettingsSection(): Section
    {
        return Section::make()
            ->columnSpanFull()
            ->statePath('settings')
            ->visible(fn (Get $get) => once(function () use ($get) {
                return $get('type') && TaskType::getTypeFromIdentifier($get('type'));
            }))
            ->hidden(fn (Section $component) => !$component->getChildComponents())
            ->schema(fn (Get $get) => once(function () use ($get) {
                $typeOptionIdentifier = $get('type');

                if (is_null($typeOptionIdentifier)) {
                    return [];
                }

                /**@var TaskType $type */
                $type = TaskType::getTypeFromIdentifier($typeOptionIdentifier);
                return $type?->getSettingSchema() ?? [];
            }));
    }

    public static function getDefaultSection(): Section
    {
        $typeOptions = collect(config('ffhs-tasks.user_creatable_types'))
            ->mapWithKeys(function (string|Type $item) {
                return [$item::identifier() => $item::displayname()];
            })->toArray();

        return Section::make()
            ->columns(2)
            ->schema([
                TextInput::make('title')
                    ->label(Task::__('attributes.title.label'))
                    ->helperText(Task::__('attributes.title.helper_text'))
                    ->markAsRequired(false)
                    ->required(),
                Select::make('type')
                    ->label(Task::__('attributes.type.label'))
                    ->label(Task::__('attributes.type.label'))
                    ->options($typeOptions)
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        if (is_null($state)) {
                            $set('settings', []);
                            return;
                        }

                        /**@var TaskType $type */
                        $type = TaskType::getTypeFromIdentifier($state);
                        foreach ($type->getSettingSchema() as $settingSchema) {
                            $set('settings.' . $settingSchema->getStatePath(false), $settingSchema->getDefaultState());
                        }
                    }),
                Toggle::make('can_cancel')
                    ->label(Task::__('attributes.can_cancel.label'))
                    ->live(),
                Textarea::make('description')
                    ->helperText(Task::__('attributes.description.helper_text'))
                    ->label(Task::__('attributes.description.label'))
                    ->columnSpanFull()
                    ->nullable(),
                Fieldset::make('Zeit')
                    ->columnSpanFull()
                    ->columns()
                    ->schema([
                        DateTimePicker::make('start_at')
                            ->helperText(Task::__('attributes.start_at.helper_text'))
                            ->label(Task::__('attributes.start_at.label'))
                            ->seconds(false)
                            ->nullable(),
                        DateTimePicker::make('deadline_at')
                            ->helperText(Task::__('attributes.deadline_at.helper_text'))
                            ->label(Task::__('attributes.deadline_at.label'))
                            ->seconds(false)
                            ->nullable(),
                    ])
            ]);
    }
}
