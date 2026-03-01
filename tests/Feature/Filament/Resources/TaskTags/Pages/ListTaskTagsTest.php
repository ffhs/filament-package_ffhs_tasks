<?php

use App\Models\User;
use Ffhs\FfhsTasks\Filament\Resources\TaskTags\Pages\ListTaskTags;
use Ffhs\FfhsTasks\Models\TaskTag;

use function Pest\Livewire\livewire;

describe('table', function () {
    test('can render page', function () {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        // Act & Assert
        livewire(ListTaskTags::class)
            ->assertSuccessful();
    });

    test('can list tags', function () {
        // Arrange
        $user = User::factory()->create();
        $tags = TaskTag::factory()->count(3)->create();

        $this->actingAs($user);

        // Act & Assert
        livewire(ListTaskTags::class)
            ->assertCanSeeTableRecords($tags);
    });

    test('has tasks count column', function () {
        // Arrange
        $user = User::factory()->create();
        TaskTag::factory()->create();

        $this->actingAs($user);

        // Act & Assert
        livewire(ListTaskTags::class)
            ->assertTableColumnExists('tasks_count');
    });

    test('can sort by display name', function () {
        // Arrange
        $user = User::factory()->create();

        $tagA = TaskTag::factory()->create(['display_name' => 'Alpha']);
        $tagB = TaskTag::factory()->create(['display_name' => 'Bravo']);

        $this->actingAs($user);

        // Act & Assert
        livewire(ListTaskTags::class)
            ->sortTable('display_name')
            ->assertCanSeeTableRecords([$tagA, $tagB], inOrder: true)
            ->sortTable('display_name', 'desc')
            ->assertCanSeeTableRecords([$tagB, $tagA], inOrder: true);
    });
});

describe('create action', function () {
    test('can create a tag', function () {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        // Act & Assert
        livewire(ListTaskTags::class)
            ->callAction('create', [
                'display_name' => 'Urgent',
            ])
            ->assertNotified();

        $this->assertDatabaseHas(TaskTag::class, [
            'display_name' => 'Urgent',
        ]);
    });

    test('validates display name is unique', function () {
        // Arrange
        $user = User::factory()->create();
        TaskTag::factory()->create(['display_name' => 'Existing']);

        $this->actingAs($user);

        // Act & Assert
        livewire(ListTaskTags::class)
            ->callAction('create', [
                'display_name' => 'Existing',
            ])
            ->assertHasActionErrors(['display_name' => 'unique']);
    });
});

describe('edit action', function () {
    test('can edit a tag', function () {
        // Arrange
        $user = User::factory()->create();
        $tag = TaskTag::factory()->create(['display_name' => 'Old Name']);

        $this->actingAs($user);

        // Act & Assert
        livewire(ListTaskTags::class)
            ->callTableAction('edit', $tag, [
                'display_name' => 'New Name',
            ])
            ->assertNotified();

        expect($tag->fresh()->display_name)->toBe('New Name');
    });
});

describe('bulk actions', function () {
    test('can delete tags', function () {
        // Arrange
        $user = User::factory()->create();
        $tags = TaskTag::factory()->count(2)->create();

        $this->actingAs($user);

        // Act & Assert
        livewire(ListTaskTags::class)
            ->callTableBulkAction('delete', $tags)
            ->assertNotified();

        foreach ($tags as $tag) {
            $this->assertSoftDeleted($tag);
        }
    });
});
