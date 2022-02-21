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
use Illuminate\Support\Facades\Event;

class AdminController extends Controller
{


    public function index()
    {
        return view('admin');
    }

    public function addTicket(Request $request)
    {
        //проверка на маты.В нашем случае просто плохие слова
        $bads = ['дурачок','долбоящер','лохопед'];
        $word = strip_tags($request->all()['user_name']);
        $word = htmlspecialchars($word, ENT_QUOTES);

        foreach ($bads as $bad){
            if($word == $bad){
                if($word == 'долбоящер'){
                    return view('admin', ['succes' => 'Уважай живые существа,исправь значение "user_name"!!!']);
                }else{
                    return view('admin', ['succes' => 'Не используйте плохие слова для поля "user_name"!!!']);
                }
            }
        }


        $validatedData = $request->validate([
            'subject' => 'required|string',
            'user_name' => 'required|string',
            'email' => 'required|email|unique:ticket,user_email',
            'ftp_login' => 'array',
            'ftp_login.*' => 'required|string',
            'ftp_password' => 'array',
            'ftp_password.*' => 'required',
            'content' => 'array',
            'author' => 'array',
            'content.*' => 'required',
            'author.*' => 'required',
        ],
            [
                'subject.required' => 'Поле "Предмет" обязательно для заполнения.',
                'subject.string' => 'Поле "Предмет" должно быть строкой.',
                'user_name.required' => 'Поле "Имя пользователя" обязательно для заполнения.',
                'user_name.string' => 'Поле "Имя пользователя" должно быть строкой.',
                'email.required' => 'Поле "Email" обязательно для заполнения.',
                'email.email' => 'Поле "Email" должно содержать @.',
                'email.unique' => 'Поле "Email" должно содержать уникальным.',
                'ftp_login.array' => 'Поле "Ftp_login" должно быть массивом.',
                'ftp_login.*.required' => 'Поле "Ftp_login" обязательно для заполнения.',
                'ftp_login.string.*' => 'Поле "Ftp_login" должно быть строкой.',
                'ftp_password.array' => 'Поле "Ftp_password" должно быть строкой.',
                'ftp_password.*.required' => 'Поле "Ftp_password" обязательно для заполнения.',
                'content.*.required' => 'Поле "Content" обязательно для заполнения.',
                'content.array' => 'Поле "Content" должно быть массивом.',
                'author.*.required' => 'Поле "Author" обязательно для заполнения.',
                'author.array' => 'Поле "Author" должно быть массивом.',
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
        $ticket = Ticket::all();
        $message = Message::all();
        $credentials = ServerCredential::all();

        return view('ticket', ['tickets' => $ticket, 'messages' => $message, 'credentials' => $credentials]);
    }


}
