<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Organiser;
use App\Invitee;
use App\Event;
use Input;
use View;
use DB;
use Mail;

class invitemail extends Mailable
{
    use Queueable, SerializesModels;
     protected  $event;
     protected $inviteeid;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($event,$inviteeid)
    {
        $this->event =$event;
        $this->inviteeid=$inviteeid;
        
       
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $event=$this->event;
        $inviteeid=$this->inviteeid;
      
        $invitee=DB::table('event_has_invitees')->where([['e_id','=',$event],['i_id','=',$inviteeid]])->first();
        
        $data=DB::table('events')->where('id','=',$event)->first();
        $data1=DB::table('dates')->where('e_id','=',$event)->get();

        return $this->view('sendinvite',compact('data','data1','invitee'));
        
        
    }
}
