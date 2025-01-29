<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroySlotRequest;
use App\Http\Requests\StoreSlotRequest;
use App\Http\Requests\UpdateSlotRequest;
use App\Models\Slot;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class SlotController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('slot_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Slot::query()->select(sprintf('%s.*', (new Slot)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'slot_show';
                $editGate      = 'slot_edit';
                $deleteGate    = 'slot_delete';
                $crudRoutePart = 'slots';

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
            $table->editColumn('time', function ($row) {
                return $row->time ? $row->time : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.slots.index');
    }

    public function create()
    {
        abort_if(Gate::denies('slot_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.slots.create');
    }

    public function store(StoreSlotRequest $request)
    {
        $slot = Slot::create($request->all());

        return redirect()->route('admin.slots.index');
    }

    public function edit(Slot $slot)
    {
        abort_if(Gate::denies('slot_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.slots.edit', compact('slot'));
    }

    public function update(UpdateSlotRequest $request, Slot $slot)
    {
        $slot->update($request->all());

        return redirect()->route('admin.slots.index');
    }

    public function show(Slot $slot)
    {
        abort_if(Gate::denies('slot_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.slots.show', compact('slot'));
    }

    public function destroy(Slot $slot)
    {
        abort_if(Gate::denies('slot_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $slot->delete();

        return back();
    }

    public function massDestroy(MassDestroySlotRequest $request)
    {
        $slots = Slot::find(request('ids'));

        foreach ($slots as $slot) {
            $slot->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
