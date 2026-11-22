<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Kondisi / Hipotesis Diagnosis (K01 - K04)
 */
class Condition extends Model
{
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'name',
        'original_name',
        'description',
        'general_advice',
        'seeking_help_advice',
        'dataset_insights',
    ];

    protected $casts = [
        'general_advice' => 'array',
        'dataset_insights' => 'array',
    ];

    public function rules(): HasMany
    {
        return $this->hasMany(Rule::class, 'condition_code', 'code');
    }
}
