<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Illuminate\Support\Facades\Redirect;
// use App\Http\Requests;
use App\Organiser;
use App\Event_has_invitee;
use App\Invitee;
use App\Event;
use App\Dates;
use Input;
use View;
use DB;
use Yajra\Datatables\Facades\Datatables;
class EventController extends Controller
{
    
  public function eventshow(Request $request)
  {
    if($request->session()->has('email'))
    {               
       
       $oemail=$request->session()->get('email');
       $organiser = Organiser::show($oemail);
       
       $oid=$organiser->id;
       $name=$organiser->name;
       
       $cactive = Event::activeeventcount($oid)->count(); 
       $ccompleted = Event::completedeventcount($oid)->count();
       $selected=$request->session()->get('selected');
       // list of event...
       $event=Event::showevent($oid,$selected);
        
       return view('allevent',compact('name','cactive','ccompleted','event','selected'));
    }
    else
    {
     return Redirect::to('/');
    }
  }

    public function eventtable(Request $request)
    {
      $oemail=$request->session()->get('email');
      $organiser = Organiser::show($oemail);
      $oid=$organiser->id;
      $event=Event::showevent($oid);
      return Datatables::of($event)->make(true);
    }


public function eventview(Request $request)
{
     if($request->session()->has('email'))
      {
            $c=0;
            $nc=0;
            $ts=0;
            $nr=0;  
            $id=$request->get('id');
            $event = Event::show($id);
            $date = Dates::viewdate($id);
            $invitee = Event_has_invitee::eid($id);
            $oemail =  $request->session()->get('email');
            $organiser = Organiser::show($oemail);
            $name = $organiser->name;
            
            

              if(count($invitee)>0)
              {  
                foreach ($invitee as $invitees) 
                 {
                  
              
                 if($invitees->ans == 0)
                 {
                  $inviteeid=$invitees->i_id;
                  $invite=Invitee::iid($inviteeid);
                  $nr=$nr+$invite->number_of_member;
                  $ts=$ts+$invite->number_of_member;
                 }
                 elseif ($invitees->ans == 1) 
                 {
                  $inviteeid=$invitees->i_id;
                  $invite=Invitee::iid($inviteeid);
                  $c=$c+$invite->number_of_member;
                  $ts=$ts+$invite->number_of_member; 
                 }
                 else
                 {
                  $inviteeid=$invitees->i_id;
                  $invite=Invitee::iid($inviteeid);
                  $nc=$nc+$invite->number_of_member;
                  $ts=$ts+$invite->number_of_member;
                 }

                 }
                 $c=($c*100)/$ts;
                 $nc=($nc*100)/$ts;
                 $nr=($nr*100)/$ts;
                 $ts=($ts*100)/($event->total_members);
                 return view('eventview',compact('event','date','organiser','name','c','nc','nr','ts','event','name'));
                 
              }
              else
              {
               return view('eventview',compact('event','date','organiser','name','c','nc','nr','ts','event','name'));
              }
      }
      else
      {
       return Redirect::to('/'); 
      }  
}


function deleteevent(Request $request)
{
  
      $id=$request->get('id');
      Event::deleteevent($id);
      Dates::deleteevent($id);
      Event_has_invitee::deleteevent($id);
      return Redirect::to('/event');
}


function viewinvite(Request $request)
{
    if($request->session()->has('email'))
      {
        $c=0;
        $nc=0;
        $ts=0;
        $nr=0;
        $eid=$request->get('id');
        $event =  Event::show($eid);
        $invitee = Event_has_invitee::eid($eid);
        $oemail =  $request->session()->get('email');
            $organiser = Organiser::show($oemail);
            $name = $organiser->name;
        if(count($invitee)>0)
        {  foreach ($invitee as $invitees) {
            
        
           if($invitees->ans == 0)
           {
            $inviteeid = $invitees->i_id;
            $invite = Invitee::iid($inviteeid);
            
            $nr=$nr+$invite->number_of_member;
            $ts=$ts+$invite->number_of_member;
           }
           elseif ($invitees->ans == 1) 
           {
             $inviteeid = $invitees->i_id;
             $invite = Invitee::iid($inviteeid);

            $c=$c+$invite->number_of_member;
            $ts=$ts+$invite->number_of_member; 
           }
           else
           {
            $inviteeid = $invitees->i_id;
            $invite = Invitee::iid($inviteeid);
            $nc=$nc+$invite->number_of_member;
            $ts=$ts+$invite->number_of_member;
           }

           }
           return view('viewinvite',compact('c','nc','nr','ts','event','name'));
        }
        else
        {
         return view('viewinvite',compact('c','nc','nr','ts','event','name'));
        }    

      } 
     else
     {
         return Redirect::to('/'); 
     } 

}
    
