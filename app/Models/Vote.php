<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    protected $fillable = ['candidate_id', 'position_id', 'student_id', 'token_id'];

    public function candidate() { return $this->belongsTo(Candidate::class); }
    public function position()  { return $this->belongsTo(Position::class); }
    public function student()   { return $this->belongsTo(Student::class); }
    public function token()     { return $this->belongsTo(VotingToken::class, 'token_id'); }
}
