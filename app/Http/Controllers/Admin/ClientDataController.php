<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyClientDataRequest;
use App\Http\Requests\StoreClientDataRequest;
use App\Http\Requests\UpdateClientDataRequest;
use App\Models\Client;
use App\Models\ClientData;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ClientDataController extends Controller
{
    public function index(Request $request)
    {

        abort_if(Gate::denies('client_data_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ClientData::with(['client'])->select(sprintf('%s.*', (new ClientData)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'client_data_show';
                $editGate      = 'client_data_edit';
                $deleteGate    = 'client_data_delete';
                $crudRoutePart = 'client-datas';

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
            $table->addColumn('client_name', function ($row) {
                return $row->client ? $row->client->name : '';
            });

            $table->editColumn('age', function ($row) {
                return $row->age ? $row->age : '';
            });
            $table->editColumn('gender', function ($row) {
                return $row->gender ? ClientData::GENDER_RADIO[$row->gender] : '';
            });
            $table->editColumn('primary_objective', function ($row) {
                return $row->primary_objective ? ClientData::PRIMARY_OBJECTIVE_RADIO[$row->primary_objective] : '';
            });
            $table->editColumn('fitness_level', function ($row) {
                return $row->fitness_level ? ClientData::FITNESS_LEVEL_RADIO[$row->fitness_level] : '';
            });
            $table->editColumn('primary_type', function ($row) {
                return $row->primary_type ? ClientData::PRIMARY_TYPE_RADIO[$row->primary_type] : '';
            });
            $table->editColumn('training_time', function ($row) {
                return $row->training_time ? ClientData::TRAINING_TIME_RADIO[$row->training_time] : '';
            });
            $table->editColumn('training_frequency', function ($row) {
                return $row->training_frequency ? $row->training_frequency : '';
            });
            $table->editColumn('condition', function ($row) {
                return $row->condition ? ClientData::CONDITION_RADIO[$row->condition] : '';
            });
            $table->editColumn('condition_obs', function ($row) {
                return $row->condition_obs ? $row->condition_obs : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'client']);

            return $table->make(true);
        }

        return view('admin.clientDatas.index');
    }

    public function create()
    {
        abort_if(Gate::denies('client_data_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $clients = Client::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.clientDatas.create', compact('clients'));
    }

    public function store(StoreClientDataRequest $request)
    {
        $clientData = ClientData::create($request->all());

        return redirect()->route('admin.client-datas.index');
    }

    public function edit(ClientData $clientData)
    {
        abort_if(Gate::denies('client_data_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $clients = Client::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $clientData->load('client');

        return view('admin.clientDatas.edit', compact('clientData', 'clients'));
    }

    public function update(UpdateClientDataRequest $request, ClientData $clientData)
    {
        $clientData->update($request->all());

        return redirect()->route('admin.client-datas.index');
    }

    public function show(ClientData $clientData)
    {
        abort_if(Gate::denies('client_data_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $clientData->load('client');

        return view('admin.clientDatas.show', compact('clientData'));
    }

    public function destroy(ClientData $clientData)
    {
        abort_if(Gate::denies('client_data_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $clientData->delete();

        return back();
    }

    public function massDestroy(MassDestroyClientDataRequest $request)
    {
        $clientDatas = ClientData::find(request('ids'));

        foreach ($clientDatas as $clientData) {
            $clientData->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
