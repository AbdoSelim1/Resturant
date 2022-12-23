<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Category\StoreCategoryRequest;
use App\Http\Requests\Admin\Category\UpdateCategoryRequest;

class CategoryController extends Controller
{
    private const AVAILABLE_STATUS = [1 => 'Active', 0 => 'Not Acvtive'];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::all();
        return view('admin.categories.index', ['categories' => $categories, "statuses" => self::AVAILABLE_STATUS]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.categories.create', ["statuses" => self::AVAILABLE_STATUS]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCategoryRequest $request)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            Category::upsert($request->safe()->categories, ['name', 'status']);
            DB::commit();
            return redirect()->route('categories.index')->with(['success' => 'Created successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
            return redirect()->route('categories.index')->with(['error' => 'Samething want wroing!']);
        }
    }

  
    public function getCategory(Request $request)
    {
        $request->validate(
            [
                'category' => ['required', 'integer', 'exists:categories,id']
            ]
        );

        $category = category::find($request->category);
        return response()->json(compact('category'));
    }

    public function updateCategory(UpdateCategoryRequest $request)
    {
        $category = Category::find($request->id);
        $category->update($request->validated());
        return redirect()->back()->with(['success' => 'Updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        DB::beginTransaction();
        try {
            $category->delete();
            DB::commit();
            return redirect()->back()->with(['success' => 'Deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Samething Want Wroning!');
        };
    }
}
