<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Review extends Model
{
    use HasUuids;

    protected $fillable = ['movie_id', 'email', 'message', 'rating'];

    public function Movie() {
        return $this -> belongsTo(Movie::class);
    }
}
