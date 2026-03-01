<?php

use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Models\TaskTag;

describe('tasks()', function () {
    it('returns associated tasks', function () {
        // Arrange
        $tag = TaskTag::factory()->create();
        $task = Task::factory()->create();
        $tag->tasks()->attach($task);

        // Act
        $result = $tag->tasks;

        // Assert
        expect($result)->toHaveCount(1)
            ->first()->id->toBe($task->id);
    });

    it('returns empty collection when no tasks are associated', function () {
        // Arrange
        $tag = TaskTag::factory()->create();

        // Act
        $result = $tag->tasks;

        // Assert
        expect($result)->toBeEmpty();
    });
});
