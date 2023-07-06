<?php

namespace App\Http\Controllers;

use App\Http\Resources\MoodBoardFrameResource;
use App\Models\MoodBoardFrame;
use Illuminate\Support\Facades\File;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MoodBoardFrameController extends Controller
{
    //
    public function index(){
        $mb_frames = MoodBoardFrameResource::collection(MoodBoardFrame::orderBy('id','desc')->get());

        return $mb_frames;
    }

    public function create(Request $request){
        DB::beginTransaction();

        try{
            if($request->image){
                $store_media = new StoreMediaController;
                $img_path = $store_media->save_media($request->image,'mood_board_frames');
                if(is_array($img_path)){
                    return response()->json(['error'=>$img_path['message']],400);
                }
            }else{
                $img_path = null;
            }
            
            $new_frame = new MoodBoardFrame;
            $new_frame->name = $request->name;
            $new_frame->tags = $request->tags?json_encode($request->tags):null;
            $new_frame->image = $img_path;

            if($new_frame->save()){
                DB::commit();

                return response()->json(['message'=>'Frame added successfully.'],201);
            }
        }catch(Exception $e){
            DB::rollBack();

            return response()->json(['error'=>$e->getMessage()],400);
        }
    }

    public function get($id){
        $frame = new MoodBoardFrameResource(MoodBoardFrame::findOrFail($id));

        return $frame;
    }

    public function update(Request $request, $id){
        try{
            DB::beginTransaction();

            $frame = MoodBoardFrame::findOrFail($id);
            $frame->name = $request->name;
            $frame->tags = $request->tags?json_encode($request->tags):null;
            
            if($request->image){
                if($frame->image){ //remove existing image 
                    $app_url = env('APP_URL');
                    $img_str_path = str_replace($app_url,public_path(),$frame->image);
                    if(File::exists($img_str_path)){
                        File::delete($img_str_path);
                    }
                }

                $store_media = new StoreMediaController;
                $img_path = $store_media->save_media($request->image,'mood_board_frames');
                if(is_array($img_path)){
                    return response()->json(['error'=>$img_path['message']],400);
                }
                $frame->image = $img_path;
            }

            if($frame->update()){
                DB::commit();

                return response()->json(['message' => 'Frame updated successfully.'], 200);
            }
        }catch(Exception $e){
            DB::rollBack();
            return response(['error' => $e->getMessage()], 400);
        }
    }

    public function delete($id){
        $background = MoodBoardFrame::findOrFail($id);
        $background->delete();

        return response()->json(['message' => 'Frame deleted successfully.'], 200);
    }
}
