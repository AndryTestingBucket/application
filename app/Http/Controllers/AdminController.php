<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Providers\AddMessage;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\FeedbackController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\Message;
use App\Models\ServerCredential;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class AdminController extends Controller
{


    public function index()
    {
        return view('admin');
    }

    public function addTicket(Request $request)
    {

        $validatedData = $request->validate([
            'subject' => 'required|string',
            'user_name' => 'required|string',
//            'email' => 'required|email',
        ],
            [
                'subject.required' => 'Поле "Предмет" обязательно для заполнения.',
                'subject.string' => 'Поле "Предмет" должно быть строкой.',
                'user_name.required' => 'Поле "Имя пользователя" обязательно для заполнения.',
                'user_name.string' => 'Поле "Имя пользователя" должно быть строкой.',
                'email.required' => 'Поле "Email" обязательно для заполнения.',
                'email.email' => 'Поле "Email" должно содержать @.',
            ]);


        $uid = Str::uuid()->toString();

        $subject = strip_tags($request->all()['subject']);
        $subject = htmlspecialchars($subject, ENT_QUOTES);

        $user_name = strip_tags($request->all()['user_name']);
        $user_name = htmlspecialchars($user_name, ENT_QUOTES);

        $user_email = strip_tags($request->all()['email']);
        $user_email = trim($user_email);

        $now = now();

        $ticket = new Ticket();

        $ticket->uid = $uid;
        $ticket->subject = $subject;
        $ticket->user_name = $user_name;
        $ticket->user_email = $user_email;

        $ticket->created_at = $now;
        $ticket->updated_at = $now;

        $ticket->save();

        $ticket_id = $ticket->id;


        foreach ($request->all()['author'] as $key_author => $author) {
            foreach ($request->all()['content'] as $key_cont => $content) {
                if ($key_author == $key_cont) {

                    $message = new Message();

                    $message->ticket_id = $ticket_id;
                    $message->author = $author;
                    $message->content = $content;

                    $message->created_at = $now;
                    $message->updated_at = $now;

                    $message->save();

                }
            }
        }

        event(new AddMessage($message,$request));

        return view('admin', ['succes' => 'Письмо отправлено пользователю']);
    }

    public function ticket()
    {
        $ticket = DB::table('ticket')->get();
        $message = DB::table('message')->get();
        $credentials = DB::table('servercredentials')->get();

        return view('ticket', ['tickets' => $ticket, 'messages' => $message, 'credentials' => $credentials]);
    }


}
