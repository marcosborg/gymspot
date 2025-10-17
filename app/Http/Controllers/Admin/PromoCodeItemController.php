<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyPromoCodeItemRequest;
use App\Http\Requests\StorePromoCodeItemRequest;
use App\Http\Requests\UpdatePromoCodeItemRequest;
use App\Models\Pack;
use App\Models\PromoCodeItem;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class PromoCodeItemController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('promo_code_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = PromoCodeItem::with(['user', 'pack'])->select(sprintf('%s.*', (new PromoCodeItem)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'promo_code_item_show';
                $editGate      = 'promo_code_item_edit';
                $deleteGate    = 'promo_code_item_delete';
                $crudRoutePart = 'promo-code-items';

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

            $table->editColumn('user.email', function ($row) {
                return $row->user ? (is_string($row->user) ? $row->user : $row->user->email) : '';
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });
            $table->editColumn('code', function ($row) {
                return $row->code ? $row->code : '';
            });
            $table->editColumn('type', function ($row) {
                return $row->type ? PromoCodeItem::TYPE_RADIO[$row->type] : '';
            });
            $table->editColumn('amount', function ($row) {
                return $row->amount ? $row->amount : '';
            });
            $table->editColumn('min_value', function ($row) {
                return $row->min_value ? $row->min_value : '';
            });

            $table->editColumn('qty', function ($row) {
                return $row->qty ? $row->qty : '';
            });
            $table->editColumn('qty_remain', function ($row) {
                return $row->qty_remain ? $row->qty_remain : '';
            });
            $table->editColumn('promo', function ($row) {
                return $row->promo ? PromoCodeItem::PROMO_RADIO[$row->promo] : '';
            });
            $table->addColumn('pack_name', function ($row) {
                return $row->pack ? $row->pack->name : '';
            });

            $table->editColumn('status', function ($row) {
                return '<input type="checkbox" disabled ' . ($row->status ? 'checked' : null) . '>';
            });

            $table->rawColumns(['actions', 'placeholder', 'user', 'pack', 'status']);

            return $table->make(true);
        }

        $users = User::get();
        $packs = Pack::get();

        return view('admin.promoCodeItems.index', compact('users', 'packs'));
    }

    public function create()
    {
        abort_if(Gate::denies('promo_code_item_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $packs = Pack::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.promoCodeItems.create', compact('packs', 'users'));
    }

    public function store(StorePromoCodeItemRequest $request)
    {
        $promoCodeItem = PromoCodeItem::create($request->all());

        return redirect()->route('admin.promo-code-items.index');
    }

    public function edit(PromoCodeItem $promoCodeItem)
    {
        abort_if(Gate::denies('promo_code_item_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $packs = Pack::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $promoCodeItem->load('user', 'pack');

        return view('admin.promoCodeItems.edit', compact('packs', 'promoCodeItem', 'users'));
    }

    public function update(UpdatePromoCodeItemRequest $request, PromoCodeItem $promoCodeItem)
    {
        $promoCodeItem->update($request->all());

        return redirect()->route('admin.promo-code-items.index');
    }

    public function show(PromoCodeItem $promoCodeItem)
    {
        abort_if(Gate::denies('promo_code_item_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $promoCodeItem->load('user', 'pack');

        return view('admin.promoCodeItems.show', compact('promoCodeItem'));
    }

    public function destroy(PromoCodeItem $promoCodeItem)
    {
        abort_if(Gate::denies('promo_code_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $promoCodeItem->delete();

        return back();
    }

    public function massDestroy(MassDestroyPromoCodeItemRequest $request)
    {
        $promoCodeItems = PromoCodeItem::find(request('ids'));

        foreach ($promoCodeItems as $promoCodeItem) {
            $promoCodeItem->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
