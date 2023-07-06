<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Image;

use App\Models\MoodBoardBackground;
use App\Models\MoodBoard;
use App\Models\User;

use App\Http\Requests\MoodBoardBackgroundStoreRequest;

class MoodBoardBackgroundsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllMoodBoardBackgrounds()
    {
        $mood_board_backgrounds = MoodBoardBackground::all();

        if(!$mood_board_backgrounds) {
            return response(['status' => 400, 'message' => 'No mood board background found!'], 400);
        }

        return response()->json([
            'status'    => 200,
            'data'      => $mood_board_backgrounds,
            'message'   => 'All Mood board backgrounds fetched successfully!'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getMoodBoardBackground($backgroundId)
    {
        $mood_board_background = MoodBoardBackground::find($backgroundId);

        if(!$mood_board_background) {
            return response(['status' => 400, 'message' => 'No mood board background found!'], 400);
        }

        return response()->json([
            'status'    => 200,
            'data'      => $mood_board_background,
            'message'   => 'Mood board background fetched successfully!'
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addMoodBoardBackground(Request $request)
    {
        DB::beginTransaction();

        try {
            // Handle File Upload
            if($request->hasFile('imageBackground')) {
                $image = $request->file('imageBackground');

                // Naming the file
                $imageName = uniqid() . time() . '.png';

                // Upload Image storage
                Storage::disk('local')->put('/public/mood_board_backgrounds/' . $imageName, File::get($image));

                // Image intervention resize then upload to storage
                $path = public_path('storage/mood_board_backgrounds/'.$imageName); 
                $img = Image::make($image->getRealPath());
                $img->resize($request->w, $request->h, function ($constraint) {
                    $constraint->aspectRatio();                 
                });
                $img->save($path);
            }

            $mood_board_background = MoodBoardBackground::create([
                'name'           =>  $request->name,
                'mood_board_id'  =>  null,
                'user_id'        =>  null,
                'image_url'      =>  config('app.url') . '/storage/mood_board_backgrounds/' . $imageName,
                'w'              =>  $request->w,
                'h'              =>  $request->h,
                'created_by'     =>  auth()->user()->id,
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
            'status'   =>  200,
            'data'     =>  $mood_board_background,
            'message'  =>  'Mood board background saved successfully!'
        ], 200);
    }
}
