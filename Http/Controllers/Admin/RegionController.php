<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\City\StoreCityRequest;
use App\Http\Requests\Admin\Region\StoreRegionRequest;
use App\Http\Requests\Admin\Region\UpdateRegionRequest;

class RegionController extends Controller
{
    private const AVAILABLE_STATUS = [1 => 'Active', 0 => 'Not Acvtive'];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $regions = Region::with('city:id,name')->get();
        return view('admin.regions.index', ['regions' => $regions, 'statuses' => self::AVAILABLE_STATUS, 'cities' => City::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.regions.create', ['statuses' => self::AVAILABLE_STATUS, 'cities' => City::all()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRegionRequest $request)
    {
        DB::beginTransaction();
        try {
            Region::upsert($request->safe()->regions, ['name', 'status', 'city_id']);
            DB::commit();
            return redirect()->route('regions.index')->with(['success' => 'Created successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('regions.index')->with(['error' => 'Samething want wroing!']);
        }
    }


    public function getRegion(Request $request)
    {
        $request->validate(
            [
                'region' => ['required', 'integer', 'exists:regions,id']
            ]
        );

        $region = Region::find($request->region);
        return response()->json(compact('region'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRegionRequest $request)
    {
        DB::beginTransaction();
        try {
            $region = Region::find($request->id);
            $region->update($request->validated());
            DB::commit();
            return redirect()->back()->with(['success' => 'created successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Samething Want Wroning!');
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Region $region)
    {
        DB::beginTransaction();
        try {
            $region->delete();
            DB::commit();
            return redirect()->back()->with(['success' => 'Deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Samething Want Wroning!');
        }
        
    }
}
