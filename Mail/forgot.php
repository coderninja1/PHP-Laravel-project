<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use View;
use DB;
use Mail;
class forgot extends Mailable
{
    use Queueable, SerializesModels;
      protected  $email;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email)
    {
       $this->email =$email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
         $email=$this->email;
         $organiser = DB::table('organisers')->where('email','=',$email)->first();

        return $this->view('forgotlink',compact('organiser'));
    }
}
