<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VotingToken extends Model
{
    protected $fillable = ['code', 'student_id', 'grade', 'used', 'used_at'];

    protected $casts = [
        'used'    => 'boolean',
        'used_at' => 'datetime',
    ];

    public function student() { return $this->belongsTo(Student::class); }
}
