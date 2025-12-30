<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisabledDate extends Model
{
    protected $table = 'disabled_dates';
    protected $fillable = ['date', 'note'];
}
