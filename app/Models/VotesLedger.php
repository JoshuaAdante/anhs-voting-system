<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VotesLedger extends Model
{
    protected $table = 'votes_ledger';

    protected $fillable = [
        'block_index',
        'block_timestamp',
        'previous_hash',
        'vote_payload_hash',
        'block_hash',
        'token_hash',
        'receipt_code',
    ];

    protected $casts = [
        'block_timestamp' => 'datetime',
    ];
}
