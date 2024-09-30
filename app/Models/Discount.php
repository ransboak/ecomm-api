<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'discount', 'uses', 'max_uses', 'expires_at'];

    public function isValid()
    {
        return $this->uses < $this->max_uses && now()->lessThanOrEqualTo($this->expires_at);
    }
}
