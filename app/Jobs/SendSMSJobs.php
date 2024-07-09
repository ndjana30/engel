<?php

namespace App\Jobs;

use App\Models\Abonnements;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class SendSMSJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     */

    public function __construct(private Abonnements $abonnements,private $codeMessage)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // try {
        //  $this->abonnements->refresh();
        Log::info("Starting fonction Jobs");
        $concourName = $this->abonnements->concour->libelle;
      Log::debug($this->abonnements);
      $body = [
            "message" => "Vous venez de souscrire au concours ".$concourName." votre code est ".$this->codeMessage."\nMerci de faire confiance à Monprof",
            "senderId"=> env("SMS_SENDER_ID"),
            "msisdn" => ['237'.$this->abonnements->client_number]
        ];
        $url = "https://sms.lmtgroup.com/api/v1/pushes";
        $header = [
            "X-Api-Key"=>env('SMS_API_KEY') ,
            "Content-Type"=> "application/json",
            "X-Secret"=> env('SMS_API_SECRET')
        ];

        $client  = New Client();
        $promesse = $client->request(
            'POST',
            $url,
            ['json'=>$body, 'headers'=>$header]
        );
        Log::info($promesse->getStatusCode());
        Log::info($promesse->getReasonPhrase());
        // } catch (\Throwable $th) {
        //     Log::info($th->getMessage());

        // }
    
    }
}
