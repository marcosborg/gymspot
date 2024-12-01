<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroySpotRequest;
use App\Http\Requests\StoreSpotRequest;
use App\Http\Requests\UpdateSpotRequest;
use App\Models\Country;
use App\Models\Item;
use App\Models\Location;
use App\Models\Spot;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class SpotController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('spot_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Spot::with(['location', 'country', 'items'])->select(sprintf('%s.*', (new Spot)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'spot_show';
                $editGate      = 'spot_edit';
                $deleteGate    = 'spot_delete';
                $crudRoutePart = 'spots';

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
            $table->editColumn('address', function ($row) {
                return $row->address ? $row->address : '';
            });
            $table->editColumn('zip', function ($row) {
                return $row->zip ? $row->zip : '';
            });
            $table->addColumn('location_title', function ($row) {
                return $row->location ? $row->location->title : '';
            });

            $table->addColumn('country_name', function ($row) {
                return $row->country ? $row->country->name : '';
            });

            $table->editColumn('price', function ($row) {
                return $row->price ? $row->price : '';
            });
            $table->editColumn('sale', function ($row) {
                return $row->sale ? $row->sale : '';
            });
            $table->editColumn('capacity', function ($row) {
                return $row->capacity ? $row->capacity : '';
            });
            $table->editColumn('email', function ($row) {
                return $row->email ? $row->email : '';
            });
            $table->editColumn('phone', function ($row) {
                return $row->phone ? $row->phone : '';
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
            $table->editColumn('item', function ($row) {
                $labels = [];
                foreach ($row->items as $item) {
                    $labels[] = sprintf('<span class="label label-info label-many">%s</span>', $item->name);
                }

                return implode(' ', $labels);
            });
            $table->editColumn('soon', function ($row) {
                return '<input type="checkbox" disabled ' . ($row->soon ? 'checked' : null) . '>';
            });

            $table->rawColumns(['actions', 'placeholder', 'location', 'country', 'photos', 'item', 'soon']);

            return $table->make(true);
        }

        $locations = Location::get();
        $countries = Country::get();
        $items     = Item::get();

        return view('admin.spots.index', compact('locations', 'countries', 'items'));
    }

    public function create()
    {
        abort_if(Gate::denies('spot_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $locations = Location::pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');

        $countries = Country::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $items = Item::pluck('name', 'id');

        return view('admin.spots.create', compact('countries', 'items', 'locations'));
    }

    public function store(StoreSpotRequest $request)
    {
        $spot = Spot::create($request->all());
        $spot->items()->sync($request->input('items', []));
        foreach ($request->input('photos', []) as $file) {
            $spot->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('photos');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $spot->id]);
        }

        return redirect()->route('admin.spots.index');
    }

    public function edit(Spot $spot)
    {
        abort_if(Gate::denies('spot_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $locations = Location::pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');

        $countries = Country::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $items = Item::pluck('name', 'id');

        $spot->load('location', 'country', 'items');

        return view('admin.spots.edit', compact('countries', 'items', 'locations', 'spot'));
    }

    public function update(UpdateSpotRequest $request, Spot $spot)
    {
        $spot->update($request->all());
        $spot->items()->sync($request->input('items', []));
        if (count($spot->photos) > 0) {
            foreach ($spot->photos as $media) {
                if (! in_array($media->file_name, $request->input('photos', []))) {
                    $media->delete();
                }
            }
        }
        $media = $spot->photos->pluck('file_name')->toArray();
        foreach ($request->input('photos', []) as $file) {
            if (count($media) === 0 || ! in_array($file, $media)) {
                $spot->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('photos');
            }
        }

        return redirect()->route('admin.spots.index');
    }

    public function show(Spot $spot)
    {
        abort_if(Gate::denies('spot_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $spot->load('location', 'country', 'items');

        return view('admin.spots.show', compact('spot'));
    }

    public function destroy(Spot $spot)
    {
        abort_if(Gate::denies('spot_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $spot->delete();

        return back();
    }

    public function massDestroy(MassDestroySpotRequest $request)
    {
        $spots = Spot::find(request('ids'));

        foreach ($spots as $spot) {
            $spot->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('spot_create') && Gate::denies('spot_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Spot();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
