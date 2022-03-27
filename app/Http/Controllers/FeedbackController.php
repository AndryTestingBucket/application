<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\FeedbackMail;

class FeedbackController extends Controller
{
    public function send($login,$password) {
        $comment = 'Это сообщение отправлено пользователю .'.$login.'. с паролем'.$password.'.';
        $toEmail = "blader_100@mail.ru";
        Mail::to($toEmail)->send(new FeedbackMail($comment));
        return 'Сообщение отправлено на адрес '. $toEmail;
    }
}
