<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redirect;
use App\Mail\forgot;
// use App\Http\Requests;
use Carbon\Carbon; 
use App\Organiser;
use App\Event_has_invitee;
use App\Invitee;
use App\Event;
use Input;
use View;
use DB;
use Validator;
use Mail;
use Datatables\Datatables;
class OrganiserController extends Controller
{
    
    public function index(Request $request)
    {

      $images = Input::file('imageupload');

      $destinationPath = public_path().'/imagesupload/';
      $filename = $images->getClientOriginalName();
      $images->move($destinationPath, $filename);

      $name = $request->input('name');
      $username=$request->input('username');
      $email=$request->input('email');
      $password=$request->input('password');
      $dob=$request->input('dob');
      $gender=$request->input('gender');

     
            //check..
            
            $mytime = Carbon::now();
            $date1=date_create($mytime);
            $date2=date_create($dob);
            $diff=date_diff($date1,$date2);
            $age =$diff->y;
            /*echo $mytime->toDateString()."<br>";
            echo $age->diff($mytime)->y;*/
            
            //check...
             $host= gethostname();
             $ip = gethostbyname($host);
             
                      
             //check email exist or not...
             $exist = Organiser::exist($email);

             if(count($exist)>0)
             {
                        $message='Email Id already Exist...';
                        return Redirect::to('/signup')->withErrors([$message]);
             }
             else
             {
                  Organiser::neworganiser($name,$username,$email,$password,$age,$dob,$gender,$filename,$ip);

                  
  
                 //redirect after inserting table into home page
                  $request->session()->put('email', $email);
                             //session for sort...
                             $selected='event_name';
                             $request->session()->put('selected',$selected);
                             //activeeventsession...
                             $activeselected='event_name';
                             $request->session()->put('activeselected',$activeselected); 
                            
                            //completedeventsession...
                             $completeselected='event_name';
                             $request->session()->put('completeselected',$completeselected);
                            //sesssion for sort...
                             $inviteeselected='name';
                             $request->session()->put('inviteeselected',$inviteeselected);
                             $searchinviteeselected='name';
                             $request->session()->put('searchinviteeselected',$searchinviteeselected); 
                            //eventsearchsession...
                            $searcheventselected='event_name';  
                            $request->session()->put('searcheventselected',$searcheventselected);

                            //activeeventsession...
                             $searchactiveselected='event_name';
                             $request->session()->put('searchactiveselected',$searchactiveselected);
                             //activeeventsession...
                             $searchcompleteselected='event_name';
                             $request->session()->put('searchcompleteselected',$searchcompleteselected);

                  return Redirect::to('activeevent');   
             }  
      

    }
    function confirm(Request $request)
    {
      $id=$request->get('id');
      $ret = $request->get('ret');
      if($ret == 1)
      {
        echo "<h1>Thanks for accept my invitation...</h1>";
      }
      else
      {
        echo "<h1>Thanks for response of my invitation...</h1>";
      }

      Event_has_invitee::confirm($id,$ret);
    }

    
   
    public function login(Request $request)
    {
     if($request->session()->has('email'))
      {
         return Redirect::to('/invitee');
      }
       else{
              $email=$request->input('email');
              $password=$request->input('password');
              
                $organiser =Organiser::login($email);//call the model and retrieve the data.....(Organiser)

                 

                    if($organiser != null)
                    { 
                     if($password == $organiser->password)
                     {
                            $request->session()->put('email', $email);
                           
                            //session for sort...
                             $selected='event_name';
                             $request->session()->put('selected',$selected);
                             //activeeventsession...
                             $activeselected='event_name';
                             $request->session()->put('activeselected',$activeselected); 
                            
                            //completedeventsession...
                             $completeselected='event_name';
                             $request->session()->put('completeselected',$completeselected);
                            //sesssion for sort...
                             $inviteeselected='name';
                             $request->session()->put('inviteeselected',$inviteeselected);
                             $searchinviteeselected='name';
                             $request->session()->put('searchinviteeselected',$searchinviteeselected); 
                            //eventsearchsession...
                            $searcheventselected='event_name';  
                            $request->session()->put('searcheventselected',$searcheventselected);

                            //activeeventsession...
                             $searchactiveselected='event_name';
                             $request->session()->put('searchactiveselected',$searchactiveselected);
                             //activeeventsession...
                             $searchcompleteselected='event_name';
                             $request->session()->put('searchcompleteselected',$searchcompleteselected);

                            return Redirect::to('/activeevent');
                            
                       
                     }
                     else{
                       $message='Wrong email or password..';
                        return Redirect::to('/login')->withErrors([$message]);
               }
                }
               else{
                  
                   $message='Wrong email or password..';
                 return Redirect::to('/login')->withErrors([$message]);
                 }
               }
    }

