<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentHandoverItem extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'handover_id',
        'document_name',
        'copies',
        'notes',
        'selected',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'copies' => 'integer',
            'selected' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function handover(): BelongsTo
    {
        return $this->belongsTo(DocumentHandover::class, 'handover_id');
    }
}