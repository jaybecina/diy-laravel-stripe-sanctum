<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Auth;

class StoreMediaController extends Controller
{
    //
    public function save_media($img,$module=''){
        // $file = $params['attached_file'];
        $allowedFileTypes = [
            'jpg', 'jpeg', 'png', 'docx', 'pdf', 'xml', 'ppt', 'xls'
        ];

        $imgdata = base64_decode($img);
        $mimetype = $this->getImageMimeType($imgdata);    

        $image = $img; //data to be stored ? default
        if(!$mimetype){
            //if format has data:type?mime;base64
            {
                try 
                {
                    $d = explode(':', substr($img, 0, strpos($img, ';')));
                    if( count($d) > 1 ) 
                    {
                        $d = $d[1];
                    }
                    else 
                    {
                        $err = $this->createErrorMessage("File type is not allowed. Please upload only a 'jpg', 'jpeg', 'png', 'docx', 'pdf', 'xml', 'ppt' or 'xls' file type.");
    
                        return $err;
                    }
                    $mimetype = explode('/', $d)[1];
                }
                catch(Exception $e) 
                {
                    $err = $this->createErrorMessage("File type is not allowed. Please upload only a 'jpg', 'jpeg', 'png', 'docx', 'pdf', 'xml', 'ppt' or 'xls' file type.");
    
                    return $err;
                    // return response()->json(['error'=>$err], 400);
                }
    
            }
            $replace = substr($img, 0, strpos($img, ',')+1); 
      
      
            $image = str_replace($replace, '', $img); 
             
            $image = str_replace(' ', '+', $image); //data to be stored 
        }
        // dd($mimetype);
        if( !in_array($mimetype, $allowedFileTypes) )
        {
            $err = $this->createErrorMessage("File type is not allowed. Please upload only a 'jpg', 'jpeg', 'png', 'docx', 'pdf', 'xml', 'ppt' or 'xls' file type.");

            return $err;
        }
      
        
        
        $user_id = Auth::user()->id;
        $mod_ex = explode("_", $module);
        $mod_pre = "";

        foreach ($mod_ex as $m) {
            $mod_pre .= mb_substr($m, 0, 1);
        }
        
        $file_name = $mod_pre.'_'.$user_id.'-'.Str::random(10).'.'.$mimetype;
        
        $file_path = '/'.$module.'/'.$file_name;
        $str_path  = env('APP_URL').'/storage'.$file_path;
        $public_path = '/public'.$file_path;
        Storage::put($public_path,base64_decode($image));

        return $str_path;
    }

    private function getImageMimeType($imagedata)
    {
        $imagemimetypes = [ 
            "jpeg" => "FFD8", 
            "png" => "89504E470D0A1A0A", 
            "gif" => "474946",
            "bmp" => "424D", 
            "tiff" => "4949",
            "tiff" => "4D4D"
        ];

        foreach ($imagemimetypes as $mime => $hexbytes)
        {
            $bytes = $this->getBytesFromHexString($hexbytes);
            if (substr($imagedata, 0, strlen($bytes)) == $bytes)
            return $mime;
        }
        return NULL;
    }

    private function getBytesFromHexString($hexdata)
    {
        for($count = 0; $count < strlen($hexdata); $count+=2)
            $bytes[] = chr(hexdec(substr($hexdata, $count, 2)));

        return implode($bytes);
    }

    static function createErrorMessage( string $message ) : array
    {
        $err = [
            "err"		=> true,
            "message"	=> $message,
            "data"		=> []
        ];
        return $err;
    }
}
