<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leger extends Model
{
    use HasFactory;
    protected $table = 'Leger';
    public $timestamps = false;
    protected $fillable = ['Dated','CompId','UserId','Accid','VNo','Type','RefNo','Debit','Credit','Description','Remarks','Timed','MvNo','Bal'];
}
