<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Movie extends Model
{
    use HasUuids;

    public function reviews() {
        return $this -> hasMany(Review::class);
    }
}
