<?php

namespace App\Http\Controllers;

use App\Http\Resources\MoodBoardBackgroundResource;
use App\Models\MoodBoardBackground;
use App\Http\Controllers\StoreMediaController;
use Illuminate\Support\Facades\File;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MoodBoardBackgroundController extends Controller
{
    //
    public function index(){
        $mb_backgrounds = MoodBoardBackgroundResource::collection(MoodBoardBackground::orderBy('id','desc')->get());

        return $mb_backgrounds;
    }

    public function create(Request $request){
        DB::beginTransaction();

        try{

            if($request->image){
                $store_media = new StoreMediaController;
                $img_path = $store_media->save_media($request->image,'mood_board_backgrounds');
                if(is_array($img_path)){
                    return response()->json(['error'=>$img_path['message']],400);
                }
            }else{
                $img_path = null;
            }
            
            $new_background = new MoodBoardBackground;
            $new_background->name = $request->name;
            $new_background->tags = $request->tags?json_encode($request->tags):null;
            $new_background->image = $img_path;

            if($new_background->save()){
                DB::commit();

                return response()->json(['message'=>'Background added successfully.'],201);
            }
        }catch(Exception $e){
            DB::rollBack();

            return response()->json(['error'=>$e->getMessage()],400);
        }
    }

    public function get($id){
        $background = new MoodBoardBackgroundResource(MoodBoardBackground::findOrFail($id));

        return $background;
    }

    public function update(Request $request, $id){
        try{
            DB::beginTransaction();

            $background = MoodBoardBackground::findOrFail($id);
            $background->name = $request->name;
            $background->tags = $request->tags?json_encode($request->tags):null;
            
            if($request->image){
                if($background->image){ //remove existing image 
                    $app_url = env('APP_URL');
                    $img_str_path = str_replace($app_url,public_path(),$background->image);
                    if(File::exists($img_str_path)){
                        File::delete($img_str_path);
                    }
                }

                $store_media = new StoreMediaController;
                $img_path = $store_media->save_media($request->image,'mood_board_backgrounds');
                if(is_array($img_path)){
                    return response()->json(['error'=>$img_path['message']],400);
                }
                $background->image = $img_path;
            }

            if($background->update()){
                DB::commit();

                return response()->json(['message' => 'Background updated successfully.'], 200);
            }
        }catch(Exception $e){
            DB::rollBack();
            return response(['error' => $e->getMessage()], 400);
        }
    }

    public function delete($id){
        $background = MoodBoardBackground::findOrFail($id);
        $background->delete();

        return response()->json(['message' => 'Background deleted successfully.'], 200);
    }
}
