<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use App\Models\CompanyProfile;

class UserInvitationNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        // Ambil data profil perusahaan
        $companyProfile = CompanyProfile::first(); 
        
        // Buat URL aman untuk setup password yang berlaku selama 24 jam
        $setupPasswordUrl = URL::temporarySignedRoute(
            'password.setup', // Nama route yang akan kita buat
            Carbon::now()->addHours(24),
            ['user' => $notifiable->id]
        );

        return (new MailMessage)
                    ->subject('Undangan untuk Mengatur Akun Anda di ' . $companyProfile->nama_perusahaan)
                    ->greeting('Halo, ' . $notifiable->name . '!')
                    ->line('Anda telah didaftarkan ke dalam sistem penggajian ' . $companyProfile->nama_perusahaan . '.')
                    ->line('Silakan klik tombol di bawah ini untuk mengatur password akun Anda dan mulai.')
                    ->action('Atur Password Anda', $setupPasswordUrl)
                    ->line('Link ini akan kedaluwarsa dalam 24 jam.')
                    ->line('Jika Anda tidak merasa mendaftar, Anda bisa mengabaikan email ini.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
