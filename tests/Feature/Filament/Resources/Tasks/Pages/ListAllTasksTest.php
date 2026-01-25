<?php

use App\Models\User;
use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\ListAllTasks;
use Ffhs\FfhsTasks\Models\Task;

use function Pest\Livewire\livewire;

describe('filtering', function () {
    test('shows tasks with all statuses', function () {
        // Arrange
        $user = User::factory()->create();

        $inProgressTask = Task::factory()->create(['status' => TaskStatus::InProgress]);
        $completedTask = Task::factory()->completed()->create();
        $cancelledTask = Task::factory()->cancelled()->create();
        $expiredTask = Task::factory()->expired()->create();

        // Act & Assert
        $this->actingAs($user);

        livewire(ListAllTasks::class)
            ->assertCanSeeTableRecords([
                $inProgressTask,
                $completedTask,
                $cancelledTask,
                $expiredTask,
            ]);
    });
});
