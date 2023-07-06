<?php

namespace App\Http\Controllers;

use App\Http\Resources\TemplateBoardResource;
use App\Http\Resources\TemplateResource;
use App\Models\MoodBoard;
use App\Models\MoodBoardVersion;
use App\Models\Template;
use App\Models\TemplateBoard;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Auth;

class TemplateController extends Controller
{
    //
    public function index(){
        $templates = TemplateResource::collection(Template::orderBy('id','desc')->get());

        // $templates = Template::with('boards')->orderBy('id','desc')->get();
        return $templates;
    }

    public function get($id){
        $template = new TemplateResource(Template::findOrFail($id)->first());

        return $template;
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'name'=>'required|unique:templates'
        ]);

        if($validator->fails()){
            return response()->json(['error'=>$validator->messages()]);
        }   
        DB::beginTransaction();
        try{
            $template = new Template;
            $template->name = $request->name;
            $template->tags = $request->tags?json_encode($request->tags):null;
            // $template->boards = $request->boards?json_encode($request->boards):null;
            if($template->save()){
                if($request->boards){
                    // $this->admod_template_board($request->boards);
                    foreach($request->boards as $board){
                        $exist = 0;
                        $template_boards = TemplateBoard::all();
                        foreach($template_boards as $tb){
                            if($board == $tb->name){
                                $exist++;
                            }
                        }
                        if($exist==0){
                            $template_board = new TemplateBoard;
                            $template_board->template_id = $template->id;
                            $template_board->name = $board;
                            $template_board->save();
                        }
                        // $template_board->template_id = $template->id;
                        // $template_board->mood_board_id = $board['id'];
                        // $template_board->save();
                    }
                }
                DB::commit();
                return response()->json(['message'=>'Template added successfully.'],201);
            }
        }catch(Exception $e){
            DB::rollBack();

            return response()->json(['error'=>$e->getMessage()],400);
        }
    }

    function update(Request $request,$id){
        $validator = Validator::make($request->all(),[
            // 'name'=>'required|unique:templates'
            'name'=>'required'
        ]);

        if($validator->fails()){
            return response()->json(['error'=>$validator->messages()]);
        }  
        
        $template = Template::findOrFail($id);
        DB::beginTransaction();
        try{
            $template->name = $request->name;
            $template->tags = $request->tags?json_encode($request->tags):null;
            // $template->boards = $request->boards?json_encode($request->boards):null;
            if($template->update()){
                if($request->boards){
                    $template_boards =TemplateBoardResource::collection(TemplateBoard::where('template_id',$id)->get());

                    foreach($request->boards as $board){
                        // check if board exist in db add if not
                        $exist = 0;
                        foreach($template_boards as $t_board){
                            if($board == $t_board->name){
                                $exist++;
                            }
                        }
            
                        if($exist == 0){
                            try{
                                $template_board = new TemplateBoard;
                                $template_board->template_id = $id;
                                $template_board->name = $board;
                                $template_board->save();
                            }catch(Exception $e){
                                DB::rollBack();
                                return response()->json(['error'=>$e->getMessage()]);
                            }
                        }
                    }
            
                    foreach($template_boards as $t_board){
                        // check if board exist in payload, delete if not
                        $exist = 0;
                        foreach($request->boards as $board){
                            if($t_board->name == $board){
                                $exist++;
                            }
                        }
            
                        if($exist==0){
                            $template_board = TemplateBoard::find($t_board->id);
                            $template_board->delete();
                        }
                    }
                    // return $template_boards;
                    // $this->update_boards($request->boards,$template_boards,$id);
                }

                DB::commit();                
                return response()->json(['message'=>'Template updated successfully.'],200);
            }
        }catch(Exception $e){
            DB::rollBack();
            return response()->json(['error'=>$e->getMessage()],400);
        }
    }
    
    function update_boards($boards,$template_boards=[],$template_id){
        // dump($boards,$template_boards,$template_id);
        foreach($boards as $board){
            // check if board exist in db add if not
            $exist = 0;
            foreach($template_boards as $t_board){
                if($board['id'] == $t_board->mood_board_id){
                    $exist++;
                }
            }

            if($exist == 0){
                $template_board = new TemplateBoard;
                $template_board->template_id = $template_id;
                $template_board->mood_board_id = $board->id;
                $template_board->save();
            }
        }

        foreach($template_boards as $t_board){
            // check if board exist in payload, delete if not
            $exist = 0;
            foreach($boards as $board){
                if($t_board->mood_board_id == $board['id']){
                    $exist++;
                }
            }

            if($exist>0){
                $template_board = TemplateBoard::find($t_board->id);
                $template_board->delete();
            }
        }
    }
    
    public function delete($id){
        $template = Template::findOrFail($id);
        $template->delete();

        return response()->json(['message'=>'Template deleted successfully.'],200);
    }

    // user

    public function create_board_using_template(Request $request){
        // $template = Template::with('boards')->findOrFail($request->template_id)->get();
        $template_boards = TemplateBoard::where('template_id',$request->template_id)->get();
        
        if($template_boards){
            try{
                DB::beginTransaction();
                foreach($template_boards as $tb){
                    $mood_board = new MoodBoard;
                    $mood_board->name = $tb->name;
                    $mood_board->user_id = Auth::user()->id;
                    $mood_board->frame_color = '#ffffff';
                    if($mood_board->save()){
                        $new_mb_version = new MoodBoardVersion;
                        $new_mb_version->mood_board_id  = $mood_board->id;
                        $new_mb_version->version        = 1;
                        $new_mb_version->remarks        = null;
                        $new_mb_version->status         = 'draft';
                        $new_mb_version->save();
                    }
                }
                DB::commit();
                return response()->json(['message'=>'Mood boards created successfully.'],201);
            }catch(Exception $e){
                DB::rollBack();

                return response()->json(['error'=>$e->getMessage()],400);
            }
        }
    }
}
