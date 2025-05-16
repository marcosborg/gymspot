<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyPromoCodeUsageRequest;
use App\Http\Requests\StorePromoCodeUsageRequest;
use App\Http\Requests\UpdatePromoCodeUsageRequest;
use App\Models\Client;
use App\Models\Payment;
use App\Models\PromoCodeItem;
use App\Models\PromoCodeUsage;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class PromoCodeUsageController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('promo_code_usage_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = PromoCodeUsage::with(['promo_code_item', 'client', 'payment'])->select(sprintf('%s.*', (new PromoCodeUsage)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'promo_code_usage_show';
                $editGate      = 'promo_code_usage_edit';
                $deleteGate    = 'promo_code_usage_delete';
                $crudRoutePart = 'promo-code-usages';

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
            $table->addColumn('promo_code_item_name', function ($row) {
                return $row->promo_code_item ? $row->promo_code_item->name : '';
            });

            $table->editColumn('promo_code_item.code', function ($row) {
                return $row->promo_code_item ? (is_string($row->promo_code_item) ? $row->promo_code_item : $row->promo_code_item->code) : '';
            });
            $table->addColumn('client_name', function ($row) {
                return $row->client ? $row->client->name : '';
            });

            $table->addColumn('payment_paid', function ($row) {
                return $row->payment ? $row->payment->paid : '';
            });

            $table->editColumn('payment.method', function ($row) {
                return $row->payment ? (is_string($row->payment) ? $row->payment : $row->payment->method) : '';
            });
            $table->editColumn('payment.cart', function ($row) {
                return $row->payment ? (is_string($row->payment) ? $row->payment : $row->payment->cart) : '';
            });
            $table->editColumn('payment.amount', function ($row) {
                return $row->payment ? (is_string($row->payment) ? $row->payment : $row->payment->amount) : '';
            });
            $table->editColumn('value', function ($row) {
                return $row->value ? $row->value : '';
            });
            $table->editColumn('used', function ($row) {
                return '<input type="checkbox" disabled ' . ($row->used ? 'checked' : null) . '>';
            });

            $table->rawColumns(['actions', 'placeholder', 'promo_code_item', 'client', 'payment', 'used']);

            return $table->make(true);
        }

        $promo_code_items = PromoCodeItem::get();
        $clients          = Client::get();
        $payments         = Payment::get();

        return view('admin.promoCodeUsages.index', compact('promo_code_items', 'clients', 'payments'));
    }

    public function create()
    {
        abort_if(Gate::denies('promo_code_usage_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $promo_code_items = PromoCodeItem::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $clients = Client::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $payments = Payment::pluck('paid', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.promoCodeUsages.create', compact('clients', 'payments', 'promo_code_items'));
    }

    public function store(StorePromoCodeUsageRequest $request)
    {
        $promoCodeUsage = PromoCodeUsage::create($request->all());

        return redirect()->route('admin.promo-code-usages.index');
    }

    public function edit(PromoCodeUsage $promoCodeUsage)
    {
        abort_if(Gate::denies('promo_code_usage_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $promo_code_items = PromoCodeItem::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $clients = Client::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $payments = Payment::pluck('paid', 'id')->prepend(trans('global.pleaseSelect'), '');

        $promoCodeUsage->load('promo_code_item', 'client', 'payment');

        return view('admin.promoCodeUsages.edit', compact('clients', 'payments', 'promoCodeUsage', 'promo_code_items'));
    }

    public function update(UpdatePromoCodeUsageRequest $request, PromoCodeUsage $promoCodeUsage)
    {
        $promoCodeUsage->update($request->all());

        return redirect()->route('admin.promo-code-usages.index');
    }

    public function show(PromoCodeUsage $promoCodeUsage)
    {
        abort_if(Gate::denies('promo_code_usage_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $promoCodeUsage->load('promo_code_item', 'client', 'payment');

        return view('admin.promoCodeUsages.show', compact('promoCodeUsage'));
    }

    public function destroy(PromoCodeUsage $promoCodeUsage)
    {
        abort_if(Gate::denies('promo_code_usage_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $promoCodeUsage->delete();

        return back();
    }

    public function massDestroy(MassDestroyPromoCodeUsageRequest $request)
    {
        $promoCodeUsages = PromoCodeUsage::find(request('ids'));

        foreach ($promoCodeUsages as $promoCodeUsage) {
            $promoCodeUsage->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}