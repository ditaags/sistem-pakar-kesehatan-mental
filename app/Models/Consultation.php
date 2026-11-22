<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * Model Riwayat Konsultasi Skrining Pasien
 */
class Consultation extends Model
{
    use HasUuids;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'patient_name',
        'patient_age',
        'patient_gender',
        'selected_symptoms',
        'top_condition_code',
        'top_condition_name',
        'top_cf_score',
        'top_percentage',
        'is_indication_sufficient',
        'has_safety_alert',
        'full_results',
    ];

    protected $casts = [
        'selected_symptoms' => 'array',
        'full_results' => 'array',
        'top_cf_score' => 'float',
        'top_percentage' => 'float',
        'is_indication_sufficient' => 'boolean',
        'has_safety_alert' => 'boolean',
    ];
}
