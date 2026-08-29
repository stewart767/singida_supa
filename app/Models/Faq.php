<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $table = 'cms_faqs';

    protected $fillable = ['question', 'answer', 'category', 'order'];

    protected function casts(): array
    {
        return ['order' => 'integer'];
    }
}
