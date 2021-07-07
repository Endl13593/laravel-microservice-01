<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Category
 * @package App\Models
 * @property int id
 * @property string title
 * @property string url
 * @property string description
 */
class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'url',
        'description'
    ];
}
