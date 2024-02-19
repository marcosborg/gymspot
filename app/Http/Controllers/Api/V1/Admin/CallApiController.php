<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreCallRequest;
use App\Http\Requests\UpdateCallRequest;
use App\Http\Resources\Admin\CallResource;
use App\Models\Call;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CallApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('call_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new CallResource(Call::all());
    }

    public function store(StoreCallRequest $request)
    {
        $call = Call::create($request->all());

        if ($request->input('image', false)) {
            $call->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
        }

        return (new CallResource($call))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Call $call)
    {
        abort_if(Gate::denies('call_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new CallResource($call);
    }

    public function update(UpdateCallRequest $request, Call $call)
    {
        $call->update($request->all());

        if ($request->input('image', false)) {
            if (! $call->image || $request->input('image') !== $call->image->file_name) {
                if ($call->image) {
                    $call->image->delete();
                }
                $call->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
            }
        } elseif ($call->image) {
            $call->image->delete();
        }

        return (new CallResource($call))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Call $call)
    {
        abort_if(Gate::denies('call_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $call->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
