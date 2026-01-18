<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions;

use Ffhs\FfhsTasks\Contracts\TaskUserGroupInterface;
use Ffhs\FfhsTasks\Facades\FfhsTasks;
use Ffhs\FfhsTasks\Models\Task;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\HtmlString;

class AssignActions extends ActionGroup
{
    public static function make(array $actions = []): static
    {
        return parent::make([]);
    }

    protected function setUp(): void
    {
        $user = auth()->user();
        $user = $user->withoutRelations();

        parent::setUp();
        $this->icon(Heroicon::User);
        $this->color(fn (Task $record) => $record->users->count() > 0 ? Color::Gray : Color::Emerald);
        $this->hidden(function (Task $record) {
            return $record->isArchived();
        });
        $this->iconButton();
        $this->actions([
            Action::make('assign_me')
                ->closeModalByClickingAway(false)
                ->label(new HtmlString('<b>'.Task::__('actions.assign_me.label').'</b>'))
                ->tooltip(Task::__('actions.assign_me.tooltip'))
                ->disabled(fn (Task $record) => once(fn () => $record->users->contains($user)))
                ->action($this->assignMe(...)),
            Action::make('unassign_me')
                ->closeModalByClickingAway(false)
                ->label(new HtmlString('<b>'.Task::__('actions.unassign_me.label').'</b>'))
                ->tooltip(Task::__('actions.unassign_me.tooltip'))
                ->visible(fn (Task $record) => once(fn () => $record->users->contains($user)))
                ->action($this->unassignMe(...)),
            Action::make('assign_group')
                ->closeModalByClickingAway(false)
                ->label(Task::__('actions.assign_group.label'))
                ->tooltip(Task::__('actions.assign_group.tooltip'))
                ->schema($this->assignGroupSchema(...))
                ->action($this->assignGroup(...)),
            Action::make('assign_user')
                ->closeModalByClickingAway(false)
                ->label(Task::__('actions.assign_user.label'))
                ->tooltip(Task::__('actions.assign_user.tooltip'))
                ->schema($this->assignPersonSchema())
                ->action($this->assignPerson(...)),
        ]);
    }

    protected function assignMe(Task $record): void
    {
        $user = auth()->user();
        if (! $record->users->contains($user)) {
            $record->users()->attach($user);
        }
    }

    protected function assignGroup(Task $record, array $data): void
    {
        $group = $data['group'] ?? '';
        $group = explode(':', $group);

        $type = $group[0];
        $id = $group[1] ?? null;

        if (empty($type) || empty($id)) {
            return;
        }

        $isExisting = $record->taskUserGroups()->where('user_group_type', $type)->where('user_group_id', $id)->exists();
        if ($isExisting) {
            return;
        }

        $record->taskUserGroups()->create([
            'user_group_type' => $type,
            'user_group_id' => $id,
        ]);
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
        $options = [];
        foreach (FfhsTasks::userGroups() as $userGroupClass) {
            /** @var TaskUserGroupInterface $userGroup */
            $userGroup = app($userGroupClass);

            $options[$userGroup::groupDisplayname()] = $userGroup::getGroupsQuery()
                ->get()
                ->mapWithKeys(function (TaskUserGroupInterface|Model $userGroupModel) use ($userGroupClass) {
                    /**@phpstan-ignore-next-line */
                    return [$userGroupClass.':'.$userGroupModel->id => $userGroupModel->getGroupModelTitle()];
                })->toArray();
        }

        return [
            Select::make('group')
                ->required()
                ->hiddenLabel()
                ->options($options),
        ];
    }

    protected function assignPersonSchema(): array
    {
        return [
            Hidden::make('userId'),
            Select::make('usersIdRaw')
                ->label(Task::__('actions.assign_user.schema.users.label'))
                ->helperText(Task::__('actions.assign_user.schema.users.helper_text'))
                ->relationship('users', FfhsTasks::config('user.name_attribute'))
                ->multiple()
                ->required()
                ->disableOptionWhen(static function (Task $record, $value) {
                    return $record->users->pluck('id')->contains($value);
                })
                ->afterStateUpdated(fn ($state, $set) => $set('userId', $state))
                ->saveRelationshipsUsing(function () {}),
        ];
    }
}
