<?php


namespace App\Message;


class ServerStatusMessage
{

    const EVENT_ADD = 1;
    const EVENT_REM = 2;

    protected $event;
    protected $server_list;

    public function __construct($event, $server_list)
    {
        $this->event = $event;
        $this->server_list = $server_list;
    }

    public function toArray()
    {
        return [
            'event' => $this->event,
            'server_list' => $this->server_list
        ];
    }
}