<?php declare(strict_types = 1);

namespace App\Notifications;

use App\Enums\Queue;
use Closure;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

/**
 * Class ResetPasswordNotification
 *
 * @package App\Notifications
 */
class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;

    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Set a callback that should be used when building the notification mail message.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public static function toMailUsing(Closure $callback): void
    {
        static::$toMailCallback = $callback;
    }

    /**
     * Create a notification instance.
     *
     * @param  string  $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
        $this->onQueue(Queue::MAIL);
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array<string>|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        return (new MailMessage)
            ->subject(trans("mail.password.subject"))
            ->line(trans("mail.password.why"))
            ->action(
                trans("mail.password.subject"),
                url(config('app.url') . route(
                    'password.reset',
                    ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()],
                    false
                ))
            )
            ->line(Lang::get(
                "mail.password.expires",
                ['count' => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire')]
            ))
            ->line(trans("mail.password.nullable"))
            ->greeting(trans("mail.password.greeting"))
            ->salutation(trans("mail.password.salutation"));
    }
}
