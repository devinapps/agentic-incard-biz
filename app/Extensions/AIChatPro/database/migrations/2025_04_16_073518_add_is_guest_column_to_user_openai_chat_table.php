<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_openai_chat', function (Blueprint $table) {
            $table->boolean('is_guest')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('user_openai_chat', function (Blueprint $table) {
            $table->dropColumn('is_guest');
        });
    }
};
