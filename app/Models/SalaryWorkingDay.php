<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryWorkingDay extends Model
{
    protected $table = 'salary_working_days';

    protected $guarded = [];

    protected $casts = [
        'month' => 'date',
    ];
}
