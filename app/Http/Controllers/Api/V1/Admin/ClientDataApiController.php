<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClientDataRequest;
use App\Http\Requests\UpdateClientDataRequest;
use App\Http\Resources\Admin\ClientDataResource;
use App\Models\ClientData;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientDataApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('client_data_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ClientDataResource(ClientData::with(['client'])->get());
    }

    public function store(StoreClientDataRequest $request)
    {
        $clientData = ClientData::create($request->all());

        return (new ClientDataResource($clientData))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(ClientData $clientData)
    {
        abort_if(Gate::denies('client_data_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ClientDataResource($clientData->load(['client']));
    }

    public function update(UpdateClientDataRequest $request, ClientData $clientData)
    {
        $clientData->update($request->all());

        return (new ClientDataResource($clientData))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(ClientData $clientData)
    {
        abort_if(Gate::denies('client_data_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $clientData->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function createClientData(Request $request)
    {

        $request->validate([
            'age' => 'required|max:2',
            'primary_objective' => 'required',
            'fitness_level' => 'required'
        ], [], [
            'age' => 'Idade',
            'primary_objective' => 'Objetivo principal',
            'fitness_level' => 'Qual é o seu nível de experiência com exercícios físicos?'
        ]);

        $client_data = new ClientData;
        $client_data->client_id = $request->user()->client->id;
        $client_data->age = $request->age;
        $client_data->gender = $request->gender;
        $client_data->primary_objective = $request->primary_objective;
        $client_data->fitness_level = $request->fitness_level;
        $client_data->condition = $request->condition;
        $client_data->condition_obs = $request->condition_obs;
        $client_data->primary_type = $request->primary_type;
        $client_data->training_time = $request->training_time;
        $client_data->training_frequency = $request->training_frequency;
        $client_data->save();
    }

    public function updateClientData(Request $request)
    {

        $request->validate([
            'age' => 'required|max:2',
            'primary_objective' => 'required',
            'fitness_level' => 'required'
        ], [], [
            'age' => 'Idade',
            'primary_objective' => 'Objetivo principal',
            'fitness_level' => 'Qual é o seu nível de experiência com exercícios físicos?'
        ]);

        $client_data = ClientData::where([
            'client_id' => $request->user()->client->id
        ])->first();
        $client_data->client_id = $request->user()->client->id;
        $client_data->age = $request->age;
        $client_data->gender = $request->gender;
        $client_data->primary_objective = $request->primary_objective;
        $client_data->fitness_level = $request->fitness_level;
        $client_data->condition = $request->condition;
        $client_data->condition_obs = $request->condition_obs;
        $client_data->primary_type = $request->primary_type;
        $client_data->training_time = $request->training_time;
        $client_data->training_frequency = $request->training_frequency;
        $client_data->save();

    }
}
