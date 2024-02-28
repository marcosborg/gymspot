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
        return new StepResource(Step::all());
    }
}
