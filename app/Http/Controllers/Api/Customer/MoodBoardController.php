<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Models\MoodBoard;
use App\Models\Gallery;
use App\Models\User;

use App\Http\Requests\MoodBoardStoreRequest;
use App\Http\Requests\MoodBoardUpdateRequest;

class MoodBoardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllMoodBoardContents()
    {
        $mood_boards = MoodBoard::with(['mood_board_images', 'mood_board_backgrounds', 'mood_board_frames'])->get();

        if(!$mood_boards) {
            return response(['status' => 400, 'message' => 'No mood board found!'], 400);
        }

        return response()->json([
            'status'    => 200,
            'data'      => $mood_boards,
            'message'   => 'All Mood boards fetched successfully!'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getMoodBoardContent($moodBoardId)
    {
        $mood_board = MoodBoard::with(['mood_board_images', 'mood_board_backgrounds', 'mood_board_frames'])->find($moodBoardId);

        if(!$mood_board) {
            return response(['status' => 400, 'message' => 'No mood board found!'], 400);
        }

        return response()->json([
            'status'    => 200,
            'data'      => $mood_board,
            'message'   => 'Mood board fetched successfully!'
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addMoodBoardContent(MoodBoardStoreRequest $request)
    {
        DB::beginTransaction();

        try {
            $mood_board = MoodBoard::create([
                'name'        =>  $request->name,
                'user_id'     =>  auth()->user()->id,
                'version'     =>  1,
            ]);

            $mood_board = MoodBoard::where([
                'name' => $request->name, 
                'user_id' => auth()->user()->id
            ])->latest()->first();

            foreach($request->moodBoardImg as $moodBoardImg) {
                $mood_board->mood_board_images()->attach($moodBoardImg['mood_board_image_id'], 
                    [
                        'x' => $moodBoardImg['x'],
                        'y' => $moodBoardImg['y'],
                        'w' => $moodBoardImg['w'],
                        'h' => $moodBoardImg['h'],
                    ]
                );
            }

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
            'data'      => $mood_board,
            'message'   => 'Mood board saved successfully!'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateMoodBoardContent(MoodBoardUpdateRequest $request)
    {
        DB::beginTransaction();

        try {
            // Old mood board and mood_board_images data for detaching
            $old_mood_board = MoodBoard::with('mood_board_images')->find($request->mood_board_id);

            // Push to array the old mood_board_images id's
            $old_mood_board_image_id = [];
            foreach($old_mood_board->mood_board_images as $old) {
                array_push($old_mood_board_image_id, $old->id);
            }

            // Get the full collection of mood board
            $mood_board = MoodBoard::find($request->mood_board_id);

            // Detach for updating existing plus new entries
            $mood_board->mood_board_images()->detach($old_mood_board_image_id);

            $mood_board->name     =  $request->name;
            $mood_board->version  =  intval($mood_board->version) + 1;
            $mood_board->save();

            // Attach for updating existing plus new entries
            foreach($request->moodBoardImg as $moodBoardImg) {
                $mood_board->mood_board_images()->attach($moodBoardImg['mood_board_image_id'], 
                    [
                        'x' => $moodBoardImg['x'],
                        'y' => $moodBoardImg['y'],
                        'w' => $moodBoardImg['w'],
                        'h' => $moodBoardImg['h']
                    ]
                );
            }

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
            'data'      => $mood_board,
            'message'   => 'Mood board updated successfully!'
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
