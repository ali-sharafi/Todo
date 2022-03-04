<?php

namespace Todo\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Todo\Broadcasting\LogChannel;
use Todo\Model\Task;

class TaskClosed extends Notification
{
    use Queueable;

    protected $task;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', LogChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url('/tasks/' . $this->task->id);

        return (new MailMessage)
            ->greeting('Hello there')
            ->line('One of your tasks has been closed!')
            ->action('View Task', $url)
            ->line('Thank you for using our application!');
    }

    public function toLog()
    {
        return "The task {$this->task->title} has been closed!";
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
