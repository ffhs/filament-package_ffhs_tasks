<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create(config('ffhs-tasks.tables.task_groups'), static function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('bulk_address')->nullable();

            $table->timestamps();
        });


        Schema::create(config('ffhs-tasks.tables.task_group_user'), static function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(config('ffhs-tasks.tables.users'))->cascadeOnDelete();
            $table->foreignId('task_group_id')->constrained(config('ffhs-tasks.tables.task_groups'))->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop(config('ffhs-tasks.tables.task_groups'));
        Schema::drop(config('ffhs-tasks.tables.task_group_user'));
    }
};
