<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
  use HasFactory;

  protected $casts = [
    "blog_category_id" => "array",
  ];

  public function blogCategories()
  {
    return $this->hasMany(BlogCategory::class, 'id');
  }
}
