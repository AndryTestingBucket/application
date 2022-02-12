<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Providers\AddMessageApi;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use App\Models\Message;
use App\Models\ServerCredential;
use Exception;

class ApiController extends Controller
{
    public function api()
    {

        try {
//            $test = ["ticket" => ["uid"=>"ab35b8d5-2433-49f0-a089-6b14977604aa","subject"=>"Культура",
//                "user_name"=>"Василий","user_email"=>"vans@mail.ru",
//                "created_at"=>"2022-02-10 12:38:25","updated_at"=>"2022-02-10 12:38:25"],
//                "send" => [
//                    ["author"=>"Next","content"=>"Культурология в древние века",
//                        "created_at"=>"2022-02-10 12:38:27","updated_at"=>"2022-02-10 12:38:27"],
//                    ["author"=>"Next2","content"=>"Культурология555а",
//                        "created_at"=>"2022-02-10 12:38:47","updated_at"=>"2022-02-10 14:38:27"],
//                ],
//                "credentials"=>[
//                    ["ftp_login"=>"КультурологиКККК6666века",
//                        "ftp_password"=>"34f","created_at"=>"2022-02-10 12:38:27","updated_at"=>"2022-02-10 12:38:27"],
//                    ["ftp_login"=>"КультуроККК",
//                        "ftp_password"=>"3664f","created_at"=>"2021-02-10 12:38:27","updated_at"=>"2021-02-10 12:38:27"]
//                ]];

            //$test = json_encode ($test, JSON_UNESCAPED_UNICODE);

            header('content-type: application/json');

            $parseJsons = json_decode(file_get_contents("php://input"), true);

            $parseTicket = $parseJsons['ticket'];


            $ticket = new Ticket();
            $ticket->uid = $parseTicket['uid'];
            $ticket->subject = $parseTicket['subject'];
            $ticket->user_name = $parseTicket['user_name'];
            $ticket->user_email = $parseTicket['user_email'];

            $ticket->created_at = $parseTicket['created_at'];
            $ticket->updated_at = $parseTicket['updated_at'];
            $ticket->save();

            $ticket_id = $ticket->id;

            foreach ($parseJsons['send'] as $parseMessage) {
                $message = new Message();
                $message->ticket_id = $ticket_id;
                $message->author = $parseMessage['author'];
                $message->content = $parseMessage['content'];

                $message->created_at = $parseMessage['created_at'];
                $message->updated_at = $parseMessage['updated_at'];
                $message->save();

            }

            event(new AddMessageApi($message, $parseJsons['credentials']));

            return response()->json('{"response":"OK"}', 200, ['Content-Type' => 'application/json; charset=UTF-8']);
        } catch (Exception $e) {
            return response()->json('{"response":"ERROR"}', 403, ['Content-Type' => 'application/json; charset=UTF-8']);
        }

    }
}
