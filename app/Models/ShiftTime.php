<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftTime extends Model
{
    protected $table = 'shifttime';

    use HasFactory;

    protected $guarded = [];
}
