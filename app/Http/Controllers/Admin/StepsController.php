<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyStepRequest;
use App\Http\Requests\StoreStepRequest;
use App\Http\Requests\UpdateStepRequest;
use App\Models\Step;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class StepsController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('step_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Step::query()->select(sprintf('%s.*', (new Step)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'step_show';
                $editGate      = 'step_edit';
                $deleteGate    = 'step_delete';
                $crudRoutePart = 'steps';

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
            $table->editColumn('number', function ($row) {
                return $row->number ? $row->number : '';
            });
            $table->editColumn('title', function ($row) {
                return $row->title ? $row->title : '';
            });
            $table->editColumn('text', function ($row) {
                return $row->text ? $row->text : '';
            });
            $table->editColumn('button', function ($row) {
                return $row->button ? $row->button : '';
            });
            $table->editColumn('link', function ($row) {
                return $row->link ? $row->link : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.steps.index');
    }

    public function create()
    {
        abort_if(Gate::denies('step_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.steps.create');
    }

    public function store(StoreStepRequest $request)
    {
        $step = Step::create($request->all());

        return redirect()->route('admin.steps.index');
    }

    public function edit(Step $step)
    {
        abort_if(Gate::denies('step_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.steps.edit', compact('step'));
    }

    public function update(UpdateStepRequest $request, Step $step)
    {
        $step->update($request->all());

        return redirect()->route('admin.steps.index');
    }

    public function show(Step $step)
    {
        abort_if(Gate::denies('step_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.steps.show', compact('step'));
    }

    public function destroy(Step $step)
    {
        abort_if(Gate::denies('step_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $step->delete();

        return back();
    }

    public function massDestroy(MassDestroyStepRequest $request)
    {
        $steps = Step::find(request('ids'));

        foreach ($steps as $step) {
            $step->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
