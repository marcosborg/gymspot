<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyRentedSlotRequest;
use App\Http\Requests\StoreRentedSlotRequest;
use App\Http\Requests\UpdateRentedSlotRequest;
use App\Models\Client;
use App\Models\RentedSlot;
use App\Models\Spot;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class RentedSlotController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('rented_slot_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = RentedSlot::with(['spot', 'client'])->select(sprintf('%s.*', (new RentedSlot)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'rented_slot_show';
                $editGate      = 'rented_slot_edit';
                $deleteGate    = 'rented_slot_delete';
                $crudRoutePart = 'rented-slots';

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
            $table->addColumn('spot_name', function ($row) {
                return $row->spot ? $row->spot->name : '';
            });

            $table->addColumn('client_name', function ($row) {
                return $row->client ? $row->client->name : '';
            });

            $table->editColumn('client.vat', function ($row) {
                return $row->client ? (is_string($row->client) ? $row->client : $row->client->vat) : '';
            });

            $table->editColumn('keypass', function ($row) {
                return $row->keypass ? $row->keypass : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'spot', 'client']);

            return $table->make(true);
        }

        $spots   = Spot::get();
        $clients = Client::get();

        return view('admin.rentedSlots.index', compact('spots', 'clients'));
    }

    public function create()
    {
        abort_if(Gate::denies('rented_slot_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $spots = Spot::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $clients = Client::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.rentedSlots.create', compact('clients', 'spots'));
    }

    public function store(StoreRentedSlotRequest $request)
    {
        $rentedSlot = RentedSlot::create($request->all());

        return redirect()->route('admin.rented-slots.index');
    }

    public function edit(RentedSlot $rentedSlot)
    {
        abort_if(Gate::denies('rented_slot_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $spots = Spot::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $clients = Client::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $rentedSlot->load('spot', 'client');

        return view('admin.rentedSlots.edit', compact('clients', 'rentedSlot', 'spots'));
    }

    public function update(UpdateRentedSlotRequest $request, RentedSlot $rentedSlot)
    {
        $rentedSlot->update($request->all());

        return redirect()->route('admin.rented-slots.index');
    }

    public function show(RentedSlot $rentedSlot)
    {
        abort_if(Gate::denies('rented_slot_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $rentedSlot->load('spot', 'client');

        return view('admin.rentedSlots.show', compact('rentedSlot'));
    }

    public function destroy(RentedSlot $rentedSlot)
    {
        abort_if(Gate::denies('rented_slot_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $rentedSlot->delete();

        return back();
    }

    public function massDestroy(MassDestroyRentedSlotRequest $request)
    {
        $rentedSlots = RentedSlot::find(request('ids'));

        foreach ($rentedSlots as $rentedSlot) {
            $rentedSlot->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
