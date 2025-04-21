<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-test-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Yandex Mail Test Email';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $recipient = 'sikko4890@gmail.com';
        $subject   = 'Тестовое письмецо';
        $content   = 'Это тестовое письмо!';

        Mail::raw($content, static function ($message) use ($recipient, $subject) {
            $message->to($recipient)
                ->subject($subject);
        });

        $this->info('Письмо отправлено на ' . $recipient);
        return 0;
    }
}
