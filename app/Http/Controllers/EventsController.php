<?php

namespace App\Http\Controllers;

// use App\Http\Controllers\Controller;

use App\Http\Resources\EventResource;
use App\Http\Resources\UserEventResource;
use App\Http\Resources\UserResource;
use App\Models\Event;
use App\Models\User;
use App\Models\UserEvent;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EventsController extends Controller
{
    //
    // public function getEvents($id = null){
    //     if($id){
    //         $user_id = Auth::user()->id;
    //         // dd($id,$user_id);
    //         $event = new UserEventResource(UserEvent::with('event')->where('id',$id)->first());
    //         if($event!=null){
    //             if($event->user_id==$user_id){
    //                 return response()->json($event);
    //             }else{
    //                 return response()->json(['type'=>'error','message' => 'Not authorized.'],403);
    //             }
    //         }else{
    //             return response()->json(['type'=>'error','message'=>'Event not found.'],404);
    //         }
    //     }else{
    //         $events = Event::with('event_type')->orderBy('id','desc')->get();
    //         return response()->json($events);
    //     }
    // }

    public function getEvent($id){
        $event = new EventResource(Event::where('id',$id)->first());

        return $event;
    }

    public function getUserEvents(){
        $user_id = Auth::user()->id;
        
        $user_events = UserEventResource::collection(UserEvent::with('event')->where('user_id',$user_id)->orderBy('id','desc')->get());
        
        return $user_events;
    }

    public function getUserEvent($id){
        $user_event = new UserEventResource(UserEvent::with('event')->findOrFail($id));
        
        return $user_event;
    }

    public function getUpcomingEvents(){
        // query all events where user is not registered
        $user_id = Auth::user()->id;
        $events = EventResource::collection(Event::whereDoesntHave('user_events',function($q) use($user_id){
            $q->where('user_id',$user_id);
        })->get()); 
        
        return $events;
    }

    public function cancelAttendance(Request $request){
        $id = $request->id;
        try{
            $event = UserEvent::where('id',$id)->first();
            DB::beginTransaction();
            $event->is_booked = 0;
            if($event->update()){
                DB::commit();
                $event = new UserEventResource(UserEvent::with('event')->where('id',$id)->first()); 
                
                return response()->json([
                    'message'=>'Successfully canceled the event attendance',
                    'data'=>$event
                ],200);
            }

        }catch(Exception $e){
            DB::rollBack();

            return response()->json(['error'=>$e->getMessage()]);
        }
    }

    public function bookAttendance(Request $request){
        $id = $request->id;
        try{
            $event = UserEvent::where('id',$id)->first();
            DB::beginTransaction();
            $event->is_booked = 1;
            if($event->update()){
                DB::commit();
                $event = new UserEventResource(UserEvent::with('event')->where('id',$id)->first()); 
                
                return response()->json([
                    'message'=>'Successfully booked the event.',
                    'data'=>$event
                ],200);
            }

        }catch(Exception $e){
            DB::rollBack();

            return response()->json(['type'=>'error','message'=>$e]);
        }
    }

    // admin functions
    public function newEvent(Request $request){
        try{
            DB::beginTransaction();
            $event = new Event;
            $event->name=$request->event_name;
            $event->description=$request->description;
            $event->link=$request->event_link;
            $event->event_schedule=$request->event_schedule;
            $event->event_type_id=$request->event_type;
            if($event->save()){
                DB::commit();
                $events = Event::with('event_type')->orderBy('id','desc')->get();
                return response()->json($events);
            }
        }catch(Exception $e){
            DB::rollBack();
            return response()->json(['type'=>'error','message'=>$e]);
        }
    }

    public function fetchParticipants($event_id){
        $user_events = UserEvent::with('user')->where('event_id',$event_id)->orderBy('id','desc')->get();
        return $user_events;
    }

    public function searchParticipants(Request $request){
        $key = $request->key;
        $event_id = $request->event_id;

        if($key=='null'){
            return response()->json([]);
        }
        $users = User::whereHas('roles',function($q){
            $q->where('name','Customer');
        })->whereDoesntHave('user_events',function($q) use($event_id){
            $q->where('event_id',$event_id);
        })
        ->where(function($query) use($key){
            $query->where('first_name', 'LIKE', '%' . $key . '%')
            ->orWhere('last_name', 'LIKE', '%' . $key . '%')
            ->orWhere(DB::raw("CONCAT(`first_name`,' ',`last_name`)"),'LIKE', '%' . $key . '%');
        })->get();
        // if($key!=''){
        //     return $users->where('first_name','LIKE','%'.$key.'%')
        //     ->orWhere('last_name','LIKE','%'.$key.'%');
        //     $users->orderBy('first_name','asc')->get();

        //     // return $users->get();
        // }else{
        //     return 'sheesh';
        // }
        // $users->orderBy('id','desc')->get();

        return $users;
    }

    public function addParticipant(Request $request){
        $user_event = new UserEvent;
        $user_event->event_id = $request->event_id;
        $user_event->user_id = $request->user_id;
        if($user_event->save()){
            $user_events = UserEvent::with('user')->where('event_id',$request->event_id)->get();
            return response()->json($user_events);
        }
    }

    public function removeParticipant(Request $request){
        $user_event = UserEvent::where('id',$request->user_event_id)->first();
        try{
            DB::beginTransaction();
            if($user_event->delete()){
                $user_events = UserEvent::with('user')->where('event_id',$request->event_id)->get();
                DB::commit();
                
                return response()->json($user_events);
            }
        }catch(Exception $e){
            DB::rollBack();
            return response()->json(['type'=>'error','message'=>$e]);
        }
    }
}
