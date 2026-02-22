<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        $tableName = config('ffhs-tasks.tables.task_notification_log', 'ffhs_task_notification_log');

        Schema::create($tableName, static function (Blueprint $table) {
            $taskTable = config('ffhs-tasks.tables.tasks');

            $table->id();
            $table->foreignId('task_id')->constrained($taskTable)->cascadeOnDelete();
            $table->string('notification_type');
            $table->string('notification_key');
            $table->timestamps();

            $table->unique(['task_id', 'notification_type', 'notification_key']);
        });
    }
};
