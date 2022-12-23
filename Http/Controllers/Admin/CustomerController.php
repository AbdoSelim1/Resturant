<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Services\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Admin\Admin\StoreAdminRequest;
use App\Http\Requests\Admin\Admin\UpdateAdminRequest;
use App\Http\Requests\Admin\Customer\StoreCustomerRequest;
use App\Http\Requests\Admin\Customer\UpdateCustomerRequest;
use App\Models\Customer;

class CustomerController extends Controller
{
    private const AVAILABLE_STATUS = [1 => 'Active', 0 => 'Block', 2 => 'Not Verified'];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.customers.index', ['customers' => Customer::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.customers.create', ['statuses' => self::AVAILABLE_STATUS]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCustomerRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['email_verified_at'] = $data['email_verified_at']  ? date('Y-m-d H:m:s') : NULL;
            $data['password'] = Hash::make($data['password']);
            $customer = Customer::create($data);
            if (isset($request->image)) {
                $customer->addMedia($request->image)->toMediaCollection('customers');
            }
            DB::commit();
            return redirectAccordingToRequest($request, "success");
        } catch (\Exception $e) {
            return redirect()->route('customers.index')->with('error', $e->getMessage());
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
    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', ['customer' => $customer, 'statuses' => self::AVAILABLE_STATUS]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {

        DB::beginTransaction();
        try {
            $data = $request->safe()->except('email_verified_at', 'password', 'password_confirmation');
            if (isset($request->password)) {
                $data['password'] = Hash::make($request->password);
            }
            $data['email_verified_at'] = $request->email_verified_at ? date('Y-m-d H:m:s') : NULL;
            $customer->update($data);
            if (isset($request->image)) {
                Media::delete($customer, 'customers');
                $customer->addMediaFromRequest('image')->toMediaCollection('customers');
            }
            DB::commit();
            return redirect()->route('customers.index')->with('success', 'تمت عمليه التعديل بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('customers.index')->with('error', 'فشلت العمليه');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        DB::beginTransaction();
        try{
            $customer->delete();
            DB::commit();
            return redirect()->back()->with('success', "بنجاح {$customer->name} تم حذف بيانات المشرف ");
        }catch(\Exception $e){
            DB::rollBack();
            return redirect()->back()->with('error' , 'فشلت العمليه');
        }
    }
}