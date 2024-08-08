<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyPersonalTrainerRequest;
use App\Http\Requests\StorePersonalTrainerRequest;
use App\Http\Requests\UpdatePersonalTrainerRequest;
use App\Models\PersonalTrainer;
use App\Models\Spot;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use App\Models\User;

class PersonalTrainerController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('personal_trainer_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = PersonalTrainer::with(['spots'])->select(sprintf('%s.*', (new PersonalTrainer)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'personal_trainer_show';
                $editGate      = 'personal_trainer_edit';
                $deleteGate    = 'personal_trainer_delete';
                $crudRoutePart = 'personal-trainers';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->editColumn('email', function ($row) {
                return $row->email ? $row->email : '';
            });
            $table->editColumn('phone', function ($row) {
                return $row->phone ? $row->phone : '';
            });
            $table->editColumn('facebook', function ($row) {
                return $row->facebook ? $row->facebook : '';
            });
            $table->editColumn('instagram', function ($row) {
                return $row->instagram ? $row->instagram : '';
            });
            $table->editColumn('linkedin', function ($row) {
                return $row->linkedin ? $row->linkedin : '';
            });
            $table->editColumn('tiktok', function ($row) {
                return $row->tiktok ? $row->tiktok : '';
            });
            $table->editColumn('photos', function ($row) {
                if (! $row->photos) {
                    return '';
                }
                $links = [];
                foreach ($row->photos as $media) {
                    $links[] = '<a href="' . $media->getUrl() . '" target="_blank"><img src="' . $media->getUrl('thumb') . '" width="50px" height="50px"></a>';
                }

                return implode(' ', $links);
            });
            $table->editColumn('spots', function ($row) {
                $labels = [];
                foreach ($row->spots as $spot) {
                    $labels[] = sprintf('<span class="label label-info label-many">%s</span>', $spot->name);
                }

                return implode(' ', $labels);
            });

            $table->editColumn('price', function ($row) {
                return $row->price ? $row->price : '';
            });

            $table->editColumn('certificate_type', function ($row) {
                return $row->certificate_type ? PersonalTrainer::CERTIFICATE_TYPE_RADIO[$row->certificate_type] : '';
            });
            $table->editColumn('professional_certificate', function ($row) {
                return $row->professional_certificate ? $row->professional_certificate : '';
            });

            $table->addColumn('user_name', function ($row) {
                return $row->user ? $row->user->name : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'photos', 'spots', 'user']);

            return $table->make(true);
        }

        return view('admin.personalTrainers.index');
    }

    public function create()
    {
        abort_if(Gate::denies('personal_trainer_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $spots = Spot::pluck('name', 'id');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.personalTrainers.create', compact('spots', 'users'));
    }

    public function store(StorePersonalTrainerRequest $request)
    {
        $personalTrainer = PersonalTrainer::create($request->all());
        $personalTrainer->spots()->sync($request->input('spots', []));
        foreach ($request->input('photos', []) as $file) {
            $personalTrainer->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('photos');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $personalTrainer->id]);
        }

        return redirect()->route('admin.personal-trainers.index');
    }

    public function edit(PersonalTrainer $personalTrainer)
    {
        abort_if(Gate::denies('personal_trainer_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $spots = Spot::pluck('name', 'id');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $personalTrainer->load('spots', 'user');

        return view('admin.personalTrainers.edit', compact('personalTrainer', 'spots', 'users'));
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

        return redirect()->route('admin.personal-trainers.index');
    }

    public function show(PersonalTrainer $personalTrainer)
    {
        abort_if(Gate::denies('personal_trainer_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $personalTrainer->load('spots', 'user');

        return view('admin.personalTrainers.show', compact('personalTrainer'));
    }

    public function destroy(PersonalTrainer $personalTrainer)
    {
        abort_if(Gate::denies('personal_trainer_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $personalTrainer->delete();

        return back();
    }

    public function massDestroy(MassDestroyPersonalTrainerRequest $request)
    {
        $personalTrainers = PersonalTrainer::find(request('ids'));

        foreach ($personalTrainers as $personalTrainer) {
            $personalTrainer->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('personal_trainer_create') && Gate::denies('personal_trainer_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new PersonalTrainer();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
