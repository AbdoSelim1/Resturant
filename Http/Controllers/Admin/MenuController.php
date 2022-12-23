<?php

namespace App\Http\Controllers\Admin;

use App\Models\Menu;
use App\Models\Category;
use Mockery\Expectation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use App\Http\Requests\Admin\Menu\StoreMenuRequest;
use App\Http\Requests\Admin\Menu\UpdateMenuRequest;

class MenuController extends Controller
{
    private const AVAILABLE_STATUS = [1 => 'available', 0 => 'Not available'];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menus = Menu::with('category:id,name')->get();
        // dd($menus);
        return view('admin.menus.index', compact('menus'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::where('status', "1")->get();
        return view('admin.menus.create', ['categories' => $categories, 'statusess' => self::AVAILABLE_STATUS]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMenuRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->except(['images', 'prices_sizes', '_token', 'create_return']);
            $data['prices_sizes'] = json_encode($request->prices_sizes);
            $data['slug'] = str_replace(" " ,"-" ,$request->name);
            $menu = Menu::create($data);
            $menu->storeImages($request->images)->resize();
            DB::commit();
            return redirectAccordingToRequest($request);
        } catch (Expectation $e) {
            DB::rollBack();
            $error = $e->getExceptionMessage();
            return redirect()->back()->with(['error' => $error]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $menu = Menu::where('id', $id)->with('category')->first();
        $menu->prices_sizes = json_decode($menu->prices_sizes);
        return view('admin.menus.show', compact('menu'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $menu = Menu::where('id', $id)->with('category')->first();
        $menu->prices_sizes = json_decode($menu->prices_sizes);
        return view('admin.menus.edit', ['categories' => Category::where('status', '1')->get(), 'menu' => $menu, 'statusess' => self::AVAILABLE_STATUS]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMenuRequest $request, Menu $menu)
    {
        DB::beginTransaction();
        try {
            $data = $request->except(['images', 'prices_sizes', '_token']);
            $data['prices_sizes'] = json_encode($request->prices_sizes);
            $data['slug'] =  str_replace(" " ,"-" ,$request->name);
            $menu->update($data);
            if (isset($request->images[0]['file_name'])) {
                $menu->storeImages($request->images)->resize();
            }
            DB::commit();
            return redirect()->route('menus.index')->with(['success' => 'Updated Successfully']);
        } catch (Expectation $e) {
            DB::rollBack();
            $error = $e->getExceptionMessage();
            return redirect()->back()->with(['error' => $error]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Menu $menu)
    {
        DB::beginTransaction();
        try {
            $menu->delete();
            DB::commit();
            return redirect()->back()->with(['success' => 'Deleted Successfully']);
        } catch (Expectation $e) {
            DB::rollBack();
            $error = $e->getExceptionMessage();
            return redirect()->back()->with(['error' => $error]);
        }
    }

    public function removeImg(Request $request)
    {
        $request->validate([
            'imgId' => ['required', 'integer', 'exists:media,id'],
            'menu' => ['required', 'integer', 'exists:menus,id']
        ]);
        $menu = Menu::find($request->menu);
        $images = $menu->getMedia('menues');
        foreach ($images as $index => $img) {
            if ($request->imgId == $img->id) {
                $images[$index]->delete();
                return response()->json(['success' => true]);
            }
        }

        return response()->json(['error' => false], 404);
    }
}
