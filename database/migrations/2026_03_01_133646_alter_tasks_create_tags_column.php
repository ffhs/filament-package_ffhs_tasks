<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        $taskTable = config('ffhs-tasks.tables.tasks');
        $tagTable = config('ffhs-tasks.tables.task_tags');
        $pivotTable = config('ffhs-tasks.tables.task_tag');

        Schema::create($tagTable, function (Blueprint $table) {
            $table->id();
            $table->text('display_name')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create($pivotTable, function (Blueprint $table) use ($taskTable, $tagTable) {
            $table->foreignId('task_id')->constrained($taskTable)->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained($tagTable)->cascadeOnDelete();
        });
    }
};
