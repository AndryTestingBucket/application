<div style="width: 100%;display:flex;">
<div style="width: 33.33%">

        <h1 style="color: darkred">Ticket</h1>

@foreach ($tickets as $ticket)
    <div class="work__pages">
        <h2 style="color: red">ID Автора:{{ $ticket->id }}</h2>
    </div>
    <div class="work__pages">
        <span>UID:{{ $ticket->uid }}</span>
    </div>
    <div class="work__pages">
        <span>Предмет:{{ $ticket->subject }}</span>
    </div>
    <div class="work__pages">
        <span>Имя пользователя:{{ $ticket->user_name }}</span>
    </div>
    <div class="work__pages">
        <span>Email пользователя:{{ $ticket->user_email }}</span>
    </div>
    <div class="work__pages">
        <span>Дата записи:{{ $ticket->created_at }}</span>
    </div>
    <div class="work__pages">
        <span>Дата обновления записи:{{ $ticket->updated_at }}</span>
    </div>
@endforeach
</div>
<div style="width: 33.33%">
    <h1 style="color: darkred">Письма</h1>
@foreach ($messages as $message)

        <div class="work__pages">
            <h2 style="color: red">Автор:{{ $message->author }}</h2>
        </div>
        <div class="work__pages">
            <span>Контент:{{ $message->content }}</span>
        </div>
        <div class="work__pages">
            <span>Дата записи:{{ $message->created_at }}</span>
        </div>
        <div class="work__pages">
            <span>Дата обновления записи:{{ $message->updated_at }}</span>
        </div>

@endforeach
</div>
<div style="width: 33.33%">
    <h1 style="color: darkred">Сredentials</h1>
@foreach ($credentials as $credential)

        <div class="work__pages">
            <h2 style="color:red">Логин:{{ $credential->ftp_login }}</h2>
        </div>
        <div class="work__pages">
            <span>Пароль:{{ $credential->ftp_password }}</span>
        </div>
        <div class="work__pages">
            <span>Дата записи:{{ $credential->created_at }}</span>
        </div>
        <div class="work__pages">
            <span>Дата обновления записи:{{ $credential->updated_at }}</span>
        </div>

@endforeach
</div>
</div>
