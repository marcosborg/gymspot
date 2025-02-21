<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientData extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'client_datas';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const GENDER_RADIO = [
        'Masculino' => 'Masculino',
        'Feminino'  => 'Feminino',
        'Outro'     => 'Outro',
    ];

    public const FITNESS_LEVEL_RADIO = [
        'Iniciante'     => 'Iniciante',
        'Intermediário' => 'Intermediário',
        'Avançado'      => 'Avançado',
    ];

    public const TRAINING_TIME_RADIO = [
        '30'  => '30 minutos',
        '60'  => '60 minutos',
        '90'  => '90 minutos',
        '120' => '120 minutos',
    ];

    public const PRIMARY_OBJECTIVE_RADIO = [
        'Perder peso'            => 'Perder peso',
        'Ganhar massa muscular'  => 'Ganhar massa muscular',
        'Melhorar a resistência' => 'Melhorar a resistência',
        'Outro'                 => 'Outro',
    ];

    public const CONDITION_RADIO = [
        'Problemas cardíacos'   => 'Problemas cardíacos',
        'Problemas articulares' => 'problemas articulares',
        'Diabetes'              => 'Diabetes',
        'Hipertensão'           => 'Hipertensão',
        'Nada a assinalar'      => 'Nada a assinalar',
    ];

    public const PRIMARY_TYPE_RADIO = [
        'Treino Básico'  => 'Treino Básico',
        'Superiores'     => 'Superiores',
        'Inferiores'     => 'Inferiores',
        'Full Body'      => 'Full Body',
        'Glúteos e Abs'  => 'Glúteos e Abs',
        'Apenas Glúteos' => 'Apenas Glúteos',
    ];

    protected $fillable = [
        'client_id',
        'age',
        'gender',
        'primary_objective',
        'fitness_level',
        'primary_type',
        'training_time',
        'training_frequency',
        'condition',
        'condition_obs',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
