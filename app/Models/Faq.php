<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'answer',
        'is_published',
        'display_order',
        'type'
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];
}