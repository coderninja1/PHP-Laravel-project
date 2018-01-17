<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;
class Event_has_invitee extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'i_id','e_id','o_id','ans','reason',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    public static function eid($id){
        
        return Event_has_invitee::where('e_id','=',$id)->get(); 
    }

    public static function attend($id){
       
        return Event_has_invitee::where([['ans','=',1],['e_id','=',$id]])->get();
    }

     public static function noattend($id){
        
        return Event_has_invitee::where([['ans','=',2],['e_id','=',$id]])->get();
    }

     public static function noresponse($id){
        
        return Event_has_invitee::where([['ans','=',0],['e_id','=',$id]])->get();
    }

    public static function deleteeventhasinvitee($id){
        
        return Event_has_invitee::where('i_id','=',$id)->delete();
    }

    public static function confirm($id,$ret){
       

       Event_has_invitee::where('id','=',$id )->update(['ans' =>$ret]);
    }

    public static function deleteevent($id){
       
        return Event_has_invitee::where('e_id','=',$id)->delete();
    }

    public static function listevent($id){
        
        return Event_has_invitee::where('i_id','=',$id)->get();
    }

    public static function deleteorganiser($id){
        
        return Event_has_invitee::where('o_id','=',$id)->delete();
    }

    public static function newinvitation($eventid,$datasid,$id){

        return DB::table('event_has_invitees')->insert(
               ['e_id'=>$eventid,'i_id'=>$datasid,'o_id'=>$id]
             );
    }

    public static function reason($id,$reason){
        return Event_has_invitee::where('id','=',$id)->update(['ans'=>2,'reason'=>$reason]);
    }
}
