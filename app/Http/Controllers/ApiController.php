<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Providers\AddMessageApi;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use App\Models\Message;
use App\Models\ServerCredential;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    public function api()
    {

        try {
//            $test = [
//                "ticket" => [
//                    ["uid"=>"ab35b8d5-2433-49f0-a089-6b14976604aa","subject"=>"Культура",
//                "user_name"=>"22","user_email"=>"van4224s@mail.ru",
//                "created_at"=>"2022-02-10 12:38:25","updated_at"=>"2022-02-10 12:38:25"],
//                    ["uid"=>"ab35b8f5-2433-49f0-a989-2b14977604aa","subject"=>"Культура",
//                        "user_name"=>"Вас323илий","user_email"=>"vaw56642ns@mail.ru",
//                        "created_at"=>"2022-02-10 12:38:25","updated_at"=>"2022-02-10 12:38:25"]
//            ],
//                "send" => [
//                    ["author"=>"aa","content"=>"df",
//                        "created_at"=>"2022-02-10 12:38:27","updated_at"=>"2022-02-10 12:38:27"],
//                    ["author"=>"Next2","content"=>"Культурология555а",
//                        "created_at"=>"2022-02-10 12:38:47","updated_at"=>"2022-02-10 14:38:27"],
//                ],
//                "credentials"=>[
//                    ["ftp_login"=>"s",
//                        "ftp_password"=>"34f","created_at"=>"2022-02-10 12:38:27","updated_at"=>"2022-02-10 12:38:27"],
//                    ["ftp_login"=>"КультуроККК",
//                        "ftp_password"=>"3664f","created_at"=>"2021-02-10 12:38:27","updated_at"=>"2021-02-10 12:38:27"]
//                ],"x-auth-token"=>"dplp31qppIkvoxr3lIqsX77BrUrhDhsg9GFk9atO"];

            $rules = [
                'ticket'=>
                    ['subject' => 'required|string',
                'user_name' => 'required|string',
                'user_email' => 'required|email|unique:ticket,user_email',
                        ],
                'send'=>[
                'content' => 'required',
                'author' => 'required',
                ],
                'credentials'=>
                [
                'ftp_login' => 'required|string',
                'ftp_password' => 'required',
                ]
            ];
            $messages = [
                'ticket'=>['subject.required' => 'Поле "Предмет" обязательно для заполнения.',
                'subject.string' => 'Поле "Предмет" должно быть строкой.',
                'user_name.required' => 'Поле "Имя пользователя" обязательно для заполнения.',
                'user_name.string' => 'Поле "Имя пользователя" должно быть строкой.',
                'user_email.required' => 'Поле "Email" обязательно для заполнения.',
                'user_email.email' => 'Поле "Email" должно содержать @.',
                'user_email.unique' => 'Поле "Email" должно содержать уникальным.',],

                'send'=>['content.required' => 'Поле "Content" обязательно для заполнения.',
                'author.required' => 'Поле "Author" обязательно для заполнения.',],

                'credentials'=>['ftp_login.required' => 'Поле "Ftp_login" обязательно для заполнения.',
                'ftp_login.string' => 'Поле "Ftp_login" должно быть строкой.',
                'ftp_password.required' => 'Поле "Ftp_password" обязательно для заполнения.',]
                ];

            header('content-type: application/json');

            $parseJsons = json_decode(file_get_contents("php://input"), true);
            $parseTickets = $parseJsons['ticket'];
            $parseMessages = $parseJsons['send'];
            $parseCredentials = $parseJsons['credentials'];
            $result = ['ticket'=>$parseTickets,'send'=>$parseMessages,'credentials'=>$parseCredentials];

            foreach ($result as $key=>$arrays){
                foreach ($parseJsons[$key] as $array){
                    $validation = Validator::make($array, $rules[$key], $messages[$key]);
                    if($validation->fails()) {
                        return response()->json('{"response":'.$validation->errors()->first().'}', 403, ['Content-Type' => 'application/json; charset=UTF-8']);
                    }
                }

            }

            foreach ($parseTickets as $parseTicket){
                $ticket = new Ticket();
                $ticket->uid = htmlspecialchars(strip_tags($parseTicket['uid'],ENT_QUOTES));
                $ticket->subject = htmlspecialchars(strip_tags($parseTicket['subject'],ENT_QUOTES));
                $ticket->user_name = htmlspecialchars(strip_tags($parseTicket['user_name'],ENT_QUOTES));
                $ticket->user_email = htmlspecialchars(strip_tags($parseTicket['user_email'],ENT_QUOTES));

                $ticket->created_at = htmlspecialchars(strip_tags($parseTicket['created_at'],ENT_QUOTES));
                $ticket->updated_at = htmlspecialchars(strip_tags($parseTicket['updated_at'],ENT_QUOTES));
                $ticket->save();
                $ticket_id[] = $ticket->id;
            }


            foreach ($parseMessages as $parseMessage) {
                foreach ($ticket_id as $ticket_id_first){
                    $message = new Message();
                    $message->ticket_id = htmlspecialchars(strip_tags($ticket_id_first,ENT_QUOTES));
                    $message->author = htmlspecialchars(strip_tags($parseMessage['author'],ENT_QUOTES));
                    $message->content = htmlspecialchars(strip_tags($parseMessage['content'],ENT_QUOTES));

                    $message->created_at = htmlspecialchars(strip_tags($parseMessage['created_at'],ENT_QUOTES));
                    $message->updated_at = htmlspecialchars(strip_tags($parseMessage['updated_at'],ENT_QUOTES));
                    $message->save();
                    event(new AddMessageApi($message, $parseCredentials));
                }
            }

            return response()->json('{"response":"OK"}', 200, ['Content-Type' => 'application/json; charset=UTF-8']);
        } catch (Exception $e) {
            return response()->json('{"response":"ERROR"}', 403, ['Content-Type' => 'application/json; charset=UTF-8']);
        }

    }

    public function requestUser($login,$password)
    {

        $client = new \GuzzleHttp\Client();

        try {
            $res = $client->request('POST', 'https://reqres.in/api/users',
                ['name' => $login,
                    'job' => $password]);

            if ($res->getStatusCode() == 201) {
                $array = json_decode($res->getBody()->getContents());

                Log::info('Hoмер пользователя: '.$array->id.' Дата создания: '.$array->createdAt);
            }
        } catch
        (ClientException $exception) {
            Log::debug('Exception while sending payments - not save');
        }

    }

}
