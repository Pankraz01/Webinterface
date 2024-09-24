<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Animation extends Model
{
    use HasFactory;

    // Erlaubte Felder für Massenzuweisungen
    protected $fillable = ['file_name', 'tags'];
}
