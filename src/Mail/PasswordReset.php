<?php

namespace Green\AdminBase\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    public ?string $email;
    public ?string $username;
    public string $password;
    public string $login;

    /**
     * インスタンスを初期化する
     *
     * @param  string|null  $email
     * @param  string|null  $username
     * @param  string  $password
     * @param  string  $login
     */
    public function __construct(?string $email, ?string $username, string $password, string $login)
    {
        $this->email = $email;
        $this->username = $username;
        $this->password = $password;
        $this->login = $login;
    }

    /**
     * メッセージのエンベロープを取得する
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('green::admin_base.emails.password_reset.subject', ['app' => config('app.name')]),
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
