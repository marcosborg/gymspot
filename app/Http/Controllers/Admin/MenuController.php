<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyMenuRequest;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Models\ContentPage;
use App\Models\Menu;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('menu_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Menu::with(['content_page'])->select(sprintf('%s.*', (new Menu)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'menu_show';
                $editGate      = 'menu_edit';
                $deleteGate    = 'menu_delete';
                $crudRoutePart = 'menus';

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
            $table->addColumn('content_page_title', function ($row) {
                return $row->content_page ? $row->content_page->title : '';
            });

            $table->editColumn('link', function ($row) {
                return $row->link ? $row->link : '';
            });
            $table->editColumn('position', function ($row) {
                return $row->position ? $row->position : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'content_page']);

            return $table->make(true);
        }

        $content_pages = ContentPage::get();

        return view('admin.menus.index', compact('content_pages'));
    }

    public function create()
    {
        abort_if(Gate::denies('menu_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $content_pages = ContentPage::pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.menus.create', compact('content_pages'));
    }

    public function store(StoreMenuRequest $request)
    {
        $menu = Menu::create($request->all());

        return redirect()->route('admin.menus.index');
    }

    public function edit(Menu $menu)
    {
        abort_if(Gate::denies('menu_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $content_pages = ContentPage::pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');

        $menu->load('content_page');

        return view('admin.menus.edit', compact('content_pages', 'menu'));
    }

    public function update(UpdateMenuRequest $request, Menu $menu)
    {
        $menu->update($request->all());

        return redirect()->route('admin.menus.index');
    }

    public function show(Menu $menu)
    {
        abort_if(Gate::denies('menu_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $menu->load('content_page');

        return view('admin.menus.show', compact('menu'));
    }

    public function destroy(Menu $menu)
    {
        abort_if(Gate::denies('menu_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $menu->delete();

        return back();
    }

    public function massDestroy(MassDestroyMenuRequest $request)
    {
        $menus = Menu::find(request('ids'));

        foreach ($menus as $menu) {
            $menu->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
