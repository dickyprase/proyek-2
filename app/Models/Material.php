<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'project_id', 'description', 'quantity', 'unit', 'is_delivered'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
