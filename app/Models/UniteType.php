<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UniteType extends Model
{
    use HasFactory;
    protected $table = 'unite_type';
    protected $guarded = ['id'];
}
