<?php

namespace Green\AdminAuth\Filament\Pages;

use Exception;
use Filament\Facades\Filament;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Green\AdminAuth\Facades\IdProviderRegistry;
use Green\AdminAuth\Models\User\Contracts\ShouldExpirePassword;
use Green\AdminAuth\Models\User\Contracts\ShouldHaveUsername;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

/**
 * ログインページ
 */
class Login extends \Filament\Pages\Auth\Login
{
    protected static string $view = 'green::filament.pages.login';

    /**
     * ログイン処理を行う
     *
     * @return mixed
     * @throws Exception
     */
    public function login(): mixed
    {
        // 通常のログイン処理
        try {
            $loginResponse = parent::authenticate();
            if ($loginResponse === null) {
                return null;
            }
        } catch (Exception $e) {
            $this->form->fill([]); // 入力をクリアする
            throw $e;
        }

        // パスワードの有効期限が切れている場合の処理
        $user = Filament::auth()->user();
        if ($user instanceof ShouldExpirePassword && $user->isPasswordExpired()) {
            // セッションに、ログインしようとしたユーザーIDを設定する
            session()->put(PasswordExpired::PASSWORD_EXPIRED_USER_ID, $user->id);

            // ログアウト処理をする
            Filament::auth()->logout();

            // パスワード有効期限切れのページにリダイレクトする
            return $this->redirectToPasswordExpired();
        }

        // ログインOK
        return $loginResponse;
    }

    /**
     * ログインの見出し
     *
     * @return string|Htmlable
     */
    public function getHeading(): string|Htmlable
    {
        return __('green::admin-auth.pages.login.heading');
    }

    /**
     * ユーザー名またはメールアドレスの入力フォームを取得する
     *
     * @return Component
     */
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label($this->getEmailFormLabel())
            ->required()
            ->email(!$this->canLoginWithUsername())
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    /**
     * ユーザー名またはメールアドレスのラベルを取得する
     *
     * @return string
     */
    protected function getEmailFormLabel(): string
    {
        $canLoginWithEmail = $this->canLoginWithEmail();
        $canLoginWithUsername = $this->canLoginWithUsername();
        if ($canLoginWithEmail && $canLoginWithUsername) {
            return __('green::admin-auth.pages.login.username-or-email');
        } elseif ($canLoginWithUsername) {
            return __('green::admin-auth.pages.login.username');
        } elseif ($canLoginWithEmail) {
            return __('green::admin-auth.pages.login.email');
        } else {
            throw new RuntimeException('Please enable login with (username or email)');
        }
    }

    /**
     * フォーム入力から認証情報を取得する
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'email' => function (Builder $query) use ($data) {
                if ($this->canLoginWithEmail()) {
                    $query->orWhere('email', $data['email']);
                }
                if ($this->canLoginWithUsername()) {
                    $query->orWhere('username', $data['email']);
                }
            },
            'password' => $data['password'],
            'is_active' => true,
        ];
    }

    /**
     * 現在のGuardのユーザーモデルのインスタンスを取得する
     *
     * @return string ユーザーモデルのクラス名
     */
    protected function getAuthProviderModel(): string
    {
        $guard = Auth::guard(\filament()->getAuthGuard());
        $provider = $guard->getProvider();
        if (!$provider instanceof EloquentUserProvider) {
            throw new RuntimeException('The current provider is not an EloquentUserProvider.');
        }
        return $provider->getModel();
    }

    /**
     * メールアドレスでログインできるか？
     *
     * @return bool メールアドレスでログインできる場合はtrue、それ以外はfalse
     */
    protected function canLoginWithEmail(): bool
    {
        $class = $this->getAuthProviderModel();
        if (is_subclass_of($class, ShouldHaveUsername::class)) {
            return $class::canLoginWithEmail();
        } else {
            return true;
        }
    }

    /**
     * ユーザー名でログインできるか？
     *
     * @return bool ユーザー名でログインできる場合はtrue、それ以外はfalse
     */
    protected function canLoginWithUsername(): bool
    {
        $class = $this->getAuthProviderModel();
        if (is_subclass_of($class, ShouldHaveUsername::class)) {
            return $class::canLoginWithUsername();
        } else {
            return false;
        }
    }

    /**
     * パスワード有効期限切れのページにリダイレクトする
     *
     * @return mixed
     * @throws Exception
     */
    public function redirectToPasswordExpired(): mixed
    {
        return redirect()->route("filament." . filament()->getCurrentPanel()->getId() . ".password-expired");
    }

    /**
     * IdPのログインアクションを取得する
     *
     * @return array
     */
    protected function getIdProviderActions(): array
    {
        $actions = [];
        foreach (IdProviderRegistry::all(Filament::getAuthGuard()) as $provider) {
            $actions[] = $provider->getLoginAction();
        }
        return $actions;
    }
}
