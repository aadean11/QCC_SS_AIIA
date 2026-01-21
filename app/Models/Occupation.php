<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Occupation extends Model
{
    protected $table = 'm_occupations';
    
    // Karena kita menjadikannya relasi berdasarkan 'code', bukan 'id'
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['code', 'name'];
}