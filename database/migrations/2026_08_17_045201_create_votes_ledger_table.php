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
        Schema::create('votes_ledger', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('block_index')->unique();
            $table->timestamp('block_timestamp');
            $table->string('previous_hash', 64);
            // Hashed payload: SHA-256 of sorted candidate_ids + hashed_token_ref
            // Does NOT store human-readable selections -- preserves ballot secrecy
            $table->string('vote_payload_hash', 64);
            $table->string('block_hash', 64)->unique();
            // Token reference stored as hash only -- cannot reverse to identify voter
            $table->string('token_hash', 64)->index();
            // Verification code shown to voter on receipt
            $table->string('receipt_code', 16)->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes_ledger');
    }
};
