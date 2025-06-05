<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftTimeWithUsers extends Model
{
    protected $table = 'shifttimewithusers';

    use HasFactory;

    protected $guarded = [];
}
