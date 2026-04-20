<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Http\Traits\SettingTrait;

class CheckedFormNotification extends Notification
{
    use Queueable;
    use SettingTrait;

    protected $all_forms;
    /**
     * Create a new notification instance.
     */
    public function __construct($all_forms)
    {
        $this->all_forms = $all_forms;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        if($this->getEmailSending()) {
            return ['database', 'mail'];
        } else {
            return ['database'];
        } 
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = "B-FORM CHECKED AND RELEASED [".$this->all_forms->model->control_number.']';
        $greeting = "Hello, {$notifiable->name}";

        // $prefix = strtolower($this->all_forms->form->prefix);

        $introLines = [
            "The ".$this->all_forms->form->name." with number \"<strong>{$this->all_forms->model->control_number}</strong>\" has been CHECKED AND RELEASED by Security!"
        ];
        $outroLines = [
            "You can view or print your B-FORM at your earliest convenience by clicking the button above."
        ];

        $buttons = [
            [
                'url' => url('myform/show/' . encrypt($this->all_forms->id)),
                'label' => 'View B-FORM',
                'class' => 'button'
            ],
            [
                'url' => url('printPDF/' . encrypt($this->all_forms->id)),
                'label' => 'Print PDF',
                'class' => 'button'
            ]
        ];


        return (new MailMessage)
            ->subject($subject)
            ->view('emails.notification', [
                'url' => url('/myform/show/' . encrypt($this->all_forms->id)),
                'greeting' => $greeting,
                'introLines' => $introLines,
                'outroLines' => $outroLines,
                'buttons' => $buttons,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'B-FORM CHECKED AND RELEASED',
            'message' => 'The '.$this->all_forms->form->name.' has been checked and released by security!',
            'action_url' => url('/myform/show/' . encrypt($this->all_forms->id)),
        ];
    }
}
