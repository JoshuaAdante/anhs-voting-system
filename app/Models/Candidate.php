<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $fillable = [
        'full_name', 'position_id', 'partylist', 'grade', 'section', 'photo_url',
    ];

    public function position() { return $this->belongsTo(Position::class); }
    public function votes()    { return $this->hasMany(Vote::class); }
}