  public function addevent(Request $request)
    { 
      
      if($request->session()->has('email'))
      {
        
        $lastdte=$_POST['edate'][count($_POST['edate'])-1];
        $lasttime=$_POST['etime'][count($_POST['etime'])-1];
        $ename=$request->input('ename');
        $edescription=$request->input('edescription');
        $eduration=$request->input('eduration');
        $etm=$request->input('etm');
        $lat=$request->input('lat');
        $lon=$request->input('lon');
        $address=$request->input('address');
        $date=$request->input('edate');
        $time=$request->input('etime');


        //retriveve the id from the email...
        $oemail=$request->session()->get('email');
        $orgid = Organiser::show($oemail);
        $id=$orgid->id;  
   
        $event = Event::newevent($ename,$edescription,$lat,$lon,$address,$etm,$eduration,$lastdte,$lasttime,$id);
        
        $eid = $event->id;

        Dates::adddates($date,$time,$eid);
       
              
         
  
        return Redirect::to('/event');

      }
      else
      {
        return Redirect::to('/');
      }

    }

    public function activeevent(Request $request)
    {
      if($request->session()->has('email'))
      {
           $activeselected = $request->session()->get('activeselected');
           
           $oemail=$request->session()->get('email');
           $organiser = Organiser::show($oemail);
           $oid=$organiser->id;
           $name = $organiser->name;

            $cactive = Event::activeeventcount($oid)->count(); 
           $ccompleted = Event::completedeventcount($oid)->count();
           $activeevent=Event::activeevent($oid,$activeselected);
           return view('activeevent',compact('name','cactive','ccompleted','activeevent','activeselected'));

            
      }
      else
      {
         return Redirect::to('/');
      } 
    }
 
  public function completedevent(Request $request)
    {
      if($request->session()->has('email'))
      {
           $completeselected = $request->session()->get('completeselected');
           
           $oemail=$request->session()->get('email');
           $organiser = Organiser::show($oemail);
           $oid=$organiser->id;
           $name = $organiser->name;

            $cactive = Event::activeeventcount($oid)->count(); 
           $ccompleted = Event::completedeventcount($oid)->count();
           $completeevent=Event::completedevent($oid,$completeselected);
           return view('completedevent',compact('name','cactive','ccompleted','completeevent','completeselected'));
      }
      else
      {
         return Redirect::to('/');
      } 
    }
  

   public function sortevent(Request $request)
   {
      if($request->session()->has('email'))
      {
        $selected=$_GET['selected'];
        $oemail=$request->session()->get('email');
        $organiser = Organiser::show($oemail);
        $oid=$organiser->id;
        $request->session()->forget('selected');  
        $request->session()->put('selected',$selected);
        $event=Event::showevent($oid,$selected);
        return response()->json(compact('event'));
      }
      else
      {
         return Redirect::to('/');
      }  
   }
   public function sortactiveevent(Request $request)
   {
    if($request->session()->has('email'))
     {
        $activeselected=$_GET['selected'];
        $oemail=$request->session()->get('email');
        $organiser = Organiser::show($oemail);
        $oid=$organiser->id;
        $request->session()->forget('activeselected');  
        $request->session()->put('activeselected',$activeselected);
        $event=Event::activeevent($oid,$activeselected);
        return response()->json(compact('event'));
     }
    else
     {
        return Redirect::to('/');
     } 
   }
   public function sortcompleteevent(Request $request)
   {
    if($request->session()->has('email'))
     {
        $completeselected=$_GET['selected'];
        $oemail=$request->session()->get('email');
        $organiser = Organiser::show($oemail);
        $oid=$organiser->id;
        $request->session()->forget('completeselected');  
        $request->session()->put('completeselected',$completeselected);
        $event=Event::completedevent($oid,$completeselected);
        return response()->json(compact('event'));
     }
    else
     {
        return Redirect::to('/');
     }
   }
   public function eventsearch(Request $request)
   {
    if($request->session()->has('email'))
      {
        $search = $request->input('search');

        $oemail=$request->session()->get('email');
        $organiser = Organiser::show($oemail);
        $oid=$organiser->id;
        $name=$organiser->name;

       $cactive = Event::searchactiveeventcount($oid,$search)->count(); 
       $ccompleted = Event::searchcompletedeventcount($oid,$search)->count();
       $searcheventselected=$request->session()->get('searcheventselected');
      
       $eventsearch = Event::eventsearch($oid,$search,$searcheventselected);
       
       return view('searchallevent',compact('name','cactive','ccompleted','eventsearch','searcheventselected','search'));

      }
    else
      {
            return Redirect::to('/');
      }  
   }

