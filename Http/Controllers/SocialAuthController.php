<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Organiser;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Socialite;
use App\User;
use DB;
class SocialAuthController extends Controller
{
     public function redirect()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function callback(Request $request)
    {
        try {
            $user = Socialite::driver('facebook')->user();
        } catch (Exception $e) {
            return redirect('login/facebook');
        }
        
        $authUser = $this->findOrCreateUser($user,$request);
        
        
 
        return redirect('/event');
    }

    /**
     * Return user if exists; create and return if doesn't
     *
     * @param $facebookUser
     * @return User
     */
    private function findOrCreateUser($facebookUser,$request)

    {
        
       $email = $facebookUser->email;
        $authUser = Organiser::where('email','=',$email)->first();

         
        	$request->session()->put('email', $email);
            
        if ($authUser)
        {
            return redirect('/event');
        }
        
        
         Organiser::insert([
            'name' => $facebookUser->name,
            'email' => $facebookUser->email,
            'facebook_id' =>$facebookUser->id,
            'gender'=>$facebookUser->user['gender'],
            'photo' => $facebookUser->avatar
            
        ]);
         
         return redirect('/event');
    }






    //google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $user = Socialite::driver('google')->user();
           
            $email = $user->email;
            $name = $user->name;
           
            $facebook_id = $user->id;
            $photo = $user->avatar;
            $auth = Organiser::where('email','=',$email)->first();
            if($auth)
            {
                  $request->session()->put('email', $email);
            	 return redirect('event');
            }
            else
            {
             Organiser::creategoogle($name,$email,$facebook_id,$photo);

             $request->session()->put('email', $email);
            return redirect('event');
            }
        } catch (Exception $e) {
            return redirect('login/google');
        }
    }
}
