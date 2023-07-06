<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Exceptions\HttpResponseException;

use App\Models\MoodBoard;
use App\Models\Supplier;
use App\Models\User;

use App\Http\Requests\SupplierStoreRequest;

class SuppliersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $suppliers = Supplier::with('galleries')->get();

        if(!$suppliers) {
            return response(['status' => 400, 'message' => 'No supplier found!'], 400);
        }

        return response()->json([
            'status'    => 200,
            'data'      => $suppliers,
            'message'   => 'Supplier fetched successfully!'
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SupplierStoreRequest $request)
    {
        DB::beginTransaction();

        try {
            $supplier = Supplier::create([
                'company' => $request->company,
                'status' => 'Active',
                'created_by' => auth()->user()->id,
            ]);
        } catch(\Exception $e)
        {
            // Rollback and then redirect
            // back to form with errors
            DB::rollback();

            return response(['status' => 400, 'message' => $e->getMessage()], 400);

        } catch(\Throwable $e)
        {
            DB::rollback();

            return response(['status' => 400, 'message' => $e->getMessage()], 400);
        }

        DB::commit();

        return response()->json([
            'status'    => 200,
            'data'      => $supplier,
            'message'   => 'Supplier saved successfully!'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $supplier = Supplier::with('galleries')->find($id);
        $supplier = Supplier::with('galleries')->find($id);

        if(!$supplier) {
            return response(['status' => 400, 'message' => 'No supplier found!'], 400);
        }

        return response()->json([
            'status'    => 200,
            'data'      => $supplier,
            'message'   => 'Supplier fetched successfully!'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
