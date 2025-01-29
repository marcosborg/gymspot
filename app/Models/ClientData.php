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

    protected $fillable = [
        'client_id',
        'age',
        'gender',
        'primary_objective',
        'fitness_level',
        'condition',
        'condition_obs',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const PRIMARY_OBJECTIVE_RADIO = [
        'Perder peso'            => 'Perder peso',
        'Ganhar massa muscular'  => 'Ganhar massa muscular',
        'Melhorar a resistência' => 'Melhorar a resistência',
    ];

    public const CONDITION_RADIO = [
        'Problemas cardíacos'   => 'Problemas cardíacos',
        'Problemas articulares' => 'Problemas articulares',
        'Diabetes'              => 'Diabetes',
        'Hipertensão'           => 'Hipertensão',
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
