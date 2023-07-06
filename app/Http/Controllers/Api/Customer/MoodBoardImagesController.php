<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Image;

use App\Models\MoodBoardContent;
use App\Models\MoodBoardImage;
use App\Models\User;

use App\Http\Requests\MoodBoardImageStoreRequest;

class MoodBoardImagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllMoodBoardImages()
    {
        $mb_images = MoodBoardImage::all();

        return response()->json([
            'status'    => 200,
            'data'      => $mb_images,
            'message'   => 'All Mood board images fetched successfully!'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getMoodBoardImage($imageId)
    {
        $mb_image = MoodBoardImage::find($imageId);

        return response()->json([
            'status'    => 200,
            'data'      => $mb_image,
            'message'   => 'Mood board image fetched successfully!'
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addMoodBoardImages(MoodBoardImageStoreRequest $request)
    {
        DB::beginTransaction();

        try {
            // Handle File Upload
            if($request->hasFile('imageMoodBoard')) {
                $image = $request->file('imageMoodBoard');

                // Naming the file
                $imageName = uniqid() . time() . '.png';

                // Upload Image storage
                Storage::disk('local')->put('/public/mood_board_images/' . $imageName, File::get($image));

                // Image intervention resize then upload to storage
                $path = public_path('storage/mood_board_images/'.$imageName); 
                $img = Image::make($image->getRealPath());
                $img->resize($request->w, $request->h, function ($constraint) {
                    $constraint->aspectRatio();                 
                });
                $img->save($path);
            }

            $mbImage = MoodBoardImage::create([
                'name'          =>  $request->name,
                'image_url'     =>  config('app.url') . '/storage/mood_board_images/' . $imageName,
                'user_id'       =>  auth()->user()->id,
                'w'             =>  $request->w,
                'h'             =>  $request->h,
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
            'data'      => $mbImage,
            'message'   => 'Mood board image saved successfully!'
        ], 201);
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
