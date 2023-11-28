<?php

namespace Green\AdminAuth\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * インスタンスを初期化する
     *
     * @param string|null $email
     * @param string|null $username
     * @param string $password
     * @param string $login
     */
    public function __construct(
        public ?string $email,
        public ?string $username,
        public string  $password,
        public string  $login
    )
    {
    }

    /**
     * メッセージのエンベロープを取得する
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('green::admin-auth.emails.password-reset.subject', ['app' => config('app.name')]),
        );
    }

    /**
     * メッセージのコンテンツ定義を取得する
     *
     * @return Content
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'green::emails.admin.password-reset',
        );
    }
}
