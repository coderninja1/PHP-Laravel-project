<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
// use App\Http\Requests;
use App\Event_has_invitee;
use App\Organiser;
use App\Invitee;
use App\Event;
use App\Admin;
use App\Dates;
use Input;
use View;
use DB;
class AdminController extends Controller
{
    
    public function index(Request $request)
    {
        
        if($request->session()->has('aemail'))
      {
         return Redirect::to('/admin/home');
      }
      else
      {
         return view('admin.login');
      }

   
   }
    public function logindo(Request $request)
    {
       if($request->session()->has('aemail'))
      {
         return Redirect::to('/admin/home');
      }
       else{
      $email=$request->input('email');
      $password=$request->input('password');
               
               $data = Admin::login($email);
               
                    if($data != null)
                    { 
                     if($password == $data->password)
                     {
                            $request->session()->put('aemail', $email); 
                            return Redirect::to('/admin/home');  
                          
                       
                     }
                     else{
                       $message='Wrong email or password..';
                        return Redirect::to('/admin')->withErrors([$message]);
               }
                }
               else{
                  
                   $message='Wrong email or password..';
                 return Redirect::to('/admin')->withErrors([$message]);
                 }
               }
    }
    public function home(Request $request)
    {
      if($request->session()->has('aemail'))
      {   
         //tab selected...
          $tab = $request->get('tab','1');
         //
         // orderBy by event..
           $activeevent = Event::home();
           $completedevent = Event::homecompleted();
           $allevent = Event::paginate(20);
          
           
          foreach ($activeevent as  $events) {
            $id=$events->o_id;
            $orgname[] = Organiser::showorganiser($id);
              
          }
          foreach ($completedevent as  $events) {
            $id=$events->o_id;
            $orgname[] = Organiser::showorganiser($id);
              
          }
          foreach ($allevent as  $events) {
            $id=$events->o_id;
            $orgname[] = Organiser::showorganiser($id);
              
          }
          

           $aemail=$request->session()->get('aemail');
           $admin = Admin::showadmin($aemail);
           
          
           $allcount = Event::all()->count();  
           $activecount = Event::where('last_date','>',date('Y-m-d'))->count();
           $completedcount = Event::where('last_date','<=',date('Y-m-d'))->count();
           $neweventdate= date("Y-m-d", strtotime( "-8 days" ) );
           $neweventl = Event::neweventl($neweventdate)->count();
           
           $newevent = Event::where('last_date','>=',$neweventdate)->paginate(20);
           foreach ($newevent as  $events) {
            $id=$events->o_id;
            $orgname[] = Organiser::showorganiser($id);
              
          }
      
           return view('admin.home',compact('neweventl','activeevent','activecount','completedevent','completedcount','allevent','allcount','orgname','admin','tab','newevent'));

      }
      else
      {
         return Redirect::to('/admin');
      }

    }
    public function logout(Request $request)
    {
      
     $request->session()->forget('aemail');
      return Redirect::to('/admin');

    }

   
    public function create(Request $request)
    {
        $name=$request->input('name');
        $email=$request->input('email');
        $password=$request->input('password');
        Admin::signup($name,$email,$password); 
        

        //session create..
         $request->session()->put('aemail', $email);
                
                  return Redirect::to('/admin/home');


    }
    public function allevent(Request $request)
    {
        
         if($request->session()->has('aemail'))
      {
         $event = Event::allevent();
         

         foreach ($event as  $events) {
            $id=$events->o_id;
            $organiser[] = Organiser::showorganiser($id);
              
          }
           $aemail=$request->session()->get('aemail');
           $admin = Admin::showadmin($aemail);
          return view('admin.allevent',compact('event','organiser','admin'));

      }
      else
      {
         return Redirect::to('/admin');
      }
    }
    public function allinvitee(Request $request)
    {
        if($request->session()->has('aemail'))
        {
          $invitee = Invitee::allinviteelist();
         
         foreach ($invitee as  $invitees) {
            $id=$invitees->o_id;
               $organiser[] = Organiser::showorganiser($id);
          }
          $aemail=$request->session()->get('aemail');
           $admin = Admin::showadmin($aemail);
          return view('admin.allinvitee',compact('invitee','organiser','admin'));
        }
         else
      {
         return Redirect::to('/admin');
      }
    }
       public function allorganiser(Request $request)
    {
        if($request->session()->has('aemail'))
        {
           $tab = $request->get('tab','1');

           $allorganiser = Organiser::allorganiser();
           $aemail=$request->session()->get('aemail');
           $admin = Admin::showadmin($aemail);

           return view('admin.allorganiser',compact('allorganiser','admin','tab'));
           
        } 
        else
        {
             return Redirect::to('/admin');
        }  

    }
   

