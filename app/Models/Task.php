<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['name','project_id', 'description', 'status', 'start_date', 'end_date', 'image'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

}
