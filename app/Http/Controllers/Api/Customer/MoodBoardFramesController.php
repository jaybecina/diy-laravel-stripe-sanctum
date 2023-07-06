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

use App\Models\MoodBoardFrame;
use App\Models\MoodBoard;
use App\Models\User;

use App\Http\Requests\MoodBoardFrameStoreRequest;

class MoodBoardFramesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllMoodBoardFrames()
    {
        $mood_board_frames = MoodBoardFrame::all();

        if(!$mood_board_frames) {
            return response(['status' => 400, 'message' => 'No mood board frame found!'], 400);
        }

        return response()->json([
            'status'   =>  200,
            'data'     =>  $mood_board_frames,
            'message'  =>  'All Mood board frames fetched successfully!'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getMoodBoardFrame($frameId)
    {
        $mood_board_frame = MoodBoardFrame::find($frameId);

        if(!$mood_board_frame) {
            return response(['status' => 400, 'message' => 'No mood board frame found!'], 400);
        }

        return response()->json([
            'status'   =>  200,
            'data'     =>  $mood_board_frame,
            'message'  =>  'Mood board frame fetched successfully!'
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addMoodBoardFrame(Request $request)
    {
        DB::beginTransaction();

        try {
            // Handle File Upload
            if($request->hasFile('imageFrame')) {
                $image = $request->file('imageFrame');

                // Naming the file
                $imageName = uniqid() . time() . '.png';

                // Upload Image storage
                Storage::disk('local')->put('/public/mood_board_frames/' . $imageName, File::get($image));

                // Image intervention resize then upload to storage
                $path = public_path('storage/mood_board_frames/'.$imageName); 
                $img = Image::make($image->getRealPath());
                $img->resize($request->w, $request->h, function ($constraint) {
                    $constraint->aspectRatio();                 
                });
                $img->save($path);
            }

            $mood_board_frame = MoodBoardFrame::create([
                'name'           =>  $request->name,
                'mood_board_id'  =>  null,
                'user_id'        =>  null,
                'image_url'      =>  config('app.url') . '/storage/mood_board_frames/' . $imageName,
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
            'data'     =>  $mood_board_frame,
            'message'  =>  'Mood board frame saved successfully!'
        ], 200);
    }
}
