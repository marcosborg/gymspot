<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Http\Resources\Admin\MenuResource;
use App\Models\Menu;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MenuApiController extends Controller
{
    public function index()
    {
        return new MenuResource(Menu::orderBy('position')
            ->whereHas('content_page')
            ->get()
            ->load('content_page'));
    }

}