    public function eventview(Request $request)
    {
      if($request->session()->has('aemail'))
        {
            $id=$request->get('id');
            $event = Event::show($id);
            $date = Dates::viewdate($id);
            $oid = $event->o_id;
            $organiser = Organiser::showorganiser($oid);


            $aemail=$request->session()->get('aemail');
            $admin = Admin::showadmin($aemail);
            return view('admin.eventview',compact('event','date','organiser','admin'));
        }    

        else
        {
             return Redirect::to('/admin');
        }
    }
    public function listinvitee(Request $request)
    {
      if($request->session()->has('aemail'))
        {
            $aemail=$request->session()->get('aemail');
            $admin = Admin::showadmin($aemail);
           $id=$request->get('id');
           $inviteelist = Event_has_invitee::eid($id);
           $invitee = collect([]);
           if(count($inviteelist)>0)
           {
            foreach ($inviteelist as $inviteelists) {
              $iid = $inviteelists->i_id;
              $invitee[] = Invitee::iid($iid);
          }
            
           return view('admin.listinvitee',compact('invitee','admin')); 
          
           }
          
       
           else
           {
             return view('admin.listinvitee',compact('invitee','admin')); 
           }

           
        }
       else
       {
        return Redirect::to('/admin');
       } 
    }
    public function delete(Request $request)
    {
      $id=$request->get('id');
      Event::deleteevent($id);
      Dates::deleteevent($id);
      Event_has_invitee::deleteevent($id);
     

      return Redirect('/admin/home');
    }
    public function inviteeview(Request $request)
    {
      if($request->session()->has('aemail'))
        {

            $id=$request->get('id');
            $invitee = Invitee::iid($id);
            $aemail=$request->session()->get('aemail');
            $admin = Admin::showadmin($aemail);
            return view('admin.inviteeview',compact('invitee','admin'));
         }
       else{
          return Redirect::to('/admin');
       }   

    }
    public function listevent(Request $request)
    {
      if($request->session()->has('aemail'))
        {
            $aemail=$request->session()->get('aemail');
            $admin = Admin::showadmin($aemail);
          $id=$request->get('id');
          $eventlist = Event_has_invitee::listevent($id);
          $organiser;
          $event = collect([]);
          if(count($eventlist)>0)
          {
           
          foreach ($eventlist as $eventlists) {
            $eid = $eventlists->e_id;
            $event[] = Event::show($eid);
            
          }

          foreach ($event as $events) {
            $oid = $events->o_id;
            $organiser = Organiser::showorganiser($oid);
            
          }
         
          
          return view('admin.listevent',compact('event','organiser','admin'));
          }
          else
          {
            return view('admin.listevent',compact('event','organiser','admin'));
          }
          

        }
      else
      {
        return Redirect::to('/admin');
      }    

    }
    public function inviteedelete(Request $request)
    {
      $id=$request->get('id');
      Invitee::deleteinvitee($id);
      Event_has_invitee::deleteeventhasinvitee($id);
      

      return Redirect('/admin/allinvitee');
    }
    public function organiserview(Request $request)
    {
      if($request->session()->has('aemail'))
        { 
          $id=$request->get('id');
          $organiser = Organiser::showorganiser($id);
          $aemail=$request->session()->get('aemail');
            $admin = Admin::showadmin($aemail);
          return view('admin.organiserview',compact('organiser','admin'));
        }
       else
       {
          return Redirect::to('/admin');
       }  
      
    }
    function totalinvitee(Request $request)
    {
      if($request->session()->has('aemail'))
        {
            $aemail=$request->session()->get('aemail');
            $admin = Admin::showadmin($aemail);
           $id=$request->get('id');
           $invitee = Invitee::showinvitee($id);
          
          
           if(count($invitee)>0)
           {
               return view('admin.totalinvitee',compact('invitee','admin','id'));
           }
           else
           {
            return view('admin.totalinvitee',compact('invitee','admin','id'));
           }

        }
      else
      {
         return Redirect::to('/admin');
      }   
    }
        function totalevent(Request $request)
    {
      if($request->session()->has('aemail'))
        {
          $aemail=$request->session()->get('aemail');
           $admin = Admin::showadmin($aemail);
           $id=$request->get('id');
           $allevent = Event::adminshowevent($id);
           
           if(count($allevent)>0)
           {
               return view('admin.totalevent',compact('allevent','admin','id'));
           }
           else
           {
            return view('admin.totalevent',compact('allevent','admin','id'));
           }

        }
      else
      {
         return Redirect::to('/admin');
      }   
    } 
    function organiserdelete(Request $request)
    {

      $id=$request->get('id');
      Organiser::deleteorganiser($id);
      Event::deleteorganiser($id);
      Invitee::deleteorganiser($id);
      Event_has_invitee::deleteorganiser($id);
     
      return Redirect('/admin/allorganiser');
    }

    public function activeevent(Request $request)
    { 
      if($request->session()->has('aemail'))
        {    $aemail=$request->session()->get('aemail');
            $admin = Admin::showadmin($aemail);
             $id=$request->get('id');
             $event = Event::activeevent($id);
             if(count($event)>0)
                 {
                     return view('admin.totalevent',compact('event','admin','id'));
                 }
                 else
                 {
                  return view('admin.totalevent',compact('event','admin','id'));
                 }
         }
         else
         {
           return Redirect::to('/admin');
         }
    }
    

    public function completedevent(Request $request)
    { 
      if($request->session()->has('aemail'))
        {    $aemail=$request->session()->get('aemail');
            $admin = Admin::showadmin($aemail);
             $id=$request->get('id');
             $event = Event::completedevent($id);
             if(count($event)>0)
                 {
                     return view('admin.totalevent',compact('event','admin','id'));
                 }
                 else
                 {
                  return view('admin.totalevent',compact('event','admin','id'));
                 }
         }
         else
         {
           return Redirect::to('/admin');
         }
    }

    public function recent(Request $request)
    {
      if($request->session()->has('aemail'))
        {    
             $dt = date("Y-m-d");
             $recdate= date( "Y-m-d", strtotime( "$dt -7 day" ) );
             $aemail=$request->session()->get('aemail');
             $admin = Admin::showadmin($aemail);
             $id=$request->get('id');
             $invitee = Invitee::recent($recdate,$id);
             if(count($invitee)>0)
           {
               return view('admin.totalinvitee',compact('invitee','admin','id'));
           }
           else
           {
            return view('admin.totalinvitee',compact('invitee','admin','id'));
           }
            
        }
        else
        {
          return Redirect::to('/admin');
        }
    }
}