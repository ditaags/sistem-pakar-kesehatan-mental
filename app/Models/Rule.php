<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Aturan Relasi Kondisi - Gejala beserta Bobot CF Pakar
 */
class Rule extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'condition_code',
        'symptom_code',
        'cf_pakar',
        'source_literature',
    ];

    protected $casts = [
        'cf_pakar' => 'float',
    ];

    public function condition(): BelongsTo
    {
        return $this->belongsTo(Condition::class, 'condition_code', 'code');
    }

    public function symptom(): BelongsTo
    {
        return $this->belongsTo(Symptom::class, 'symptom_code', 'code');
    }
}
