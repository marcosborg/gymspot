<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StorePersonalTrainerRequest;
use App\Http\Requests\UpdatePersonalTrainerRequest;
use App\Http\Resources\Admin\PersonalTrainerResource;
use App\Models\PersonalTrainer;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PersonalTrainerApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {

        if(request()->query('limit')){
            $limit = request()->query('limit');
            $personal_trainers = new PersonalTrainerResource(PersonalTrainer::with(['spots', 'user'])->inRandomOrder()->limit($limit)->get());
        } else {
            $personal_trainers = new PersonalTrainerResource(PersonalTrainer::with(['spots', 'user'])->inRandomOrder()->get());
        }

        

        return $personal_trainers;
    }

    public function store(StorePersonalTrainerRequest $request)
    {
        $personalTrainer = PersonalTrainer::create($request->all());
        $personalTrainer->spots()->sync($request->input('spots', []));
        foreach ($request->input('photos', []) as $file) {
            $personalTrainer->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('photos');
        }

        return (new PersonalTrainerResource($personalTrainer))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(PersonalTrainer $personalTrainer)
    {
        //abort_if(Gate::denies('personal_trainer_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PersonalTrainerResource($personalTrainer->load(['spots', 'user']));
    }

    public function update(UpdatePersonalTrainerRequest $request, PersonalTrainer $personalTrainer)
    {
        $personalTrainer->update($request->all());
        $personalTrainer->spots()->sync($request->input('spots', []));
        if (count($personalTrainer->photos) > 0) {
            foreach ($personalTrainer->photos as $media) {
                if (! in_array($media->file_name, $request->input('photos', []))) {
                    $media->delete();
                }
            }
        }
        $media = $personalTrainer->photos->pluck('file_name')->toArray();
        foreach ($request->input('photos', []) as $file) {
            if (count($media) === 0 || ! in_array($file, $media)) {
                $personalTrainer->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('photos');
            }
        }

        return (new PersonalTrainerResource($personalTrainer))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(PersonalTrainer $personalTrainer)
    {
        abort_if(Gate::denies('personal_trainer_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $personalTrainer->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
