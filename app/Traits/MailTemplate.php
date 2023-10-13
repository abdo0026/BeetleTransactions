<?php
/**
 * Created by PhpStorm.
 * User: foda
 * Date: 10/21/2019
 * Time: 9:31 AM
 */

namespace App\Traits;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailTemplate extends Mailable
{
    use Queueable, SerializesModels;
    public $data ;
    public $subject ;
    public $view ;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data , $subject , $view)
    {
        $this->data = $data ;
        $this->subject = $subject ;
        $this->view = $view ;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)->view($this->view);
    }
}
