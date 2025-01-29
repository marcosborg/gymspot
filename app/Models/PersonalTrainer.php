<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PersonalTrainer extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, HasFactory;

    protected $appends = [
        'photos',
    ];

    public $table = 'personal_trainers';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const CERTIFICATE_TYPE_RADIO = [
        'tef'   => 'TEF - Técnico de Exercício Físico',
        'dt'    => 'DT - Diretor Técnico',
        'other' => 'Outro',
    ];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'facebook',
        'instagram',
        'linkedin',
        'tiktok',
        'description',
        'price',
        'certificate_type',
        'professional_certificate',
        'expiration',
        'user_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function getPhotosAttribute()
    {
        $files = $this->getMedia('photos');
        $files->each(function ($item) {
            $item->url       = $item->getUrl();
            $item->thumbnail = $item->getUrl('thumb');
            $item->preview   = $item->getUrl('preview');
        });

        return $files;
    }

    public function spots()
    {
        return $this->belongsToMany(Spot::class);
    }

    public function setExpirationAttribute($value)
    {
        $this->attributes['expiration'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
