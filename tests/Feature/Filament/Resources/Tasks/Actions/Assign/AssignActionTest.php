<?php

use App\Models\FirstUserGroup;
use App\Models\User;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\ListAllTasks;
use Ffhs\FfhsTasks\Models\Assignable;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Policies\TaskPolicy;
use Ffhs\FfhsTasks\Support\AssignableHelper;

use function Pest\Livewire\livewire;

describe('visibility', function () {
    test('is visible when assignable models are configured', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create();

        TaskPolicy::fake(['update' => true]);

        // Act & Assert
        $this->actingAs($user);

        livewire(ListAllTasks::class)
            ->assertTableActionVisible('assign', $task);
    });

    test('is hidden when no assignable models are configured', function () {
        // Arrange
        config()->set('ffhs-tasks.assignable_models', []);

        $user = User::factory()->create();
        $task = Task::factory()->create();

        TaskPolicy::fake(['update' => true]);

        // Act & Assert
        $this->actingAs($user);

        livewire(ListAllTasks::class)
            ->assertTableActionHidden('assign', $task);
    });

    test('is hidden when task is archived', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->completed()->create();

        TaskPolicy::fake(['update' => true]);

        // Act & Assert
        $this->actingAs($user);

        livewire(ListAllTasks::class)
            ->assertTableActionHidden('assign', $task);
    });
});

describe('action', function () {
    test('saves assignables to the task', function () {
        // Arrange
        config()->set('ffhs-tasks.assignable_models', [FirstUserGroup::class]);

        $user = User::factory()->create();
        $task = Task::factory()->create();
        $group = FirstUserGroup::factory()->create();

        TaskPolicy::fake(['update' => true]);

        $morphKey = AssignableHelper::getCompositeKey($group);

        // Act
        $this->actingAs($user);

        livewire(ListAllTasks::class)
            ->callTableAction('assign', $task, [
                'assignables' => [$morphKey],
            ])
            ->assertNotified();

        // Assert
        expect($task->assignables()->count())->toBe(1)
            ->and($task->assignables()->first())
            ->assignable_type->toBe(FirstUserGroup::class)
            ->assignable_id->toBe($group->id);
    });

    test('replaces existing assignables', function () {
        // Arrange
        TaskPolicy::fake(['update' => true]);

        config()->set('ffhs-tasks.assignable_models', [FirstUserGroup::class]);

        $user = User::factory()->create();
        $task = Task::factory()->create();

        $oldGroup = FirstUserGroup::factory()->create();

        Assignable::query()->create([
            'task_id' => $task->id,
            'assignable_type' => FirstUserGroup::class,
            'assignable_id' => $oldGroup->id,
        ]);

        $newGroup = FirstUserGroup::factory()->create();

        $morphKey = AssignableHelper::getCompositeKey($newGroup);

        // Act
        $this->actingAs($user);

        livewire(ListAllTasks::class)
            ->callTableAction('assign', $task, [
                'assignables' => [$morphKey],
            ]);

        // Assert
        $assignables = $task->assignables()->get();

        expect($assignables)->toHaveCount(1)
            ->and($assignables->first())
            ->assignable_id->toBe($newGroup->id);
    });
});
