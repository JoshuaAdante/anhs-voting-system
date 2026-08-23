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
        Schema::table('votes', function (Blueprint $table) {
            $table->foreignId('token_id')->nullable()->after('student_id')
                  ->constrained('voting_tokens')->nullOnDelete();
            // Also track abstains: if candidate_id is null, voter abstained
            $table->foreignId('position_id')->nullable()->after('candidate_id')
                  ->constrained('positions')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            $table->dropForeign(['token_id']);
            $table->dropColumn('token_id');
            $table->dropForeign(['position_id']);
            $table->dropColumn('position_id');
        });
    }
};
