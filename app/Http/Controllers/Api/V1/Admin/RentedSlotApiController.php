<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRentedSlotRequest;
use App\Http\Requests\UpdateRentedSlotRequest;
use App\Http\Resources\Admin\RentedSlotResource;
use App\Models\RentedSlot;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Client;

class RentedSlotApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('rented_slot_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new RentedSlotResource(RentedSlot::with(['spot', 'client'])->get());
    }

    public function store(StoreRentedSlotRequest $request)
    {
        $rentedSlot = RentedSlot::create($request->all());

        return (new RentedSlotResource($rentedSlot))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(RentedSlot $rentedSlot)
    {
        abort_if(Gate::denies('rented_slot_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new RentedSlotResource($rentedSlot->load(['spot', 'client']));
    }

    public function update(UpdateRentedSlotRequest $request, RentedSlot $rentedSlot)
    {
        $rentedSlot->update($request->all());

        return (new RentedSlotResource($rentedSlot))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(RentedSlot $rentedSlot)
    {
        abort_if(Gate::denies('rented_slot_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $rentedSlot->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function rentedSlots(Request $request)
    {
        $user_id = $request->user()->id;
        $client = Client::where('user_id', $user_id)->first();
        $rented_slots = RentedSlot::where('client_id', $client->id)->orderBy('id', 'desc')->get()->load('spot');
        return $rented_slots;
    }
}
