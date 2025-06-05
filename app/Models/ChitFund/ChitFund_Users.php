<?php

namespace App\Models\ChitFund;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChitFund_Users extends Model
{
    protected $table = 'chitfund_users';

    use HasFactory;

    protected $guarded = [];
}
