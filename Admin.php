<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use Notifiable;
   
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','email','password','photo',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password','remember_token',
    ];

    public static function login($email){
        return Admin::where('email', '=' ,$email)->first();

    }

    public static function showadmin($aemail){
       
        return Admin::where('email','=',$aemail)->first();
    }

    public static function signup($name,$email,$password){
        //insert into table..
       $admin = new Admin();
        $admin->name=$name;
        $admin->email=$email;
        $admin->password=$password;
        $admin->save();
    }
}

  