<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Http\Traits\SettingTrait;

class SubmitFormNotification extends Notification
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
        $subject = "B-FORM SUBMISSION [".$this->all_forms->model->control_number.']';
        $greeting = "Hello, {$notifiable->name}";
        $introLines = [
            "A new ".$this->all_forms->form->name." with number \"<strong>{$this->all_forms->model->control_number}</strong>\" has been submitted and requires your signature."
        ];
        $outroLines = [
            "Please review the submitted B-FORM at your earliest convenience by clicking the button above."
        ];


        return (new MailMessage)
            ->subject($subject)
            ->view('emails.notification', [
                'url' => url('/approver/show/' . encrypt($this->all_forms->id)),
                'greeting' => $greeting,
                'introLines' => $introLines,
                'outroLines' => $outroLines,
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
            'title' => 'B-FORM SUBMITTED',
            'message' => 'A new '.$this->all_forms->form->name.' has been submitted and requires your signature.',
            'action_url' => url('/approver/show/' . encrypt($this->all_forms->id)),
        ];
    }
}
