<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\FeedbackController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\Message;
use App\Models\ServerCredential;

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
            'email' => 'required|email',
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

        $author = strip_tags($request->all()['author']);
        $author = htmlspecialchars($author, ENT_QUOTES);

        $content = strip_tags($request->all()['content']);
        $content = htmlspecialchars($content, ENT_QUOTES);


        $message = new Message();

        $message->ticket_id = $ticket_id;
        $message->author = $author;
        $message->content = $content;

        $message->created_at = $now;
        $message->updated_at = $now;

        $message->save();

        $message_id = $message->id;

        $ftp_login = strip_tags($request->all()['ftp_login']);
        $ftp_login = trim($ftp_login);
        $ftp_login = Hash::make($ftp_login);

        $ftp_password = Hash::make($request->all()['ftp_password']);

        $serverCred = new ServerCredential();

        $serverCred->message_id = $message_id;
        $serverCred->ftp_login = $ftp_login;
        $serverCred->ftp_password = $ftp_password;

        $serverCred->save();

        $feedback = new FeedbackController();
        $feedback->send();


        return view('admin', ['succes' => 'Письмо отправлено пользователю']);
    }

}
