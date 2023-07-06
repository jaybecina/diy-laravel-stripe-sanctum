<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Models\MoodBoard;
use App\Models\Supplier;
use App\Models\Gallery;
use App\Models\User;

use App\Http\Requests\GalleryStoreRequest;

class GalleriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GalleryStoreRequest $request)
    {
        DB::beginTransaction();

        try {

            $base64image = $request->src;

            if (preg_match('/^data:image\/(\w+);base64,/', $base64image)) {
                $value = substr($base64image, strpos($base64image, ',') + 1);
                $value = base64_decode($value);

                $imageName = $request->uid . '.png';
                
                // $val = Storage::put($imageName, $value);

                $val = Storage::disk('local')->put('/public/gallery_images/' . $imageName, $value);

                if(!$val) {
                    return response(['status' => 400, 'message' => 'Error in saving image in storage disk'], 400);
                }
                
                // $path = public_path('storage/profile_images/'.$imageName); // this cause cant write image to public...
                $path = 'gallery_images/'.$imageName;
                // $resized_image = Image::make($base64image)->resize(304, 277)->save($path);
          
                $result_image = $imageName;

            } else {
                $result_image = null;

                return response(['status' => 400, 'message' => 'Image not valid format'], 400);
            }

            if($request->has_supplier) {
                if(!$request->supplier_id) {
                    return response(['status' => 400, 'message' => 'Requires a supplier!'], 400);
                }
            }

            if(env("APP_URL") == "http://localhost:8000") {
                $app_url = env("APP_URL");
            } else {
                $app_url = "https://diy-api.dev.nxt.work";
            }

            $gallery = Gallery::create([
                'sku'               =>  $request->sku,
                'name'              =>  $request->name,
                'supplier_id'       =>  $request->supplier_id,
                'colour'            =>  $request->colour,
                'image_path'        =>  $app_url . '/storage/gallery_images/' . $result_image,
                'uid'               =>  $request->uid,
                'src'               =>  Str::slug($request->name, "-") . '_' . $request->uid . '.png',
                'length'            =>  $request->length,
                'width'             =>  $request->width,
                'height'            =>  $request->height,
                'tags'              =>  $request->tags,
                'price'             =>  $request->price,
                'discount'          =>  $request->discount,
                'quantity'          =>  $request->quantity,
                'status'            =>  'Available',
                'created_by'        =>  auth()->user()->id,
                'has_supplier'      =>  $request->has_supplier,
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
            'data'      => $gallery,
            'message'   => 'Gallery image saved successfully!'
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
        //
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
