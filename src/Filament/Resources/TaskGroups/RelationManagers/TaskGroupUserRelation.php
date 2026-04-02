<?php

namespace Ffhs\FfhsTasks\Filament\Resources\TaskGroups\RelationManagers;

use Ffhs\FfhsTasks\Models\TaskGroup;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * @method TaskGroup getOwnerRecord()
 */
class TaskGroupUserRelation extends RelationManager
{
    protected static string $relationship = 'users';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('ffhs-tasks::task_groups.attributes.user');
    }


    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(config('ffhs-tasks.user.name_attribute', 'name'))
                    ->label('Name'),
            ])
            ->filters([])
            ->headerActions([
                AttachAction::make()
                    ->schema(fn() => [
                        Select::make('userIds')
                            ->hiddenLabel()
                            ->searchable()
                            ->multiple()
                            ->required()
                            ->options(
                                app(config('ffhs-tasks.user.model', \App\Models\User::class))::pluck(
                                    config('ffhs-tasks.user.name_attribute', 'name'),
                                    'id'
                                )
                            ),
                    ])
                    ->action(function (array $data): void {
                        $this->getOwnerRecord()->users()->attach($data['userIds']);
                    }),
            ])
            ->recordActions([
                DetachAction::make(),
            ])
            ->toolbarActions([
                DetachBulkAction::make(),
            ]);
    }

    protected function canEdit(Model $record): bool
    {
        return false;
    }
}
