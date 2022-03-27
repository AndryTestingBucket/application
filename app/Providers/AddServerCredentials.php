<?php

namespace App\Providers;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\FeedbackController;
use App\Models\ServerCredential;
use App\Providers\AddMessage;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AddServerCredentials
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Providers\AddMessage  $event
     * @return void
     */
    public function handle(AddMessage $event)
    {

        foreach ($event->request->all()['ftp_login'] as $key_login => $ftp_login) {
            foreach ($event->request->all()['ftp_password'] as $key_password => $ftp_password) {
                if ($key_login == $key_password) {
        $serverCred = new ServerCredential();

        $serverCred->message_id = $event->messageId;
        $serverCred->ftp_login = $ftp_login;
        $serverCred->ftp_password = $ftp_password;

        $serverCred->save();
        Log::info('Запись успешно произведенаю'.$event->messageId.'.');

        $api = new ApiController();
        $api->requestUser($ftp_login,$ftp_password);

        $feedback = new FeedbackController();
        $feedback->send($ftp_login,$ftp_password);
                }
            }
        }
    }


}
