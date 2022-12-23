<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Admin\Admin\StoreAdminRequest;
use App\Http\Requests\Admin\Admin\UpdateAdminRequest;
use App\Services\Media;

class AdminController extends Controller
{
    private const AVAILABLE_STATUS = [1 => 'Active', 0 => 'Block', 2 => 'Not Verified'];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.admins.index', ['admins' => Admin::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.admins.create', ['statuses' => self::AVAILABLE_STATUS]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAdminRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['email_verified_at'] = $data['email_verified_at']  ? date('Y-m-d H:m:s') : NULL;
            $data['password'] = Hash::make($data['password']);
            $admin = Admin::create($data);
            if (isset($request->image)) {
                $admin->addMedia($request->image)->toMediaCollection('admins');
            }
            DB::commit();
            return redirectAccordingToRequest($request, "success");
        } catch (\Exception $e) {
            return redirect()->route('admins.index')->with('error', $e->getMessage());
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
    public function edit(Admin $admin)
    {
        return view('admin.admins.edit', ['admin' => $admin, 'statuses' => self::AVAILABLE_STATUS]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAdminRequest $request, Admin $admin)
    {
        // dd($request->all());

        DB::beginTransaction();
        try {
            $data = $request->safe()->except('email_verified_at', 'password', 'password_confirmation');

            if (isset($request->password)) {
                $data['password'] = Hash::make($request->password);
            }
            $data['email_verified_at'] = $request->email_verified_at ? date('Y-m-d H:m:s') : NULL;
            $admin->update($data);
            if (isset($request->image)) {
                Media::delete($admin, 'admins');
                $admin->addMediaFromRequest('image')->toMediaCollection('admins');
            }
            DB::commit();
            return redirect()->route('admins.index')->with('success', 'تمت عمليه التعديل بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admins.index')->with('error', 'فشلت العمليه');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Admin $admin)
    {
        DB::beginTransaction();
        try{
            $admin->delete();
            DB::commit();
            return redirect()->back()->with('success', "بنجاح {$admin->name} تم حذف بيانات المشرف ");
        }catch(\Exception $e){
            DB::rollBack();
            return redirect()->back()->with('error' , 'فشلت العمليه');
        }
    }
}
