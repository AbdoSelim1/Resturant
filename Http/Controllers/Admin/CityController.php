<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\City\StoreCityRequest;
use App\Http\Requests\Admin\City\UpdateCityRequest;

class CityController extends Controller
{
    private const AVAILABLE_STATUS = [1 => 'Active', 0 => 'Not Acvtive'];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cities = City::all();
        return view('admin.cities.index', ['cities' => $cities, 'statuses' => self::AVAILABLE_STATUS]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.cities.create', ['statuses' => self::AVAILABLE_STATUS]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCityRequest $request)
    {
        DB::beginTransaction();
        try {
            City::upsert($request->safe()->cities, ['name', 'status']);
            DB::commit();
            return redirect()->route('cities.index')->with(['success' => 'Created successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cities.index')->with(['error' => 'Samething want wroing!']);
        }
    }


    public function getCity(Request $request)
    {
        $request->validate(
            [
                'city' => ['required', 'integer', 'exists:cities,id']
            ]
        );

        $city = City::find($request->city);
        return response()->json(compact('city'));
    }
    public function updateCity(UpdateCityRequest $request)
    {
        $city = City::find($request->id);
        $city->update($request->validated());
        return redirect()->back()->with(['success' => 'Updated successfully']);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(City $city)
    {
        DB::beginTransaction();
        try {
            $city->delete();
            DB::commit();
            return redirect()->back()->with(['success' => 'Deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Samething Want Wroning!');
        };
    }
}
