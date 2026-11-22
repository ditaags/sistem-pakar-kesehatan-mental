<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Gejala Klinis (G01 - G25)
 */
class Symptom extends Model
{
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'text',
        'category',
        'clinical_explanation',
        'is_safety_trigger',
    ];

    protected $casts = [
        'is_safety_trigger' => 'boolean',
    ];

    public function rules(): HasMany
    {
        return $this->hasMany(Rule::class, 'symptom_code', 'code');
    }
}
