<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Pagination\Paginator;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Event extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_name','event_description','lad','lng','address','total_members','duration','last_date','time','o_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];
    public static function show($eid){
        
        return Event::where('id','=',$eid)->first();
    }
    public static function adminshowevent($oid){
        return Event::where('o_id','=',$oid)->get();
    }
    public static function showevent($oid,$selected){
        // $currentPage = $currentPage;
       
        //   Paginator::currentPageResolver(function() use ($currentPage) {
        //       return $currentPage;
        //   });

         
        return Event::where('o_id','=',$oid)->orderBy($selected)->paginate(20);
    }
    
    public static function activeevent($oid,$selected){
        return Event::where([['o_id','=',$oid],['last_date','>',date('Y-m-d')]])->orderBy($selected)->paginate(20);
    }
    public static function activeeventsearch($oid,$search,$searcheventselected){
      return Event::where([['o_id','=',$oid],['last_date','>',date('Y-m-d')]])
                  ->where(function($query) use($search) {
                   return $query->where('event_name','like','%'.$search.'%')->orWhere('event_description','like','%'.$search.'%')->orWhere('address','like','%'.$search.'%');
                       })
            ->orderBy($searcheventselected)->paginate(20);
    }
    public static function completeeventsearch($oid,$search,$searcheventselected){
      return Event::where([['o_id','=',$oid],['last_date','<=',date('Y-m-d')]])
                  ->where(function($query) use($search) {
                   return $query->where('event_name','like','%'.$search.'%')->orWhere('event_description','like','%'.$search.'%')->orWhere('address','like','%'.$search.'%');
                       })
            ->orderBy($searcheventselected)->paginate(20);
    }
    public static function activeeventcount($oid){
        return Event::where([['o_id','=',$oid],['last_date','>',date('Y-m-d')]])->get();
    }
    public static function searchactiveeventcount($oid,$search){
      return Event::where([['o_id','=',$oid],['last_date','>',date('Y-m-d')]])
                  ->where(function($query) use($search) {
                   return $query->where('event_name','like','%'.$search.'%')->orWhere('event_description','like','%'.$search.'%')->orWhere('address','like','%'.$search.'%');
                       })
            ->get();
    }
    public static function completedevent($oid,$completeselected){
        return Event::where([['o_id','=',$oid],['last_date','<=',date('Y-m-d')]])->orderBy($completeselected)->paginate(20);
    }
    public static function completedeventcount($oid){
         return Event::where([['o_id','=',$oid],['last_date','<=',date('Y-m-d')]])->get();
    }
    public static function searchcompletedeventcount($oid,$search){
        return Event::where([['o_id','=',$oid],['last_date','<=',date('Y-m-d')]])
                  ->where(function($query) use($search) {
                   return $query->where('event_name','like','%'.$search.'%')->orWhere('event_description','like','%'.$search.'%')->orWhere('address','like','%'.$search.'%');
                       })
            ->get();
    }  
    public static function deleteevent($id){
        
        return Event::where('id','=',$id)->delete();
    }

    public static function home(){
        
       return Event::where('last_date','>',date('Y-m-d'))->paginate(20);
    }
    public static function homecompleted(){
       return Event::where('last_date','<=',date('Y-m-d'))->paginate(20);
    }
    public static function allevent(){
        
        return Event::all();
    }

    public static function deleteorganiser($id){
        
        return Event::where('o_id','=',$id)->delete();
    }

    public static function newevent($ename,$edescription,$lat,$lon,$address,$etm,$eduration,$lastdte,$lasttime,$id){
        $event = new Event();
        $event->event_name=$ename; 
        $event->event_description=$edescription;
        $event->lad=$lat;
        $event->lng=$lon;
        $event->address=$address;
        $event->total_members=$etm;
        $event->duration=$eduration;
        $event->last_date=$lastdte;
        $event->time=$lasttime;
        $event->o_id=$id;
        $event->save();
        return $event;
        //insert the dates into dates table..
    }

   public static function neweventl($neweventdate){
    return Event::where('created_at','>',$neweventdate)->get();
   }

   public static function randomevent(){
      $event = new Event();
      $event->event_name=str_random(10);
      $event->event_description=str_random(50);
      $event->lad=rand(15,38);
      $event->lng=rand(20,40);
      $event->address=str_random(40);
      $event->total_members=rand(60,250);
      $event->duration=rand(1,5);
      $event->last_date=date('Y-m-d');
      $event->time="13:00";
      $event->o_id=13;
      $event->save();
      return $event;
   }

   public static function eventsearch($oid,$search,$searcheventselected){
       return Event::where('o_id', '=',$oid)
                  ->where(function($query) use($search) {
                   return $query->where('event_name','like','%'.$search.'%')->orWhere('event_description','like','%'.$search.'%')->orWhere('address','like','%'.$search.'%');
                       })
            ->orderBy($searcheventselected)->paginate(20);
   }

   
}
