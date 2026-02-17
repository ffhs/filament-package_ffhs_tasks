<?php

use App\Models\User;
use Ffhs\FfhsTasks\Enums\TaskPrivacy;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Components\AssignablesSelect;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\CreateTask;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Models\Assignable;
use Ffhs\FfhsTasks\Policies\TaskPolicy;
use Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes\TestTaskType;
use App\Models\SecondUserGroup;
use App\Models\FirstUserGroup;
use Livewire\Livewire;

describe('visibility', function () {
    test('is visible when user groups are configured', function () {
        // Arrange
        config()->set('ffhs-tasks.types', [TestTaskType::class]);
        $user = User::factory()->create();

        TaskPolicy::fake(['create' => true]);
        $this->actingAs($user);

        // Act & Assert
        Livewire::test(CreateTask::class, ['type' => TestTaskType::identifier()])
            ->assertFormFieldIsVisible('assignables');
    });

    test('is hidden when no user groups are configured', function () {
        // Arrange
        config()->set('ffhs-tasks.types', [TestTaskType::class]);
        config()->set('ffhs-tasks.assignable_models', []);
        $user = User::factory()->create();

        TaskPolicy::fake(['create' => true]);
        $this->actingAs($user);

        // Act & Assert
        Livewire::test(CreateTask::class, ['type' => TestTaskType::identifier()])
            ->assertFormFieldIsHidden('assignables');
    });
});

describe('make', function () {
    test('returns null-safe Select when user groups are empty', function () {
        config()->set('ffhs-tasks.assignable_models', []);

        $select = AssignablesSelect::make('assignables');

        expect($select)->not->toBeNull();
    });
});

describe('search results', function () {
    test('returns options from all configured user group models', function () {
        // Arrange
        config()->set('ffhs-tasks.assignable_models', [FirstUserGroup::class, SecondUserGroup::class]);
        FirstUserGroup::factory()->create(['display_name' => 'Team Alpha']);
        SecondUserGroup::factory()->create(['display_name' => 'Squad Alpha']);

        // Act
        $options = invade(new AssignablesSelect())->buildOptions('Alpha');

        // Assert
        expect($options)->toHaveCount(2)
            ->and($options->values()->all())->toContain('Team Alpha', 'Squad Alpha');
    });

    test('filters options by search term', function () {
        // Arrange
        config()->set('ffhs-tasks.assignable_models', [FirstUserGroup::class]);
        FirstUserGroup::factory()->create(['display_name' => 'Team Alpha']);
        FirstUserGroup::factory()->create(['display_name' => 'Team Beta']);

        // Act
        $options = invade(new AssignablesSelect())->buildOptions('Alpha');

        // Assert
        expect($options)->toHaveCount(1)
            ->and($options->values()->first())->toBe('Team Alpha');
    });

    test('uses composite key format for option values', function () {
        // Arrange
        config()->set('ffhs-tasks.assignable_models', [FirstUserGroup::class]);
        $group = FirstUserGroup::factory()->create(['display_name' => 'Team Alpha']);

        // Act
        $options = invade(new AssignablesSelect())->buildOptions('Alpha');

        // Assert
        $expectedKey = FirstUserGroup::class . ':::' . $group->id;
        expect($options->keys()->first())->toBe($expectedKey);
    });
});

describe('option labels', function () {
    test('resolves labels from composite values', function () {
        // Arrange
        $firstGroup = FirstUserGroup::factory()->create(['display_name' => 'Team Alpha']);
        $secondGroup = SecondUserGroup::factory()->create(['display_name' => 'Squad Beta']);

        $values = [
            FirstUserGroup::class . ':::' . $firstGroup->id,
            SecondUserGroup::class . ':::' . $secondGroup->id,
        ];

        // Act
        $labels = invade(new AssignablesSelect())->buildOptionLabels($values);

        // Assert
        expect($labels)->toHaveCount(2)
            ->and($labels->values()->all())->toContain('Team Alpha', 'Squad Beta');
    });
});

describe('save relationships', function () {
    test('saves user group relationships on task creation', function () {
        // Arrange
        config()->set('ffhs-tasks.types', [TestTaskType::class]);
        config()->set('ffhs-tasks.assignable_models', [FirstUserGroup::class]);

        $user = User::factory()->create();
        $group = FirstUserGroup::factory()->create(['display_name' => 'Team Alpha']);

        TaskPolicy::fake(['create' => true]);
        $this->actingAs($user);

        $compositeValue = FirstUserGroup::class . ':::' . $group->id;

        // Act
        Livewire::test(CreateTask::class, ['type' => TestTaskType::identifier()])
            ->fillForm([
                'title' => 'Test Task',
                'privacy' => TaskPrivacy::Public,
                'assignables' => [$compositeValue],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        // Assert
        $task = Task::query()->latest('id')->first();

        expect(
            Assignable::query()
                ->where('task_id', $task->id)
                ->where('assignable_type', FirstUserGroup::class)
                ->where('assignable_id', $group->id)
                ->exists()
        )->toBeTrue();
    });
});
