<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    use HasFactory;

    protected $table = 'cms_downloads';

    protected $fillable = ['title', 'category', 'file_path', 'file_size_bytes'];
}
