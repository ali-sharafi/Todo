<?php

namespace Todo\Broadcasting;


use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Notification;

class LogChannel
{
    /**
     * @var Dispatcher
     */
    protected $events;

    /**
     * LogChannel constructor.
     *
     * @param Dispatcher $events
     */
    public function __construct(Dispatcher $events)
    {
        $this->events = $events;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     *
     */
    public function send($notifiable, Notification $notification)
    {
        $logChannel = $this->getLogChannel();

        $message = $notification->toLog($notifiable);

        Log::channel($logChannel)->debug($message);
    }

    /**
     * @return string
     */
    private function getLogChannel()
    {
        return env('LOG_NOTIFICATIONS_CHANNEL', config('logging.default'));
    }
}
