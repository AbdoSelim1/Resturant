<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
use App\Models\Region;
use App\Models\Address;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Address\GetRegionsRequest;
use App\Http\Requests\Admin\Address\StoreAddressRequest;

class AddressController extends Controller
{
    private const AVAILABLE_STATUS = [1 => 'Delivery available', 0 => ' Delivery Not available'];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $addresses = Address::with('region.city')->get();
        return view('admin.addresses.index', compact('addresses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $cities = City::where('status', '=', "1")->get();
        $regions = Region::where('status', '=', "1")->get();
        $customers = Customer::select('id', 'name', 'email')->where('status', '=', "1")->get();
        return view('admin.addresses.create', ['cities' => $cities, 'regions' => $regions, 'customers' => $customers, 'statusess' => self::AVAILABLE_STATUS]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAddressRequest $request)
    {

        DB::beginTransaction();
        try {
            Address::create($request->validated());
            DB::commit();
            return redirectAccordingToRequest($request);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('addresses.index')->with('error', 'Something Went Wronge !');
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $address = Address::where('id', $id)->with('customer')->with('region.city')->first();
        $cities = City::where('status', '=', "1")->get();
        $regions = DB::select("SELECT
        *
    FROM
        regions
    WHERE
    `status`= '1' AND city_id IN(
        SELECT
            cities.id
        FROM
            `addresses`
        JOIN regions ON addresses.region_id = regions.id
        JOIN cities ON regions.city_id = cities.id
        WHERE
            addresses.id = $address->id
    )");
        $customers = Customer::select('id', 'name', 'email')->where('status', '=', "1")->get();
        return view(
            'admin.addresses.edit',
            [
                'cities' => $cities,
                'regions' => $regions,
                'customers' => $customers,
                'statusess' => self::AVAILABLE_STATUS,
                'address' => $address
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreAddressRequest $request, Address $address)
    {

        DB::beginTransaction();
        try {
            $address->update($request->validated());
            DB::commit();
            return redirect()->route('addresses.index')->with('success', 'Updated Successfilly');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('addresses.index')->with('error', 'Something Went Wronge !');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Address $address)
    {
        DB::beginTransaction();
        try {
            $address->delete();
            DB::commit();
            return redirect()->route('addresses.index')->with('success', 'Deleted Successfilly');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('addresses.index')->with('error', 'Something Went Wronge!');
        }
    }

    public function regions(GetRegionsRequest $request)
    {
        $regions = Region::select('id', 'name')->where([['city_id', $request->city_id], ['status', "1"]])->get();
        return response()->json(compact('regions'));
    }
}
