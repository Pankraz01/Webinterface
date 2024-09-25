<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tag;

class Animation extends Model
{
    use HasFactory;

    // Erlaubte Felder für Massenzuweisungen
    protected $fillable = ['file_name'];

    // Die Tags-Beziehung: Eine Animation kann viele Tags haben
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}

