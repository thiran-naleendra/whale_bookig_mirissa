<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthPrice extends Model
{
    protected $table = 'month_prices';

    protected $fillable = ['year','month','adult_price','child_price','is_enabled'];

}
