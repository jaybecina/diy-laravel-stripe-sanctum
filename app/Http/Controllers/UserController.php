<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\StoreMediaController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Auth\RegisterRequest;

class UserController extends Controller
{
    //
    public function list(){
        $users = User::orderBy('id','desc')->get();
        return $users;
    }

    public function get_user(){
        $user = new UserResource(Auth::user());
        // $user = Auth::user()->roles;
        return $user;
    }

    public function update(Request $request, $id){
        try{
            DB::beginTransaction();

            $user = User::findOrFail($id);
            $user->first_name   = $request->firstname;
            $user->last_name    = $request->lastname;
            $user->mobile_num   = $request->mobile_num;
            $user->address      = $request->address;
            if($user->email != $request->email){
                $user->email    = $request->email;
            }

            if($request->image){
                if($user->profile_image){ //remove existing image
                    $app_url = env('APP_URL');
                    $img_str_path = str_replace($app_url,public_path(),$user->profile_image);
                    if(File::exists($img_str_path)){
                        File::delete($img_str_path);
                    }
                }
                
                $store_media = new StoreMediaController;
                $img_path = $store_media->save_media($request->image,'profile_images');
                if(is_array($img_path)){
                    return response()->json(['error'=>$img_path['message']],400);
                }
                $user->profile_image = $img_path;
            }else{
                $user->profile_image = null;
            }
            
            if($user->update()){
                DB::commit();
                return response()->json(['message' => 'User updated successfully.'], 200);
            }

        }catch(Exception $e){
            DB::rollBack();
            return response(['error' => $e->getMessage()], 400);
        }
    }
}
