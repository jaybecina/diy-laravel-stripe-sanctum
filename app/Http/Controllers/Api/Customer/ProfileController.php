<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Storage;
use Image;

use App\Models\User;

use App\Http\Requests\ProfileUpdateRequest;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // // Convert image file into base64 image
        // $path = 'myfolder/myimage.png';
        // $type = pathinfo($path, PATHINFO_EXTENSION);
        // $data = file_get_contents($path);
        // $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        return response()->json([
            'status'  => 200,
            'data'    => 'asd',
            'message' => 'Profile get successfully!'
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function update(ProfileUpdateRequest $request)
    {
        DB::beginTransaction();

        try {
            // Handle File Upload
            $base64image = $request->profileImage;

            if (preg_match('/^data:image\/(\w+);base64,/', $base64image)) {
                $value = substr($base64image, strpos($base64image, ',') + 1);
                $value = base64_decode($value);
                $imageName = 'profile_' . time()  . '.png';
                // $val = Storage::put($imageName, $value);

                $val = Storage::disk('local')->put('/public/profile_images/' . $imageName, $value);

                if(!$val) {
                    return response(['status' => 400, 'message' => 'Error in saving image in storage disk'], 400);
                }
                
                // $path = public_path('storage/profile_images/'.$imageName); // this cause cant write image to public...
                $path = 'profile_images/'.$imageName;
                // $resized_image = Image::make($base64image)->resize(304, 277)->save($path);
          
                $result_image = $imageName;

            } else {
                $result_image = null;
            }

            $profile = User::where('email', $request->email)->first();
            $profile->address = $request->address;
            $profile->first_name = $request->firstName;
            $profile->last_name = $request->lastName;
            $profile->profile_image = $result_image;
            $profile->save();
            
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

        $accessToken = auth()->user()->createToken('auth_token')->accessToken;

        return response()->json([
            'status'        => 200,
            'data'          => $profile,
            'accessToken'   => $accessToken,
            'message'       => 'Profile updated successfully!'
        ], 200);
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
