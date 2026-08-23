<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $fillable = ['name', 'sort_order'];

    public function candidates() { return $this->hasMany(Candidate::class)->orderBy('full_name'); }
}
