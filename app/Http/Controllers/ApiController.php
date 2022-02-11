<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use App\Models\Message;
use App\Models\ServerCredential;
use Exception;

class ApiController extends Controller
{
    public function api($json)
    {

        try {

//        $json = '{"ticket":[{"id":"14","uid":"ab35b8d5-2433-49f0-a089-6b14977604aa","subject":"Культура","user_name":"Василий","user_email":"vans@mail.ru",
//        "created_at":"2022-02-10 12:38:25","updated_at":"2022-02-10 12:38:25"}],
//        "send":[{"id":"14","ticket_id":"2","author":"Next","content":"Культурология в древние века",
//        "created_at":"2022-02-10 12:38:27","updated_at":"2022-02-10 12:38:27"},
//        {"id":"14","ticket_id":"3","author":"Next","content":"Культурология в древние века",
//        "created_at":"2022-02-10 12:38:27","updated_at":"2022-02-10 12:38:27"}]
//        "credentials":[{"id":"12","message_id":"Next","ftp_login":"Культурология в древние века",
//        "ftp_password":"34f","created_at":"2022-02-10 12:38:27","updated_at":"2022-02-10 12:38:27"},
//        {"id":"15","message_id":"N2ext","ftp_login":"Культурол222огия в древние века",
//        "ftp_password":"343f","created_at":"2022-02-10 12:38:27","updated_at":"2022-02-10 12:38:27"}]}';


            $parseJsons =  json_decode($json, true);

            foreach ($parseJsons['ticket'] as $parseTicket){

                $ticket = New Ticket();
                $ticket->uid = $parseTicket['uid'];
                $ticket->subject = $parseTicket['subject'];
                $ticket->user_name = $parseTicket['user_name'];
                $ticket->user_email = $parseTicket['user_email'];

                $ticket->created_at = $parseTicket['created_at'];
                $ticket->updated_at = $parseTicket['updated_at'];
                $ticket->save();
            }

            $ticket_id = $ticket->id;

            foreach ($parseJsons['send'] as $parseMessage){

                $message = New Message();
                $message->ticket_id = $ticket_id;
                $message->author = $parseMessage['author'];
                $message->content = $parseMessage['content'];

                $message->created_at = $parseMessage['created_at'];
                $message->updated_at = $parseMessage['updated_at'];
                $message->save();

            }

            $message_id = $message->id;

            foreach ($parseJsons['credentials'] as $parseCredentials){

                $credentials = New ServerCredential();
                $credentials->message_id = $message_id;
                $credentials->ftp_login = $parseCredentials['ftp_login'];
                $credentials->ftp_password = $parseCredentials['ftp_password'];

                $credentials->created_at = $parseCredentials['created_at'];
                $credentials->updated_at = $parseCredentials['updated_at'];
                $credentials->save();

            }

            return response()->json('{"response":"OK"}', 200, ['Content-Type' => 'application/json; charset=UTF-8']);
        } catch (Exception $e) {
            return response()->json('{"response":"ERROR"}', 404, ['Content-Type' => 'application/json; charset=UTF-8']);
        }

    }
}
