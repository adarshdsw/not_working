<?php

namespace App\Notifications;

use Illuminate\Support\Str;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\AndroidConfig;
use NotificationChannels\Fcm\Resources\AndroidFcmOptions;
use NotificationChannels\Fcm\Resources\AndroidNotification;
use NotificationChannels\Fcm\Resources\ApnsConfig;
use NotificationChannels\Fcm\Resources\ApnsFcmOptions;

use Kreait\Firebase\Exception\Messaging\InvalidArgument;
use Kreait\Firebase\Exception\Messaging\NotFound;

use App\Models\Scheme;

class NewSchemeCreated extends Notification
{
    use Queueable;

    private $notify_scheme;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Scheme $scheme)
    {
        $this->notify_scheme = $scheme;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable)
    {
        $title = $this->notify_scheme->title;
        $body = Str::limit(strip_tags($this->notify_scheme->description), 20, ' (...)');
        $custom_data = json_encode(['scheme_id'=>$this->notify_scheme->id, 'case' => 'scheme']);
        
        return FcmMessage::create()
            ->setData(['data'=>$custom_data])
            ->setNotification(\NotificationChannels\Fcm\Resources\Notification::create()
                ->setTitle($title)
                ->setBody($body)
                // ->setImage('http://192.168.0.131:8000/uploads/user_image/05-Mar-2021_user_img_1614920313223914208.jpg')
            )
            ->setAndroid(
                AndroidConfig::create()
                    ->setFcmOptions(AndroidFcmOptions::create()->setAnalyticsLabel('analytics'))
                    ->setNotification(AndroidNotification::create()->setColor('#0A0A0A'))
            )->setApns(
                ApnsConfig::create()
                    ->setFcmOptions(ApnsFcmOptions::create()->setAnalyticsLabel('analytics_ios')));
    }

    // optional method when using kreait/laravel-firebase:^3.0, this method can be omitted, defaults to the default project
    public function fcmProject($notifiable, $message)
    {
        // $message is what is returned by `toFcm`
        return 'app'; // name of the firebase project to use
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'title' => __('messages.notification.new_news.title'),
            'body' => __('messages.notification.new_news.body'),
            'url' => '/',
            'custom_data' => ['scheme_id'=>$this->notify_scheme->id, 'case' => 'scheme'],
        ];
    }
}