<?php

namespace Green\AdminAuth\Listeners;

use Green\AdminAuth\Models\User\Contracts\ShouldLogLogin;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

/**
 * 管理ユーザーのログインを記録する
 */
class LogAdminLogin
{
    /**
     * インスタンスを初期化する
     *
     * @param Request $request
     */
    public function __construct(protected Request $request)
    {
    }

    /**
     * イベントの処理
     *
     * @param Login $login
     * @return void
     */
    public function handle(Login $login): void
    {
        // ログイン記録をするユーザーでなければ処理しない
        if (!($login->user instanceof ShouldLogLogin)) {
            return;
        }

        // UserAgentを解析
        $agent = new Agent();
        $agent->setUserAgent($this->request->userAgent());

        // ログイン履歴を記録
        $login->user->loginLogs()->create([
            'languages' => join(',', $agent->languages($this->request->header('Accept-Language'))),
            'device' => $agent->device(),
            'platform' => ($platform = $agent->platform()) . ' ' . $agent->version($platform),
            'browser' => ($browser = $agent->browser()) . ' ' . $agent->version($browser),
            'ip_address' => $this->request->getClientIp(),
        ]);
    }
}
