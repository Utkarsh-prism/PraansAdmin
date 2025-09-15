<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasFactory as HasFactoryAlias;

class Link extends Model
{
    use HasFactory;

    protected $fillable = [
        'title','color','description','url','image',
    ];
}
