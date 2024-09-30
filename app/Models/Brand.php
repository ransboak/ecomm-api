<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'logo_url', 'slug'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($brand) {
            $brand->slug = static::generateUniqueSlug($brand->name);
        });
    }

    public static function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $count = static::where('slug', 'like', "$slug%")->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }

    public function products(){
        return $this->hasMany(Product::class);
    }


}
