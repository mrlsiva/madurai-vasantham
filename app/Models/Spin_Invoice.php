<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spin_Invoice extends Model
{
    protected $table = 'spin_invoice';
    use HasFactory;

    protected $fillable = ['iyerbungalow','alanganallur','palamedu','valasai'];
}
