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

class TestTaskType extends TaskType
{
    public static function identifier(): string
    {
        return 'test';
    }

    public static function displayname(): string
    {
        return 'Test Task';
    }
}
