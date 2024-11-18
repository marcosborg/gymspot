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
}
