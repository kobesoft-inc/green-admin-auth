# green-admin-auth

Copyright &copy; Kobesoft, Inc. All rights reserved.

## 概要

これはGreen Engineの管理画面システムの認証コンポーネントです。
下記の機能を実装しています。

- 管理者アカウントの管理
- グループの管理
- ロールの管理
- ロールベースのアクセス制御（RBAC）
- パスワードの有効期限機能

## 導入方法

composerでインストール

```shell
composer install kobesoft/green-admin-auth
```

Panelにプラグインとして追加する。

```php

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            :
            :
            ->plugins([
                \Green\AdminAuth\Plugin::make()
            ]);
    }
}
```

## 権限

### 組み込み済みの権限

| 権限               | 説明                                                           |
|------------------|--------------------------------------------------------------|
| 全ての操作            | 全ての操作を許可します。                                                 |
| 全ての管理ユーザーを管理     | 全ての管理ユーザーを管理します。                                             |
| 所属グループの管理ユーザーを管理 | 自分が所属するグループの管理ユーザーを管理します。管理ユーザーに、自分が所属するグループのみを割り当てることができます。 |
| 管理ユーザーにロールを割当    | 管理ユーザーにロールを割り当てることができます。                                     |
| パスワードをリセット       | 管理ユーザーのパスワードをリセットすることができます。                                  |
| 管理ユーザーを削除        | 管理ユーザーを削除することができます。                                          |
| グループを管理          | グループを管理することができます。                                            |
| ロールを管理           | ロールを管理することができます。                                             |

### 権限の実装

権限を実装するには、下記の手順を行います。

権限のクラスを作成します。

```php
use Green\AdminAuth\Permission;

class MyPermission extends Permission
{
    static public function getGroup(): string
    {
        return '権限のグループ名';
    }

    static public function getLabel(): string
    {
        return '権限の名前';
    }
}
```

権限をServiceProviderに登録します。これで管理画面の権限設定画面に表示されるようになります。

```php
use MyPermission;
class MyServiceProvider extends ServiceProvider
{
    public function boot()
    {
        PermissionRegistry::register([
            MyPermission::class
        ]);
    }
}
```

権限を下記のようにチェックします。

```php
if (!$adminUser->hasPermission(MyPermission::class)) {
    abort(403);
}
```

####    

## ロールベースのアクセス制御（RBAC）

### 基本的な考え方

- 操作権限を設定したロールをユーザーかグループに割り当てることで、権限の管理を行います。
- ユーザーは、割り当てられたロールの操作ができます。
- ユーザーに、複数のロールを割り当てることができます。
- ユーザーは、複数のグループに所属することができます。
- グループに、複数のロールを割り当てることができます。
- グループは、ツリー状に構成できます。下位のグループは上位のグループのロールを継承します。

### 例

#### 小規模でシンプルなシステム

- ロールを作ります。例えば、システム管理者と一般ユーザーなどです。
- ユーザーにロールを割り当てます。
- グループ機能は無効化します。

#### 部署毎にユーザー管理をする

- 部署に合わせてグループを作ります。
- グループにロールを割り振ります。
- "所属グループの管理ユーザーを管理"の権限を与えた"ユーザー管理者"のロールを作ります。
- "ユーザー管理者"は、ユーザー管理をするユーザーに付与します。

## カスタマイズ

### 管理ユーザーの項目カスタマイズ

```php
\Green\AdminAuth\Plugin::make()
    // 管理ユーザーは複数のグループに所属できる
    // 複雑な構成が必要な場合に有効化する。 
    ->multipleGroups()

    // 管理ユーザーは複数のロールを割当できる
    // 複雑な構成が必要な場合に有効化する。 
    ->multipleRoles()

    // メールアドレスのログインを無効化する
    // メールアドレスでログインすることがない場合。
    ->loginWithEmail(false)

    // ユーザー名のログインを無効化する
    // ユーザー名でログインすることがない場合。
    ->loginWithUsername(false)

    // 管理ユーザーのアバターを無効化
    ->disableAvatar()

    // 管理ユーザーのメールアドレスを無効化
    // ユーザー名だけで管理画面を運用する場合。
    ->disableEmail()

    // 管理ユーザーのユーザー名を無効化
    // メールアドレスだけで管理画面を運用する場合。
    ->disableUsername()

    // 管理ユーザーのモデルを指定
    // 管理ユーザーに追加の情報や機能を加える場合。\Green\AdminAuth\Models\AdminUserを継承すること。
    ->userModel(MyAdminUserModel::class)

    // 管理グループのモデルを指定
    // 管理グループに追加の情報や機能を加える場合。\Green\AdminAuth\Models\AdminGroupを継承すること。
    ->userModel(MyAdminGroupModel::class)

    // 管理ユーザーの呼び方を指定
    // 運用の実情に合わせて指定
    ->userModelLabel('社員')

    // 管理グループの呼び方を指定
    // 運用の実情に合わせて指定
    ->groupModelLabel('部署')
```


