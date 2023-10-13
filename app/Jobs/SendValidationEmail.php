<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Repositories\Repository;
use App\Traits\MailTemplate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;


class SendValidationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $userRepository = Repository::getRepository('User');
        $user = $userRepository->getById($this->userId);
        $registerationValidation = $user->registerationValidation;

        //send email with validation code
        try{
            $mailData['email']  = $user->email;
            $mailData['name'] = $user->name;
            $mailData['verify_link'] = env('VERIFY_URL') . "?uid=" . $user->id . "&" . "code=" . $registerationValidation->verification_code;
            
            //Mail::to($mailData['email'])->send(new MailTemplate($mailData, 'verify account', 'Mails.VerifyEmail'));
        }catch(\Exception $e) {
                Log::info($e);
        }
    }
}
