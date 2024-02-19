<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStepRequest;
use App\Http\Requests\UpdateStepRequest;
use App\Http\Resources\Admin\StepResource;
use App\Models\Step;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StepsApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('step_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new StepResource(Step::all());
    }

    public function store(StoreStepRequest $request)
    {
        $step = Step::create($request->all());

        return (new StepResource($step))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Step $step)
    {
        abort_if(Gate::denies('step_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new StepResource($step);
    }

    public function update(UpdateStepRequest $request, Step $step)
    {
        $step->update($request->all());

        return (new StepResource($step))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Step $step)
    {
        abort_if(Gate::denies('step_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $step->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
