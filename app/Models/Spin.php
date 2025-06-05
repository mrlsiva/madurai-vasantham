<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spin extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'mobile', 'branch', 'invoice_copy', 'invoice_number', 'discount', 'expires_at', 'is_redeemed'];
}
