<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;
class Dates extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'e_id','day','time',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    public static function viewdate($id){
        
        return Dates::where('e_id','=',$id)->get();
    }

    public static function deleteevent($id){
        
        return Dates::where('e_id','=',$id)->delete();
    }
    public static function adddates($date,$time,$eid){
        foreach($date as $key => $dates)
        {

          //insert date...
          DB::table('dates')->insert(
           ['e_id'=>$eid,'day'=>$dates,'time'=>$time[$key]]
         );
          //insert end...
        }    
        
    }

   
    
}
