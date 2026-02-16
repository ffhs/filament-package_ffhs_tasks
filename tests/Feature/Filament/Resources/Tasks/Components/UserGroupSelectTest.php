<?php

use App\Models\User;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Components\UserGroupSelect;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\CreateTask;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Models\TaskUserGroup;
use Ffhs\FfhsTasks\Policies\TaskPolicy;
use Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes\TestTaskType;
use Ffhs\FfhsTasks\Tests\Fixtures\UserGroups\AnotherTestUserGroup;
use Ffhs\FfhsTasks\Tests\Fixtures\UserGroups\TestUserGroup;
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
            ->assertFormFieldIsVisible('taskUserGroups');
    });

    test('is hidden when no user groups are configured', function () {
        // Arrange
        config()->set('ffhs-tasks.types', [TestTaskType::class]);
        config()->set('ffhs-tasks.user_groups', []);
        $user = User::factory()->create();

        TaskPolicy::fake(['create' => true]);
        $this->actingAs($user);

        // Act & Assert
        Livewire::test(CreateTask::class, ['type' => TestTaskType::identifier()])
            ->assertFormFieldIsHidden('taskUserGroups');
    });
});

describe('make', function () {
    test('returns null-safe Select when user groups are empty', function () {
        config()->set('ffhs-tasks.user_groups', []);

        $select = UserGroupSelect::make('taskUserGroups');

        expect($select)->not->toBeNull();
    });
});

describe('search results', function () {
    test('returns options from all configured user group models', function () {
        // Arrange
        config()->set('ffhs-tasks.user_groups', [TestUserGroup::class, AnotherTestUserGroup::class]);
        TestUserGroup::factory()->create(['display_name' => 'Team Alpha']);
        AnotherTestUserGroup::factory()->create(['display_name' => 'Squad Alpha']);

        // Act
        $options = invade(new UserGroupSelect())->buildOptions('Alpha');

        // Assert
        expect($options)->toHaveCount(2)
            ->and($options->values()->all())->toContain('Team Alpha', 'Squad Alpha');
    });

    test('filters options by search term', function () {
        // Arrange
        config()->set('ffhs-tasks.user_groups', [TestUserGroup::class]);
        TestUserGroup::factory()->create(['display_name' => 'Team Alpha']);
        TestUserGroup::factory()->create(['display_name' => 'Team Beta']);

        // Act
        $options = invade(new UserGroupSelect())->buildOptions('Alpha');

        // Assert
        expect($options)->toHaveCount(1)
            ->and($options->values()->first())->toBe('Team Alpha');
    });

    test('uses composite key format for option values', function () {
        // Arrange
        config()->set('ffhs-tasks.user_groups', [TestUserGroup::class]);
        $group = TestUserGroup::factory()->create(['display_name' => 'Team Alpha']);

        // Act
        $options = invade(new UserGroupSelect())->buildOptions('Alpha');

        // Assert
        $expectedKey = TestUserGroup::class . ':::' . $group->id;
        expect($options->keys()->first())->toBe($expectedKey);
    });
});

describe('option labels', function () {
    test('resolves labels from composite values', function () {
        // Arrange
        $firstGroup = TestUserGroup::factory()->create(['display_name' => 'Team Alpha']);
        $secondGroup = AnotherTestUserGroup::factory()->create(['display_name' => 'Squad Beta']);

        $values = [
            TestUserGroup::class . ':::' . $firstGroup->id,
            AnotherTestUserGroup::class . ':::' . $secondGroup->id,
        ];

        // Act
        $labels = invade(new UserGroupSelect())->buildOptionLabels($values);

        // Assert
        expect($labels)->toHaveCount(2)
            ->and($labels->values()->all())->toContain('Team Alpha', 'Squad Beta');
    });
});

describe('save relationships', function () {
    test('saves user group relationships on task creation', function () {
        // Arrange
        config()->set('ffhs-tasks.types', [TestTaskType::class]);
        config()->set('ffhs-tasks.user_groups', [TestUserGroup::class]);
        $user = User::factory()->create();
        $group = TestUserGroup::factory()->create(['display_name' => 'Team Alpha']);

        TaskPolicy::fake(['create' => true]);
        $this->actingAs($user);

        $compositeValue = TestUserGroup::class . ':::' . $group->id;

        // Act
        Livewire::test(CreateTask::class, ['type' => TestTaskType::identifier()])
            ->fillForm([
                'title' => 'Test Task',
                'taskUserGroups' => [$compositeValue],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        // Assert
        $task = Task::query()->latest('id')->first();

        expect(
            TaskUserGroup::query()
                ->where('task_id', $task->id)
                ->where('user_group_type', TestUserGroup::class)
                ->where('user_group_id', $group->id)
                ->exists()
        )->toBeTrue();
    });
});
