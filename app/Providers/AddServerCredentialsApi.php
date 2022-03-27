<?php

namespace App\Providers;

use App\Providers\AddMessageApi;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\ServerCredential;

class AddServerCredentialsApi
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
     * @param \App\Providers\AddMessageApi $event
     * @return void
     */
    public function handle(AddMessageApi $event)
    {

        foreach ($event->credentialsJson as $ftp) {

            $serverCred = new ServerCredential();

            $serverCred->message_id = $event->messageId;
            $serverCred->ftp_login = htmlspecialchars(strip_tags($ftp['ftp_login'],ENT_QUOTES));
            $serverCred->ftp_password = strip_tags($ftp['ftp_password'],ENT_QUOTES);

            $serverCred->save();
            Log::info('Запись Api успешно произведенаю' . $event->messageId . '.');
        }
    }

}
