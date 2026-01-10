<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->longText('input_vocabulary')->nullable()->after('error_message');
            $table->foreignId('prompt_id')->nullable()->constrained()->nullOnDelete()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->dropForeign(['prompt_id']);
            $table->dropColumn(['input_vocabulary', 'prompt_id']);
        });
    }
};
