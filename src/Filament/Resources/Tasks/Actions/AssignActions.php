<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions;

use App\Models\User;
use Ffhs\FfhsTasks\Models\Task;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;

class AssignActions extends ActionGroup
{
    public static function make(array $actions = []): static
    {
        return parent::make([]);
    }

    protected function setUp(): void
    {
        /**@var \App\Models\User $user */
        $user = auth()->user();
        $user = $user->withoutRelations();

        parent::setUp();
        $this->icon(Heroicon::User);
        $this->color(fn(Task $record) => $record->users->count() > 0 ? Color::Gray : Color::Emerald);
        $this->iconButton();
        $this->actions([
            Action::make('assign_me')
                ->label(new HtmlString('<b>' . Task::__('actions.assign_me.label') . '</b>'))
                ->tooltip(Task::__('actions.assign_me.tooltip'))
                ->disabled(fn(Task $record) => once(fn() => $record->users->contains($user)))
                ->action($this->assignMe(...)),
            Action::make('unassign_me')
                ->label(new HtmlString('<b>' . Task::__('actions.unassign_me.label') . '</b>'))
                ->tooltip(Task::__('actions.unassign_me.tooltip'))
                ->visible(fn(Task $record) => once(fn() => $record->users->contains($user)))
                ->action($this->unassignMe(...)),
            Action::make('assign_group')
                ->label(Task::__('actions.assign_group.label'))
                ->tooltip(Task::__('actions.assign_group.tooltip'))
                ->schema($this->assignGroupSchema(...))
                ->action($this->assignGroup(...)),
            Action::make('assign_user')
                ->label(Task::__('actions.assign_user.label'))
                ->tooltip(Task::__('actions.assign_user.tooltip'))
                ->schema($this->assignPersonSchema())
                ->action($this->assignPerson(...)),
        ]);
    }

    protected function assignMe(Task $record): void
    {
        $user = auth()->user();
        if (!$record->users->contains($user)) {
            $record->users()->attach($user);
        }
    }

    protected function assignGroup(Task $record, array $data): void
    {
    }

    protected function unassignMe(Task $record, array $data): void
    {
        $record->users()->detach(auth()->id());
    }

    protected function assignPerson($data, Task $record, Action $action): void
    {
        $users = User::query()
            ->where('id', $data)
            ->whereNotIn('id', $record->users->pluck('id'))
            ->pluck('id');
        $record->users()->attach($users);
    }

    protected function assignGroupSchema(): array
    {
        //toDo
        return [];
    }

    protected function assignPersonSchema(): array
    {
        return [
            Hidden::make('userId'),
            Select::make('usersIdRaw')
                ->label(Task::__('actions.assign_user.schema.users.label'))
                ->helperText(Task::__('actions.assign_user.schema.users.helper_text'))
                ->relationship('users', 'email')
                ->multiple()
                ->required()
                ->disableOptionWhen(static function (Task $record, $value) {
                    return $record->users->pluck('id')->contains($value);
                })
                ->afterStateUpdated(fn($state, $set) => $set('userId', $state))
                ->saveRelationshipsUsing(function () {

                })
        ];
    }
}
