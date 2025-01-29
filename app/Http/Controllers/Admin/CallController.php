<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyCallRequest;
use App\Http\Requests\StoreCallRequest;
use App\Http\Requests\UpdateCallRequest;
use App\Models\Call;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class CallController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('call_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Call::query()->select(sprintf('%s.*', (new Call)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'call_show';
                $editGate      = 'call_edit';
                $deleteGate    = 'call_delete';
                $crudRoutePart = 'calls';

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
            $table->editColumn('title', function ($row) {
                return $row->title ? $row->title : '';
            });
            $table->editColumn('subtitle', function ($row) {
                return $row->subtitle ? $row->subtitle : '';
            });
            $table->editColumn('button', function ($row) {
                return $row->button ? $row->button : '';
            });
            $table->editColumn('link', function ($row) {
                return $row->link ? $row->link : '';
            });
            $table->editColumn('image', function ($row) {
                if ($photo = $row->image) {
                    return sprintf(
                        '<a href="%s" target="_blank"><img src="%s" width="50px" height="50px"></a>',
                        $photo->url,
                        $photo->thumbnail
                    );
                }

                return '';
            });

            $table->rawColumns(['actions', 'placeholder', 'image']);

            return $table->make(true);
        }

        return view('admin.calls.index');
    }

    public function create()
    {
        abort_if(Gate::denies('call_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.calls.create');
    }

    public function store(StoreCallRequest $request)
    {
        $call = Call::create($request->all());

        if ($request->input('image', false)) {
            $call->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $call->id]);
        }

        return redirect()->route('admin.calls.index');
    }

    public function edit(Call $call)
    {
        abort_if(Gate::denies('call_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.calls.edit', compact('call'));
    }

    public function update(UpdateCallRequest $request, Call $call)
    {
        $call->update($request->all());

        if ($request->input('image', false)) {
            if (! $call->image || $request->input('image') !== $call->image->file_name) {
                if ($call->image) {
                    $call->image->delete();
                }
                $call->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
            }
        } elseif ($call->image) {
            $call->image->delete();
        }

        return redirect()->route('admin.calls.index');
    }

    public function show(Call $call)
    {
        abort_if(Gate::denies('call_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.calls.show', compact('call'));
    }

    public function destroy(Call $call)
    {
        abort_if(Gate::denies('call_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $call->delete();

        return back();
    }

    public function massDestroy(MassDestroyCallRequest $request)
    {
        $calls = Call::find(request('ids'));

        foreach ($calls as $call) {
            $call->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('call_create') && Gate::denies('call_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Call();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