   public function sorteventevent(Request $request)
   {
     if($request->session()->has('email'))
      {
        $selected=$_GET['selected'];
        $search=$_GET['search'];

        $oemail=$request->session()->get('email');
        $organiser = Organiser::show($oemail);
        $oid=$organiser->id;
        $request->session()->forget('searcheventselected');  
        $request->session()->put('searcheventselected',$selected);
        $event=Event::eventsearch($oid,$search,$selected);
        return response()->json(compact('event'));
      }
      else
      {
          return Redirect::to('/');
      }
   }

   public function activeeventsearch(Request $request)
   {
    if($request->session()->has('email'))
      {
           $searcheventselected = $request->session()->get('searchactiveselected');
           $search = $request->get('search');

           $oemail=$request->session()->get('email');
           $organiser = Organiser::show($oemail);
           $oid=$organiser->id;
           $name = $organiser->name;

           $cactive = Event::searchactiveeventcount($oid,$search)->count(); 
           $ccompleted = Event::searchcompletedeventcount($oid,$search)->count();
           
           
      
           $eventsearch = Event::activeeventsearch($oid,$search,$searcheventselected);
       
         return view('activeeventsearch',compact('name','cactive','ccompleted','eventsearch','searcheventselected','search'));
      }
    else
    {
      return Redirect::to('/');
    }  
   }

   public function sortactivesearch(Request $request)
   {
     if($request->session()->has('email'))
      {
        $selected=$_GET['selected'];
        $search=$_GET['search'];
        
        $oemail=$request->session()->get('email');
        $organiser = Organiser::show($oemail);
        $oid=$organiser->id;
        $request->session()->forget('searchactiveselected');  
        $request->session()->put('searchactiveselected',$selected);
        $event=Event::activeeventsearch($oid,$search,$selected);
        
        return response()->json(compact('event'));
      }
      else
      {
          return Redirect::to('/');
      }
   }

   public function completedeventsearch(Request $request)
   {
    if($request->session()->has('email'))
      {
           $searcheventselected = $request->session()->get('searchcompleteselected');
           $search = $request->get('search');
            
           $oemail=$request->session()->get('email');
           $organiser = Organiser::show($oemail);
           $oid=$organiser->id;
           $name = $organiser->name;

           $cactive = Event::searchactiveeventcount($oid,$search)->count(); 
           $ccompleted = Event::searchcompletedeventcount($oid,$search)->count();
           
           
      
           $eventsearch = Event::completeeventsearch($oid,$search,$searcheventselected);
       
         return view('completedeventsearch',compact('name','cactive','ccompleted','eventsearch','searcheventselected','search'));
      }
    else
    {
      return Redirect::to('/');
    }  
   }
   public function sortcompletesearch(Request $request)
   {
     if($request->session()->has('email'))
      {
        $selected=$_GET['selected'];
        $search=$_GET['search'];

        $oemail=$request->session()->get('email');
        $organiser = Organiser::show($oemail);
        $oid=$organiser->id;
        $request->session()->forget('searchcompleteselected');  
        $request->session()->put('searchcompleteselected',$selected);
        $event=Event::completeeventsearch($oid,$search,$selected);
        return response()->json(compact('event'));
      }
      else
      {
          return Redirect::to('/');
      }
   }


   public function noattendAjax(Request $request) {
      if($request->session()->has('email')) {
        $id = $request->get('id');
        $e_id = Event_has_invitee::where('id','=',$id)->first();
        $e_id = $e_id->e_id;
        echo json_encode($e_id);
       }
      else {
         return Redirect::to('/');
      }  
   }

}
