<?php

namespace App\Providers;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Providers\AddServerCredentialsApi;
use App\Models\Message;
use Illuminate\Http\Request;


class AddMessageApi
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Экземпляр заказа.
     *
     * @var \App\Models\Message
     */

    public $message;

    public $credentialsJson;

    public $messageId;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Message  $message
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct($message,$credentials)
    {
        $this->messageId = $message->id;
        $this->credentialsJson= $credentials;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
