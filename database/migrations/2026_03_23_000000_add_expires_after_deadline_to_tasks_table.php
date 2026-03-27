<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table(config('ffhs-tasks.tables.tasks'), static function (Blueprint $table) {
            $table->boolean('expires_after_deadline')->default(false)->after('can_be_cancelled');
        });
    }

    public function down(): void
    {
        Schema::table(config('ffhs-tasks.tables.tasks'), static function (Blueprint $table) {
            $table->dropColumn('expires_after_deadline');
        });
    }
};
