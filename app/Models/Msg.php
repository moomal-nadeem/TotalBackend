<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Msg extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['Msg', 'Accid', 'Dated'];
}
