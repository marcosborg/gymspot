<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyLocationRequest;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\Location;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class LocationController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('location_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Location::query()->select(sprintf('%s.*', (new Location)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'location_show';
                $editGate      = 'location_edit';
                $deleteGate    = 'location_delete';
                $crudRoutePart = 'locations';

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
            $table->editColumn('soon', function ($row) {
                return '<input type="checkbox" disabled ' . ($row->soon ? 'checked' : null) . '>';
            });

            $table->rawColumns(['actions', 'placeholder', 'image', 'soon']);

            return $table->make(true);
        }

        return view('admin.locations.index');
    }

    public function create()
    {
        abort_if(Gate::denies('location_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.locations.create');
    }

    public function store(StoreLocationRequest $request)
    {
        $location = Location::create($request->all());

        if ($request->input('image', false)) {
            $location->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $location->id]);
        }

        return redirect()->route('admin.locations.index');
    }

    public function edit(Location $location)
    {
        abort_if(Gate::denies('location_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.locations.edit', compact('location'));
    }

    public function update(UpdateLocationRequest $request, Location $location)
    {
        $location->update($request->all());

        if ($request->input('image', false)) {
            if (! $location->image || $request->input('image') !== $location->image->file_name) {
                if ($location->image) {
                    $location->image->delete();
                }
                $location->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
            }
        } elseif ($location->image) {
            $location->image->delete();
        }

        return redirect()->route('admin.locations.index');
    }

    public function show(Location $location)
    {
        abort_if(Gate::denies('location_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.locations.show', compact('location'));
    }

    public function destroy(Location $location)
    {
        abort_if(Gate::denies('location_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $location->delete();

        return back();
    }

    public function massDestroy(MassDestroyLocationRequest $request)
    {
        $locations = Location::find(request('ids'));

        foreach ($locations as $location) {
            $location->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('location_create') && Gate::denies('location_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Location();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
