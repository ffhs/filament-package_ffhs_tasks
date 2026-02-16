<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('first_user_groups', function (Blueprint $table) {
            $table->id();
            $table->string('display_name');
            $table->timestamps();
        });

        Schema::create('second_user_groups', function (Blueprint $table) {
            $table->id();
            $table->string('display_name');
            $table->timestamps();
        });
    }
};
