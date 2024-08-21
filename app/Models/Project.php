<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name','user_id', 'description', 'completion','budget', 'start_date', 'end_date'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->whereHas('roles', function($query){
            $query ->where('name', 'mandor');
        });
    }
}
