<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        $taskTable = config('ffhs-tasks.tables.tasks');
        $taskAssignablesTable = config('ffhs-tasks.tables.task_assignables');
        $taskWatchablesTable = config('ffhs-tasks.tables.task_watchables');

        Schema::create($taskTable, static function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('description');
            $table->string('type');
            $table->string('status');

            $table->nullableMorphs('creator');

            $table->boolean('can_be_cancelled')->default(false);

            $table->dateTime('cancelled_at')->nullable();
            $table->dateTime('completed_at')->nullable();

            $table->dateTime('starts_at')->nullable();
            $table->dateTime('deadline_at')->nullable();

            $table->json('extra')->nullable();
            $table->json('data')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create($taskAssignablesTable, static function (Blueprint $table) use ($taskTable) {
            $table->id();
            $table->foreignId('task_id')->constrained($taskTable)->cascadeOnDelete();
            $table->morphs('assignable');
            $table->timestamps();
        });

        Schema::create($taskWatchablesTable, static function (Blueprint $table) use ($taskTable) {
            $table->id();
            $table->foreignId('task_id')->constrained($taskTable)->cascadeOnDelete();
            $table->morphs('assignable');
            $table->timestamps();
        });
    }
};
