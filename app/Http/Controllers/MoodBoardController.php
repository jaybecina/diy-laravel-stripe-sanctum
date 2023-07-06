<?php

namespace App\Http\Controllers;

use App\Http\Resources\MoodBoardResource;
use App\Models\MoodBoard;
use App\Http\Resources\MoodBoardItemResource;
use App\Models\MoodBoardItem;
use App\Models\MoodBoardVersion;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\StoreMediaController;
use App\Http\Resources\MoodBoardVersionItemResource;
use App\Models\MoodBoardVersionItem;
use Illuminate\Support\Facades\File;

class MoodBoardController extends Controller
{
    //
    public function mood_board_list(){
        $mb = MoodBoardResource::collection(MoodBoard::orderBy('id','desc')->get());

        return $mb;
    }

    public function get_board($id){
        $mood_board = new MoodBoardResource(MoodBoard::findOrFail($id));

        return $mood_board;
    }   

    public function create_mood_board(Request $request){
        $user_id = Auth::user()->id;
        $name = $request->name;
        
        DB::beginTransaction();
        
        if($request->frame_background){
            $store_media = new StoreMediaController;
            $img_path = $store_media->save_media($request->frame_background,'mood_board');
            if(is_array($img_path)){
                return response()->json(['error'=>$img_path['message']],400);
            }
        }else{
            $img_path = "";
        }
        
        try{
            $mb = new MoodBoard;
            $mb->name               = $name;
            $mb->user_id            = $user_id;
            $mb->frame_color        = $request->frame_color;
            $mb->frame_background   = $img_path;
            $mb->image_placeholder  = $request->image_placeholder;

            if($mb->save()){
                //create mb version 1
                $new_mb_version = new MoodBoardVersion;
                $new_mb_version->mood_board_id  = $mb->id;
                $new_mb_version->version        = 1;
                $new_mb_version->remarks        = null;
                $new_mb_version->status         = 'draft';

                if($new_mb_version->save()){
                    DB::commit();
                    return response()->json(['message'=>'Moodboard successfully created'],201);
                }
            }
        }catch(Exception $e){
            DB::rollBack();
            return response()->json(["message"=>$e->getMessage()],400);
        }
        
    }
    
    //mood board item

    public function mood_board_item_list(){
        $mb_items = MoodBoardItemResource::collection(MoodBoardItem::orderBy('id','desc')->get());

        return $mb_items;
    }

    public function get_item($id){
        $mb_item = new MoodBoardItemResource(MoodBoardItem::findOrFail($id));

        return $mb_item;
    }

    // mood board versioning
    
    public function create_version(Request $request){
        DB::beginTransaction();
        try{
            $new_mb_version = new MoodBoardVersion;
            $new_mb_version->mood_board_id  = $request->mood_board_id;
            $new_mb_version->version        = $request->version;
            $new_mb_version->remarks        = $request->remarks;
            $new_mb_version->status         = $request->status;
            
            if($new_mb_version->save()){
                DB::commit();
                return response()->json(['message'=>'Moodboard version successfully created'],201);
            }

        }
        catch(Exception $e){
            DB::rollBack();
            return response()->json(["message"=>$e->getMessage()],400);
        }
    }

    // version items

    public function get_version_item($id=null){
        if($id==null){
            $mbv_items = MoodBoardVersionItemResource::collection(MoodBoardVersionItem::orderBy('id','desc')->get());
        }else{
            $mbv_items = new MoodBoardVersionItemResource(MoodBoardVersionItem::findOrFail($id));
        }
        return $mbv_items;
    }
    
    public function version_items_update(Request $request){
        $v_items = $request->mood_board_version_items;
        
        if(count($v_items) == 0){
            return response()->json(['error'=> "No items were found." ],400);
        }

        try{
            DB::beginTransaction();

            foreach($v_items as $item){
                $mbv_item = MoodBoardVersionItem::find($item['id']);
                $mbv_item->x = $item['x'];
                $mbv_item->y = $item['y'];
                $mbv_item->w = $item['w'];
                $mbv_item->h = $item['h'];
                $mbv_item->index = $item['index'];
                $mbv_item->update();
            }

            DB::commit();
            return response()->json(['message'=>'Moodboard items successfully updated!'],201);
        }catch(Exception $e){
            DB::rollBack();
            return response()->json(["message"=>$e->getMessage()],400);
        }
    }

    public function version_add_item(Request $request){
        DB::beginTransaction();
        try{
            $index = count(MoodBoardVersionItem::where('mood_board_version_id',$request->mood_board_version_id)->get());
            $mbv_item = new MoodBoardVersionItem;
            $mbv_item->mood_board_version_id    = $request->mood_board_version_id;
            $mbv_item->mood_board_item_id       = $request->mood_board_item_id;
            if($request->x){
                $mbv_item->x                        = $request->x;
            }
            if($request->y){
                $mbv_item->y                        = $request->y;
            }
            if($request->h){
                $mbv_item->h                        = $request->h;
            }
            if($request->w){
                $mbv_item->w                        = $request->w;
            }
            $mbv_item->index                    = $index;
            $mbv_item->remarks                  = $request->remarks;
            if($mbv_item->save()){
                DB::commit();
                return response()->json(['message'=>'Board Version item successfully created'],201);
            }
        }catch(Exception $e){
            DB::rollBack();
            return response()->json(["message"=>$e->getMessage()],400);
        }
    }

    public function update_inspiration_picture(Request $request){
        $mb = MoodBoard::findOrFail($request->mood_board_id);

        try{
            if($request->image){
                if($mb->inspiration_picture){ //remove existing image 
                    $app_url = env('APP_URL');
                    $img_str_path = str_replace($app_url,public_path(),$mb->inspiration_picture);
                    if(File::exists($img_str_path)){
                        File::delete($img_str_path);
                    }
                }
    
                $store_media = new StoreMediaController;
                $img_path = $store_media->save_media($request->image,'mood_board_inspiration_pictures');
                if(is_array($img_path)){
                    return response()->json(['error'=>$img_path['message']],400);
                }
                $mb->inspiration_picture = $img_path;
            }else{
                if($mb->inspiration_picture){
                    $app_url = env('APP_URL');
                    $img_str_path = str_replace($app_url,public_path(),$mb->inspiration_picture);
                    if(File::exists($img_str_path)){
                        File::delete($img_str_path);
                    }
                }
                $mb->inspiration_picture = null;
            }

            if($mb->update()){
                DB::commit();

                return response()->json(['message' => 'Inspiration picture updated successfully.'], 200);
            }
        }catch(Exception $e){
            DB::rollBack();

            return response(['error' => $e->getMessage()], 400);
        }
    }
    
}
