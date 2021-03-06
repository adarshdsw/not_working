<?php

namespace App\Notifications;

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

use App\Models\User;
use App\Models\Ebill;

class NotifyShippingStatus extends Notification
{
    use Queueable;

    private $ebill = '';
    private $user_name = '';

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Ebill $ebill)
    {
        $this->ebill = $ebill;
        // find user
        $user = User::find($ebill->user_id);
        if(!empty($user)){
            $this->user_name = ($user->name) ? $user->name : '';
        }
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database',FcmChannel::class];
    }

    public function toFcm($notifiable)
    {
        $title = __('messages.notification.shipping_status.title');
        $body = __('messages.notification.shipping_status.body', ['user'=>$this->user_name, 'shipping_status'=> ($this->ebill->shipping_status== 1) ? "Processed" : "Holding"]);
        $custom_data = json_encode(['ebil_id'=>$this->ebill->id, 'case' => 'shipping_status']);
        
        return FcmMessage::create()
            ->setData(['data' => $custom_data])
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
            'title' => __('messages.notification.shipping_status.title'),
            'body' => __('messages.notification.shipping_status.body', ['user'=>$this->user_name, 'shipping_status'=> ($this->ebill->shipping_status== 1) ? "Processed" : "Holding"]),
            'url' => '/',
            'custom_data' => ['ebil_id'=>$this->ebill->id, 'case' => 'shipping_status'],
        ];
    }
}
