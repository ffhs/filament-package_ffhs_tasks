<?php

use App\Models\User;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\ListTasks;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Ffhs\FfhsTasks\TaskType\TaskType;
use Ffhs\FfhsTasks\TaskType\Types\ApprovalTaskType;

use function Pest\Livewire\livewire;

describe('single task type', function () {
    test('has action with direct url to create page', function () {
        // Arrange
        $user = User::factory()->create();

        config(['ffhs-tasks.types' => [ApprovalTaskType::class]]);

        // Act & Assert
        $this->actingAs($user);

        livewire(ListTasks::class)
            ->assertActionExists('create')
            ->assertActionHasUrl(
                'create',
                TaskResource::getUrl('create', ['type' => ApprovalTaskType::identifier()])
            );
    });
});

describe('multiple task types', function () {
    test('shows modal with task type options when multiple types exist', function () {
        // Arrange
        $user = User::factory()->create();

        config(['ffhs-tasks.types' => [
            ApprovalTaskType::class,
            TestTaskType::class,
        ]]);

        // Act & Assert
        $this->actingAs($user);

        livewire(ListTasks::class)
            ->assertActionExists('create')
            ->mountAction('create')
            ->assertActionMounted('create')
            ->assertMountedActionModalSee([
                ApprovalTaskType::displayname(),
                TestTaskType::displayname()
            ]);
    });

    test('redirects to create page with selected type', function () {
        // Arrange
        $user = User::factory()->create();

        config(['ffhs-tasks.types' => [
            ApprovalTaskType::class,
            TestTaskType::class,
        ]]);

        // Act & Assert
        $this->actingAs($user);

        livewire(ListTasks::class)
            ->callAction('create', ['type' => ApprovalTaskType::identifier()])
            ->assertRedirect(TaskResource::getUrl('create', ['type' => ApprovalTaskType::identifier()]));
    });
});

describe('canBeCreatedViaUi filtering', function () {
    test('filters out task types that cannot be created via UI', function () {
        // Arrange
        $user = User::factory()->create();

        config(['ffhs-tasks.types' => [
            TestTaskType::class,
            NonCreatableTaskType::class,
        ]]);

        // Act & Assert
        $this->actingAs($user);

        livewire(ListTasks::class)
            ->assertActionExists('create')
            ->assertActionHasUrl(
                'create',
                TaskResource::getUrl('create', ['type' => TestTaskType::identifier()])
            );
    });

    test('shows only creatable task types in modal', function () {
        // Arrange
        $user = User::factory()->create();

        config(['ffhs-tasks.types' => [
            TestTaskType::class,
            TestTaskType2::class,
            NonCreatableTaskType::class,
        ]]);

        // Act & Assert
        $this->actingAs($user);

        livewire(ListTasks::class)
            ->mountAction('create')
            ->assertMountedActionModalSee([
                TestTaskType::displayname(),
                TestTaskType2::displayname(),
            ])
            ->assertMountedActionModalDontSee([
                NonCreatableTaskType::displayname(),
            ]);
    });
});

class TestTaskType extends TaskType
{
    public static function identifier(): string
    {
        return 'test';
    }

    public static function displayname(): string
    {
        return 'Test 1';
    }
}

class TestTaskType2 extends TaskType
{
    public static function identifier(): string
    {
        return 'test2';
    }

    public static function displayname(): string
    {
        return 'Test 2';
    }
}

class NonCreatableTaskType extends TaskType
{
    public static function identifier(): string
    {
        return 'non-creatable';
    }

    public static function displayname(): string
    {
        return 'Non Creatable Task';
    }

    public function canBeCreatedViaUi(): bool
    {
        return false;
    }
}
