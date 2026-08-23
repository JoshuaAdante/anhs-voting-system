<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'lrn', 'last_name', 'first_name', 'given_name',
        'grade', 'section', 'sex', 'has_voted', 'voted_at',
    ];

    protected $casts = [
        'has_voted' => 'boolean',
        'voted_at'  => 'datetime',
    ];

    public function votingToken() { return $this->hasOne(VotingToken::class); }
    public function votes()       { return $this->hasMany(Vote::class); }
}
