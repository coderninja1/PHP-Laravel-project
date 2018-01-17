<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;
use App\Event_has_invitee;
class Invitee extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','gender','age','email','address','number_of_member',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];


    public static function allInvitee($oid,$inviteeselected){
         
         return Invitee::where('o_id','=',$oid)->orderBy($inviteeselected)->paginate(20);
    }
    public static function allInvitee1($oid){
      return Invitee::where('o_id','=',$oid)->get();
    }
   public static function searchInvitee($oid,$searchinviteeselected,$search){
    return Invitee::where('o_id', '=',$oid)
                  ->where(function($query) use($search) {
                   return $query->where('name','like','%'.$search.'%')->orWhere('email','like','%'.$search.'%')->orWhere('address','like','%'.$search.'%');
                       })
            ->orderBy($searchinviteeselected)->paginate(20);
   }
   
    public static function iid($id){
        
        return Invitee::where('id', '=' ,$id)->first();

    }
   
    public static function inviteeList($inviteeid){
        
        return Invitee::where('id','=',$inviteeid)->first();
    }
    
    public static function recent($recdate,$id){
      return Invitee::where([['o_id','=',$id],['created_at','>=',$recdate.' 00:00:00']])->get();
    } 

    public static function sendinvitation($iid){
       
        return Invitee::where('id', '=' ,$iid)->first();
    }

    public static function deleteinvitee($id){
        
        return Invitee::where('id','=',$id)->delete();
    }

    public static function showinvitee($oid){
       
        return Invitee::where('o_id','=',$oid)->get();
    }

    public static function allinviteelist(){
        
        return Invitee::all();
    }

    public static function deleteorganiser($id){
       
        return Invitee::where('o_id','=',$id)->delete();
    }

    public static function newinvitee($name,$gender,$age,$dob,$email,$address,$nom,$filename,$id){
      $invitee = new Invitee();
      $invitee->name=$name;
      $invitee->gender=$gender;
      $invitee->age=$age;
      $invitee->date_of_birth=$dob;
      $invitee->email=$email;
      $invitee->address=$address;
      $invitee->number_of_member=$nom;
      $invitee->photo=$filename;
      $invitee->o_id=$id;
      $invitee->save();
    }

    public static function noinvited($eid,$oid){
    
      $data = Event_has_invitee::where('e_id','=',$eid)->get();
      if(count($data)>0)
      {
      foreach ($data as $datas) {
          $iid[] = $datas['i_id'];
        }  
        return Invitee::where('o_id','=',$oid)
        ->whereNotIn('id',$iid)
        ->paginate(20);
      }
      else
      {
       return Invitee::where('o_id','=',$oid)->paginate(20); 
      }

    }


  public static function searchnoinvited($eid,$oid,$search){
    
      $data = Event_has_invitee::where('e_id','=',$eid)->get();
      if(count($data)>0)
      {
      foreach ($data as $datas) {
          $iid[] = $datas['i_id'];
        }  
        return Invitee::where('o_id','=',$oid)
        ->whereNotIn('id',$iid)
        ->where(function($query) use($search) {
                   return $query->where('name','like','%'.$search.'%')->orWhere('email','like','%'.$search.'%')->orWhere('address','like','%'.$search.'%');
                       })
        ->paginate(20);
      }
      else
      {
       return Invitee::where('o_id','=',$oid)
       ->where(function($query) use($search) {
                   return $query->where('name','like','%'.$search.'%')->orWhere('email','like','%'.$search.'%')->orWhere('address','like','%'.$search.'%');
                       })
       ->paginate(20); 
      }

    }


    public static function randomevent(){
      $invitee = new Invitee();
      $invitee->name=str_random(7);
      $invitee->gender="female";
      $invitee->age=26;
      $invitee->date_of_birth='1990-06-16';
      $invitee->email=str_random(6)."@g.com";
      $invitee->address=str_random(20);
      $invitee->number_of_member=rand(6,8);
      $invitee->photo="anyone.png";
      $invitee->o_id=13;
      $invitee->save();
      return $invitee;
    }
}
  