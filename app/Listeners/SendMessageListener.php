<?php

namespace App\Listeners;

use App\Events\SendMessageEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SendMessageListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SendMessageEvent $event): void
    {
      $concour = $event->abonnements->concour();
      Log::info($concour->libelle);
      $body = [
            "message" => "Vous venez de souscrire au concours ".$concour->libelle." votre code est ".$event->code."\nMerci d'utiliser Monprof",
            "senderId"=> env("SMS_SENDER_ID"),
            "msisdn" => ['237'.$event->abonnements->client_number]
        ];
        $url = "https://sms.lmtgroup.com/api/v1/pushes";
        $header = [
            "X-Api-Key"=>env("SMS_API_KEY") ,
            "Content-Type"=> "application/json",
            "X-Secret"=> env('SMS_API_SECRET')
        ];

        $client  = New Client();
        $promesse = $client->post(
            $url,
            ['body'=>$body, 'header'=>$header]
        );
        Log::info($promesse->getStatusCode());
        Log::info($promesse->getReasonPhrase());
    }
}
