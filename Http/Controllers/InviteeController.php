<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Mail\invitemail;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use App\Organiser;
use App\Invitee;
use App\Event_has_invitee;
use App\Event;
use Input;
use View;
use DB;
use Mail;
class InviteeController extends Controller
{
    
    public function addinvitee(Request $request)
    {
    if($request->session()->has('email'))
      {
        $oemail=$request->session()->get('email');
        $organiser = Organiser::show($oemail);
        $name = $organiser->name; 
            return view('addinvitee',compact('name'));
      }
      else
      {
        return Redirect::to('/login');
      }
    }
    public function check(Request $request)
    {
       if($request->session()->has('email'))
      {

       $images = Input::file('imageupload');

      $destinationPath = public_path().'/inviteephoto/';
      if( is_null($images))
      {
         $filename="anyone.png";
      }
      else
      {
         $filename = $images->getClientOriginalName();

         $images->move($destinationPath, $filename); 
      }
      

      $name = $request->input('name');
      $email=$request->input('email');
      $dob=$request->input('dob');
      $gender=$request->input('gender');
      $address=$request->input('address');
      $nom=$request->input('nom');

       $oemail=$request->session()->get('email');
       $orgid = Organiser::show($oemail);
       $id=$orgid->id;

           //check..
            
            $mytime = Carbon::now();
            $date1=date_create($mytime);
            $date2=date_create($dob);
            $diff=date_diff($date1,$date2);
            $age =$diff->y;
            /*echo $mytime->toDateString()."<br>";
            echo $age->diff($mytime)->y;*/
            
            //check... 
        
        Invitee::newinvitee($name,$gender,$age,$dob,$email,$address,$nom,$filename,$id);
      
      

      return Redirect::to('/invitee');

    }
     else
      {
        return Redirect::to('/login');
      }
      

    }
    
   
    public function show(Request $request)
    {
        if($request->session()->has('email'))
        {
        $id = $request->get('id');
        $invitee = Invitee::iid($id);//retrieve the invitee id...(invitee)
        $oemail=$request->session()->get('email');
        $organiser = Organiser::show($oemail);
        $name = $organiser->name; 
        return view('inviteeprofile', ['data' => $invitee],['name'=>$name]);
        }
        else{
            return Redirect::to('/login');
        } 
    }
    function invitedlist(Request $request)
    {
      if($request->session()->has('email'))
        {
      $iid=$request->get('id');
      $search = $request->input('search','');
      $id = $request->input('id','iid');
      
      $event_has_invitee = Event_has_invitee::eid($id);

      $invitee = collect([]);
      $oemail=$request->session()->get('email');
        $organiser = Organiser::show($oemail);
        $name = $organiser->name; 
      if(count($event_has_invitee)>0)
      {
        
        foreach ($event_has_invitee as $invitees) {
          $inviteeid[] = $invitees->i_id;
          }
        
        $invitee = Invitee::whereIn('id',$inviteeid)
                   ->where(function($query) use($search) {
                   return $query->where('name','like','%'.$search.'%')->orWhere('email','like','%'.$search.'%')->orWhere('address','like','%'.$search.'%');
                       })->paginate(20);
        
        return view('invitedlist',compact('invitee','name','search','id'));
      }
      else
      {
       return view('invitedlist',compact('invitee','name','search','id'));
      }
      }
        else{
            return Redirect::to('/login');
        } 

    }
    public function attendinvitee(Request $request)
    {
      if($request->session()->has('email'))
        {
      $iid=$request->get('id');
      $search = $request->input('search','');
      $id = $request->input('id','iid');
      $event_has_invitee = Event_has_invitee::eid($id);

      $invitee = collect([]);
      $oemail=$request->session()->get('email');
        $organiser = Organiser::show($oemail);
        $name = $organiser->name;

      if(count($event_has_invitee)>0)
      {
          $attend = Event_has_invitee::attend($id);
          
        if(count($attend)>0)
         {
            foreach ($attend as $invitees) {
              $inviteeid[]=$invitees->i_id;
               
            }
          
             $invitee = Invitee::whereIn('id',$inviteeid)
                   ->where(function($query) use($search) {
                   return $query->where('name','like','%'.$search.'%')->orWhere('email','like','%'.$search.'%')->orWhere('address','like','%'.$search.'%');
                       })->paginate(20);
             if(count($invitee)>0)
              {
                foreach ($invitee as $invitees) {
              $inviteeid1[]=$invitees->id;
               
            }    
           $event_has_invitee = Event_has_invitee::whereIn('i_id',$inviteeid1)
                      ->where('e_id',$id)
                      ->get();
               }    
             
        

            $attend=$invitee;
           
            return view('attendinvitee',compact('name','attend','event_has_invitee','search','id'));
         } 
        else
        {
            
             return view('attendinvitee',compact('name','attend','event_has_invitee','search','id'));
        }
        
      }
      else
      {
       return view('invitedlist',compact('name','invitee','search','id'));
      }

        }
        else{
            return Redirect::to('/login');
        }   
    }
public function noattendinvitee(Request $request)
    {
      if($request->session()->has('email'))
        {
       $iid=$request->get('id');
       $search = $request->input('search','');
      $id = $request->input('id','iid');
       $event_has_invitee = Event_has_invitee::eid($id);
       $invitee = collect([]);
       $oemail=$request->session()->get('email');
        $organiser = Organiser::show($oemail);
        $name = $organiser->name; 
      if(count($event_has_invitee)>0)
      {
        $attend = Event_has_invitee::noattend($id);

         if(count($attend)>0)
         {
            foreach ($attend as $invitees) {
              $inviteeid[]=$invitees->i_id;
              
            }
            $invitee = Invitee::whereIn('id',$inviteeid)
                   ->where(function($query) use($search) {
                   return $query->where('name','like','%'.$search.'%')->orWhere('email','like','%'.$search.'%')->orWhere('address','like','%'.$search.'%');
                       })->paginate(20);
            $attend=$invitee;
            return view('noattendinvitee',compact('attend','name','search','id'));
         } 
        else
        {
            
             return view('noattendinvitee',compact('attend','name','search','id'));
        }
        
      }
      else
      {
        return view('invitedlist',compact('name','invitee','search','id'));
      }

   }
        else{
            return Redirect::to('/login');
        }   
    }
    public function noresponse(Request $request)
    {
      if($request->session()->has('email'))
        {
       $iid=$request->get('id');
       $search = $request->input('search','');
       $id = $request->input('id','iid');
       $event_has_invitee = Event_has_invitee::eid($id);
        $invitee = collect([]);
        $oemail=$request->session()->get('email');
        $organiser = Organiser::show($oemail);
        $name = $organiser->name; 
      if(count($event_has_invitee)>0)
      {
        $attend = Event_has_invitee::noresponse($id);
        
         if(count($attend)>0)
         {
             foreach ($attend as $invitees) {
              $inviteeid[]=$invitees->i_id;
             
            }
            $invitee = Invitee::whereIn('id',$inviteeid)
                   ->where(function($query) use($search) {
                   return $query->where('name','like','%'.$search.'%')->orWhere('email','like','%'.$search.'%')->orWhere('address','like','%'.$search.'%');
                       })->paginate(20);
            $attend=$invitee;
            return view('noresponse',compact('attend','name','search','id'));
         } 
        else
        {
            
             return view('noresponse',compact('attend','name','search','id'));
        }
        
      }
      else
      {
        return view('invitedlist',compact('invitee','name','search','id'));
      }
      }
        else{
            return Redirect::to('/login');
        }
    }
    
