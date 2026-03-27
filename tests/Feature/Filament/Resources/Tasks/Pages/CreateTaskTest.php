<?php

use App\Models\User;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\CreateTask;
use Ffhs\FfhsTasks\Policies\TaskPolicy;
use Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes\ExpirableTaskType;
use Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes\NonExpirableTaskType;
use Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes\TestTaskType;
use Livewire\Livewire;

describe('can_be_cancelled toggle visibility', function () {
    test('is visible when task type allows cancellation', function () {
        // Arrange
        config()->set('ffhs-tasks.types', [TestTaskType::class]);
        $user = User::factory()->create();

        TaskPolicy::fake(['create' => true]);

        // Act & Assert
        $this->actingAs($user);

        Livewire::test(CreateTask::class, ['type' => TestTaskType::identifier()])
            ->assertFormFieldIsVisible('can_be_cancelled');
    });

    test('is hidden when task type does not allow cancellation', function () {
        // Arrange
        config()->set('ffhs-tasks.types', [NonExpirableTaskType::class]);
        $user = User::factory()->create();

        TaskPolicy::fake(['create' => true]);

        // Act & Assert
        $this->actingAs($user);

        Livewire::test(CreateTask::class, ['type' => NonExpirableTaskType::identifier()])
            ->assertFormFieldIsHidden('can_be_cancelled');
    });
});

describe('expires_after_deadline toggle visibility', function () {
    test('is visible when task type allows expiration after deadline', function () {
        // Arrange
        config()->set('ffhs-tasks.types', [ExpirableTaskType::class]);

        $user = User::factory()->create();

        TaskPolicy::fake(['create' => true]);

        // Act & Assert
        $this->actingAs($user);

        Livewire::test(CreateTask::class, ['type' => ExpirableTaskType::identifier()])
            ->assertFormFieldIsVisible('expires_after_deadline');
    });

    test('is hidden when task type does not allow expiration after deadline', function () {
        // Arrange
        config()->set('ffhs-tasks.types', [NonExpirableTaskType::class]);

        $user = User::factory()->create();

        TaskPolicy::fake(['create' => true]);

        // Act & Assert
        $this->actingAs($user);

        Livewire::test(CreateTask::class, ['type' => NonExpirableTaskType::identifier()])
            ->assertFormFieldIsHidden('expires_after_deadline');
    });
});
