<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActiveCall extends Model
{
    protected $fillable = ['agent_id', 'conference_id'];
    public $timestamps  = false;
}
