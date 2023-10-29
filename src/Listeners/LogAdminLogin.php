<?php

namespace Green\AdminBase\Listeners;

use Green\AdminBase\Models\AdminLoginLog;
use Green\AdminBase\Models\AdminUser;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class LogAdminLogin
{
    protected Request $request;

    /**
     * インスタンスを初期化する
     *
     * @param  Request  $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * イベントの処理
     *
     * @param  Login  $login
     * @return void
     */
    public function handle(Login $login): void
    {
        // 管理ユーザーのログインでなければ処理しない
        if (!$login->user instanceof AdminUser) {
            return;
        }

        // UserAgentを解析
        $agent = new Agent();
        $agent->setUserAgent($this->request->userAgent());

        // ログイン履歴を記録
        AdminLoginLog::create([
            'admin_user_id' => $login->user->id,
            'languages' => join(',', $agent->languages($this->request->header('Accept-Language'))),
            'device' => $agent->device(),
            'platform' => ($platform = $agent->platform()).' '.$agent->version($platform),
            'browser' => ($browser = $agent->browser()).' '.$agent->version($browser),
            'ip_address' => $this->request->getClientIp(),
        ]);
    }
}