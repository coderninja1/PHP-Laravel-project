<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use DB;
class Organiser extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignale.
     
     * @var array
     */
    protected $fillable = [
        'name','username','email','password','age','gender','photo','ip',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password','remember_token',
    ];

   public static  function  login($email){
      
        return  Organiser::where('email','=', $email)->first();

    }

    public static function oid($oemail){
         
           return Organiser::where('email','=',$oemail)->first();   
    }

    public static function show($oemail){
       
        return Organiser::where('email', '=' ,$oemail)->first();
    }
   
   public static function showorganiser($id){
    
    return Organiser::where('id','=',$id)->first();
   }

   public static function allorganiser(){
     
     return Organiser::paginate(3);
   }
  
   public static function deleteorganiser($id){
    
    return Organiser::where('id','=',$id)->delete();
   }

   public static function exist($email){
    return Organiser::where('email','=',$email)->first();
   }

   public static function neworganiser($name,$username,$email,$password,$age,$dob,$gender,$filename,$ip){
    
                  $organiser = new Organiser();
                  $organiser->name=$name;
                  $organiser->username=$username;
                  $organiser->email=$email;
                  $organiser->password=$password;
                  $organiser->age=$age;
                  $organiser->date_of_birth=$dob;
                  $organiser->gender=$gender;
                  $organiser->photo=$filename;
                  $organiser->ip=$ip;
                  $organiser->save();
   }

public static function creategoogle($name,$email,$facebook_id,$photo){
     return DB::table('Organisers')->insert(
               ['name'=>$name,'email'=>$email,'facebook_id'=>$facebook_id,'photo'=>$photo]
             );
}
   public static function updatepassword($id,$password){
    return Organiser::where('remember_token','=',$id)->update(['password'=>$password]);
   }
   public static function nameupdate($oemail,$rename)
   {
    return Organiser::where('email','=',$oemail)->update(['name'=>$rename]);
   }
   public static function usernameupdate($oemail,$reusername)
   {
    return Organiser::where('email','=',$oemail)->update(['username'=>$reusername]);
   }

   public static function passwordupdate($oemail,$newpassword){
     return Organiser::where('email','=',$oemail)->update(['password'=>$newpassword]);
   }

   public static function token($token,$id){
    return Organiser::where('id','=',$id)->update(['remember_token'=>$token]);
   }
   public static function tokenremove($id){
    return Organiser::where('remember_token','=',$id)->update(['remember_token'=>" "]);
   }
  
  public static function settingprofile($email,$name){
    return Organiser::where('email','=',$email)->update(['name'=>$name]);
  }
  public static function checkpassword($email,$password){
    return Organiser::where('email','=',$email)->update(['password'=>$password]);
  }
}
