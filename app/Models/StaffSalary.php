<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffSalary extends Model
{
    protected $table = 'staff_salaries';

    protected $guarded = [];

    protected $casts = [
        'effective_from' => 'date',
        'monthly_salary' => 'decimal:2',
    ];
}
