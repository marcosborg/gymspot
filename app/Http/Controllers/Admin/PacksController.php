<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyPackRequest;
use App\Http\Requests\StorePackRequest;
use App\Http\Requests\UpdatePackRequest;
use App\Models\Pack;
use App\Models\Spot;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class PacksController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('pack_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Pack::with(['spot'])->select(sprintf('%s.*', (new Pack)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'pack_show';
                $editGate      = 'pack_edit';
                $deleteGate    = 'pack_delete';
                $crudRoutePart = 'packs';

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
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });
            $table->addColumn('spot_name', function ($row) {
                return $row->spot ? $row->spot->name : '';
            });

            $table->editColumn('price', function ($row) {
                return $row->price ? $row->price : '';
            });
            $table->editColumn('quantity', function ($row) {
                return $row->quantity ? $row->quantity : '';
            });

            $table->editColumn('promo_title', function ($row) {
                return $row->promo_title ? $row->promo_title : '';
            });
            $table->editColumn('promo_description', function ($row) {
                return $row->promo_description ? $row->promo_description : '';
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
            $table->editColumn('vality_days', function ($row) {
                return $row->vality_days ? $row->vality_days : '';
            });

            $table->editColumn('status', function ($row) {
                return '<input type="checkbox" disabled ' . ($row->status ? 'checked' : null) . '>';
            });

            $table->rawColumns(['actions', 'placeholder', 'spot', 'image', 'status']);

            return $table->make(true);
        }

        $spots = Spot::get();

        return view('admin.packs.index', compact('spots'));
    }

    public function create()
    {
        abort_if(Gate::denies('pack_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $spots = Spot::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.packs.create', compact('spots'));
    }

    public function store(StorePackRequest $request)
    {
        $pack = Pack::create($request->all());

        if ($request->input('image', false)) {
            $pack->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $pack->id]);
        }

        return redirect()->route('admin.packs.index');
    }

    public function edit(Pack $pack)
    {
        abort_if(Gate::denies('pack_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $spots = Spot::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $pack->load('spot');

        return view('admin.packs.edit', compact('pack', 'spots'));
    }

    public function update(UpdatePackRequest $request, Pack $pack)
    {
        $pack->update($request->all());

        if ($request->input('image', false)) {
            if (! $pack->image || $request->input('image') !== $pack->image->file_name) {
                if ($pack->image) {
                    $pack->image->delete();
                }
                $pack->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
            }
        } elseif ($pack->image) {
            $pack->image->delete();
        }

        return redirect()->route('admin.packs.index');
    }

    public function show(Pack $pack)
    {
        abort_if(Gate::denies('pack_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $pack->load('spot');

        return view('admin.packs.show', compact('pack'));
    }

    public function destroy(Pack $pack)
    {
        abort_if(Gate::denies('pack_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $pack->delete();

        return back();
    }

    public function massDestroy(MassDestroyPackRequest $request)
    {
        $packs = Pack::find(request('ids'));

        foreach ($packs as $pack) {
            $pack->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('pack_create') && Gate::denies('pack_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Pack();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