    public function highchart(Request $request)
        {
              $c=0;
              $nc=0;
              $ts=0;
              $nr=0;
              $eid=$request->get('id');
              $event = Event::show($eid);
              $invitee = Event_has_invitee::eid($eid);
              $oemail=$request->session()->get('email');
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
                 return view('highchart',compact('c','nc','nr','ts','event','name'));
              }
              else
              {
               return view('highchart',compact('c','nc','nr','ts','event','name'));
              }
        }

    public function sendinvite(Request $request)
    {
      if($request->session()->has('email'))
      { 
            $eventid=$request->get('id');
            $event = Event::show($eventid);
            
               

            $oemail=$request->session()->get('email');
            $organiser = Organiser::show($oemail);
            $oid=$organiser->id;
            $eid=$eventid;
 
            
            
            
            $results = Invitee::noinvited($eventid,$oid);


            
             if(count($results)>0)
             {
              return view('sendevent',compact('results','event'));
             }
             else
             {
             return view('sendevent',compact('results','event'));
             }
            
      }
      else
      {
        return Redirect::to('/login');
      }
       
    }


   function searchsendinvite(Request $request)
   {
    if($request->session()->has('email'))
      {
          $search = $request->get('search');
          $eid = $request->get('eid');
          $event = Event::show($eid);
          $oemail=$request->session()->get('email');
          $organiser = Organiser::show($oemail);
          $oid=$organiser->id;
          
          $results = Invitee::searchnoinvited($eid,$oid,$search);

          if(count($results)>0)
             {
              return view('searchsendevent',compact('results','event','search'));
             }
             else
             {
              return view('searchsendevent',compact('results','event','search'));
             }
      } 
     else
      {
        return Redirect::to('/login');
      }    
    
   }

    function sendinvitation(Request $request)
    {
            $eventid=$_GET['e_id'];          
            $iids =$_GET['iid'];
           foreach ($iids as  $iid) 
           {
             $data[] = Invitee::sendinvitation($iid); 
           } 
        
             //insert the event id and invitee id into invitee_has_event table with organiser id...
            
            $oemail=$request->session()->get('email');
            $organiser = Organiser::show($oemail);
           
            $id=$organiser->id;
           //insert query start...
            foreach($data as  $datas)
            {
              $datasid=$datas->id;
              Event_has_invitee::newinvitation($eventid,$datasid,$id);
              
            } 
            
            //insert query end...
            foreach($data as $invitee)
             {
               $inviteeemail = $invitee->email;
               $inviteeid=$invitee->id;
               Mail::to($inviteeemail)->send(new invitemail($eventid,$inviteeid));
             }
            
             echo json_encode("success");
    }

    public function delete(Request $request)
    {
      if($request->session()->has('email'))
      { 
         $id = $request->get('id');//invitee id...
         Invitee::deleteinvitee($id);
         Event_has_invitee::deleteeventhasinvitee($id);
         
         return Redirect::to('/invitee');
      }
      else
      {
       return Redirect::to('/login'); 
      }
    }

    public function reason(Request $request)
    {
      $id=$request->input('reason');
      $reason=$request->input('name');
      Event_has_invitee::reason($id,$reason);
      return Redirect::to('/event');
    }

    public function random(Request $request)
    {
     
        for($i=0;$i<30;$i++)
        {
          // Event::randomevent();
          // Invitee::randomevent();
        } 
        return Redirect::to('/invitee');
     
    }

    public function sortallinvitee(Request $request)
    {
      if($request->session()->has('email'))
      {
        $inviteeselected=$_GET['selected'];
        $oemail=$request->session()->get('email');
        $organiser = Organiser::show($oemail);
        $oid=$organiser->id;
        $request->session()->forget('inviteeselected');  
        $request->session()->put('inviteeselected',$inviteeselected);
        $allinvitee =Invitee::allInvitee($oid,$inviteeselected);
        return response()->json(compact('allinvitee'));
      }
      else
      {
         return Redirect::to('/');
      }
    }

    public function inviteesearch(Request $request)
    {
      if($request->session()->has('email'))
      {
           $search =  $request->get('search');
           $oemail=$request->session()->get('email');
     
           $organiser =Organiser::oid($oemail);//call the model and retrieve the oid....(Organiser)
           $oid=$organiser->id;
           $name=$organiser->name;
           $searchinviteeselected = $request->session()->get('searchinviteeselected');
           $searchinvitee =Invitee::searchInvitee($oid,$searchinviteeselected,$search);
                     
           return view('searchinvitee',compact('searchinvitee','name','searchinviteeselected','search'));
      }
      else
      {
         return Redirect::to('/');
      }

    }

    public function sortsearchinvitee(Request $request)
    {
      if($request->session()->has('email'))
      {
        $searchinviteeselected=$_GET['selected'];
        $search = $_GET['search'];
        $oemail=$request->session()->get('email');
        $organiser = Organiser::show($oemail);
        $oid=$organiser->id;
        $request->session()->forget('searchinviteeselected');  
        $request->session()->put('searchinviteeselected',$searchinviteeselected);
        $allinvitee =Invitee::searchInvitee($oid,$searchinviteeselected,$search);
        return response()->json(compact('allinvitee'));
      }
      else
      {
         return Redirect::to('/');
      }
    }



}   


           
            
            

