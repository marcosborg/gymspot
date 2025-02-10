<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyPackPurchaseRequest;
use App\Http\Requests\StorePackPurchaseRequest;
use App\Http\Requests\UpdatePackPurchaseRequest;
use App\Models\Pack;
use App\Models\PackPurchase;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class PackPurchaseController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('pack_purchase_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = PackPurchase::with(['user', 'pack'])->select(sprintf('%s.*', (new PackPurchase)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'pack_purchase_show';
                $editGate      = 'pack_purchase_edit';
                $deleteGate    = 'pack_purchase_delete';
                $crudRoutePart = 'pack-purchases';

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
            $table->addColumn('user_name', function ($row) {
                return $row->user ? $row->user->name : '';
            });

            $table->addColumn('pack_name', function ($row) {
                return $row->pack ? $row->pack->name : '';
            });

            $table->editColumn('quantity', function ($row) {
                return $row->quantity ? $row->quantity : '';
            });
            $table->editColumn('available', function ($row) {
                return $row->available ? $row->available : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'user', 'pack']);

            return $table->make(true);
        }

        $users = User::get();
        $packs = Pack::get();

        return view('admin.packPurchases.index', compact('users', 'packs'));
    }

    public function create()
    {
        abort_if(Gate::denies('pack_purchase_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $packs = Pack::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.packPurchases.create', compact('packs', 'users'));
    }

    public function store(StorePackPurchaseRequest $request)
    {
        $packPurchase = PackPurchase::create($request->all());

        return redirect()->route('admin.pack-purchases.index');
    }

    public function edit(PackPurchase $packPurchase)
    {
        abort_if(Gate::denies('pack_purchase_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $packs = Pack::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $packPurchase->load('user', 'pack');

        return view('admin.packPurchases.edit', compact('packPurchase', 'packs', 'users'));
    }

    public function update(UpdatePackPurchaseRequest $request, PackPurchase $packPurchase)
    {
        $packPurchase->update($request->all());

        return redirect()->route('admin.pack-purchases.index');
    }

    public function show(PackPurchase $packPurchase)
    {
        abort_if(Gate::denies('pack_purchase_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $packPurchase->load('user', 'pack');

        return view('admin.packPurchases.show', compact('packPurchase'));
    }

    public function destroy(PackPurchase $packPurchase)
    {
        abort_if(Gate::denies('pack_purchase_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $packPurchase->delete();

        return back();
    }

    public function massDestroy(MassDestroyPackPurchaseRequest $request)
    {
        $packPurchases = PackPurchase::find(request('ids'));

        foreach ($packPurchases as $packPurchase) {
            $packPurchase->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
