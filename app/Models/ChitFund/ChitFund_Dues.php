<?php

namespace App\Models\ChitFund;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChitFund_Dues extends Model
{
    protected $table = 'chitfund_dues';

    use HasFactory;

    protected $guarded = [];
}