    public function forgot(Request $request)
    {
     $email=$request->input('email');
     if($email == "")
     {
                        $message='Please Fill Email Text';
                        return Redirect::to('/forgot')->withErrors([$message]);
     }
     else
     {
       $organiser =Organiser::login($email);
       if($organiser == null)
       {
               $message='Email Id is invalid';
               return Redirect::to('/forgot')->withErrors([$message]);

               
       }
       else
       {
            $token = str_random(16);

           $id = $organiser->id;
            Organiser::token($token,$id);
        

           Mail::to($email)->send(new forgot($email));
           
            $request->session()->put('token', $token); 
           echo "<a href=".e(url('/')).">  <button type='button' class='btn'  style='background-color:#3B5998;color:white;height:40px;width:150px' ><b> Login</b></button></a><center><br><br><br><br><br><br><br>
               <h1>Reset Your RSVP Password</h1>
               <h4>Check your mail for reset password.  send the link for reset password...</h4>
                     </center>";
       }
     }
    }
    public function newpassword(Request $request)
    {
       if($request->session()->has('token'))
      {
          $id = $request->get('id');
          $token = $request->session()->get('token');
          if($token === $id)
          {
           return view('newpassword',compact('id'));
          }
          else
          {
            echo "<br><br><br><br><br><br><center><h2><b>Reset Your Trello Password</b></h2></4><i>It looks like this password reset link can't be used anymore. This probably means that you sent us another password reset request; in that case you'll need to use the newer reset link.<i></4></center>

            ";
          }
      }
       else
        {
             return Redirect::to('/');
        }  
    }
    public function passwordupdate(Request $request)
    {
      if($request->session()->has('token'))
      {
                $id = $request->input('id');

                $password = $request->input('password');
                $rpassword = $request->input('rpassword');
                if($password === $rpassword)
                {

                  Organiser::updatepassword($id,$password);
                  Organiser::tokenremove($id);
                  $request->session()->forget('token');
                  echo "<br><br><br><br><br><center>
                  <h3>Your password has been successfully changed.</h3> <a href=".e(url('/')).">Click here to return to the login page.</a></center>
                  ";

                }
                else
                {
                   $message='Password Doesn\'t match ' ;
                   return Redirect::to('/newpassword')->withErrors([$message]);
                }
        }
        else
        {
             return Redirect::to('/');
        }      
    }
    public function home(Request $request)
    { 
      
      if($request->session()->has('email'))
      {

           $oemail=$request->session()->get('email');

           $organiser =Organiser::oid($oemail);//call the model and retrieve the oid....(Organiser)
           $oid=$organiser->id;
           $name=$organiser->name;
           $inviteeselected = $request->session()->get('inviteeselected');
           $allinvitee =Invitee::allInvitee($oid,$inviteeselected);
                     
           return view('home',compact('allinvitee','name','inviteeselected'));
      }
      else
      {
         return Redirect::to('/');
      }

    }

    public function logout(Request $request)
    {
      
     $request->session()->forget('email');
      return Redirect::to('/');

    }

    public function newevent(Request $request)
    {
      if($request->session()->has('email'))
      {
           $oemail=$request->session()->get('email');
        $organiser = Organiser::show($oemail);
        $name = $organiser->name;  
            return view('newevent',compact('name'));
      }
      else
      {
        return Redirect::to('/login');
      }
    }

    public function noattend(Request $request)
      {
         $id=$request->get('id');
         
          return view('noattend',compact('id'));
          
      }
  public function setting(Request $request)
  {
     if($request->session()->has('email'))
      {
        $oemail=$request->session()->get('email');
        $organiser = Organiser::show($oemail);
        $name=$organiser->name;
         
            return view('setting',compact('organiser','name'));
      }
      else
      {
        return Redirect::to('/login');
      }
  }

public function nameupdate(Request $request)
{
  if($request->session()->has('email'))
      {
        $rename = $request->input('rename');
        $oemail=$request->session()->get('email');
        $organiser = Organiser::nameupdate($oemail,$rename);
        
       return  Redirect::to('/setting');
      }
      else
      {
        return Redirect::to('/login');
      }
}
public function usernameupdate(Request $request)
{
  if($request->session()->has('email'))
      {
        $reusername = $request->input('reusername');
        $oemail=$request->session()->get('email');
        $organiser = Organiser::usernameupdate($oemail,$reusername);
        
       return  Redirect::to('/setting');
      }
      else
      {
        return Redirect::to('/login');
      }
}

public function updatepassword(Request $request)
{
  if($request->session()->has('email'))
      {
        $cpassword = $request->input('current_password');
        $newpassword = $request->input('new_password');
        $oemail=$request->session()->get('email');
        $organiser = Organiser::passwordupdate($oemail,$newpassword);
       return  Redirect::to('/setting');
      }
      else
      {
        return Redirect::to('/login');
      }
}


  public function settingprofile(Request $request)
   {
    if($request->session()->has('email'))
      {
          $email = $request->session()->get('email');
          $name = $request->input('name');
          Organiser::settingprofile($email,$name);
          $message='successfully change your name...';
          return Redirect::to('/setting')->withErrors([$message]);  
      }
    else
     {
          return Redirect::to('/login');
     }    
   }

   public function passwordchange(Request $request)
   {
    if($request->session()->has('email'))
      {
        $organiser=Organiser::show($request->session()->get('email'));
        $name=$organiser->name;
        return view('passwordchange',compact('name'));
      }  
    else
     {
          return Redirect::to('/login');
     } 
   }

   public function checkpassword(Request $request)
   {
    if($request->session()->has('email'))
      {
        $email = $request->session()->get('email');
        $password = $request->input('password');
        Organiser::checkpassword($email,$password);
        return Redirect::to('/logout');
      }
    else
     {
          return Redirect::to('/login');
     }    
   }
}

