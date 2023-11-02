# green-admin-base

Copyright &copy; Kobesoft, Inc. All rights reserved.

## 概要

これはGreen Engineの管理画面システムの基盤コンポーネントです。
下記の機能を実装しています。

- 管理者アカウントの管理
- グループの管理
- ロールの管理
- ロールベースのアクセス制御（RBAC）
- パスワードの有効期限機能

## 導入方法

composerでインストール

```shell
composer install kobesoft/green-admin-base
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
                \Green\AdminBase\Plugin::make()
            ]);
    }
}
```

## ロールベースのアクセス制御（RBAC）

### 基本的な考え方

下記の3要素があります。

- ユーザー。各ユーザーを表します。 
  - ユーザーに複数のロールを割り当てられます。
  - ユーザーは複数のグループに所属できます。
- グループ。
  - グループに複数のロールを割り当てられます。
  - グループはツリー状に構成できます。下位のグループは上位のグループのロールを継承します。
- ロール
  - 操作権限を設定したものです。

ユーザーができる操作は、割り当てられたロール、所属グループのロールを全て加えたものとなります。

### 例

#### 小規模でシンプルなシステム

- ロールを作ります。例えば、システム管理者と一般ユーザーなどです。
- ユーザーにロールを割り振ります。
- グループ機能は無効化します。

#### 部署毎にユーザー管理をする

- 部署に合わせてグループを作ります。
- グループにロールを割り振ります。
- グループ内の"ユーザー管理ロール"を作り、"所属グループの管理ユーザーを管理"のパーミッションを与えます。
- ユーザー管理をするユーザーにのみ、"ユーザー管理ロール"を与えます。
- 注意点として、"ユーザー管理ロール"には"管理ユーザーにロールを割当"のロールを与えないようにします。

## カスタマイズ

### 管理ユーザーの項目カスタマイズ

```php
\Green\AdminBase\Plugin::make()
    // #1 管理ユーザーは複数のグループに所属できる
    // 複雑な構成が必要な場合に有効化する。 
    ->multipleGroups()

    // #2 管理ユーザーは複数のロールを割当できる
    // 複雑な構成が必要な場合に有効化する。 
    ->multipleRoles()

    // #3 メールアドレスのログインを無効化する
    // メールアドレスでログインすることがない場合。
    ->loginWithEmail(false)

    // #4 ユーザー名のログインを無効化する
    // ユーザー名でログインすることがない場合。
    ->loginWithUsername(false)

    // #5 管理ユーザーのメールアドレスを無効化
    // ユーザー名だけで管理画面を運用する場合。
    ->emailDisabled()

    // #6 管理ユーザーのユーザー名を無効化
    // メールアドレスだけで管理画面を運用する場合。
    ->usernameDisabled()

    // #7 管理ユーザーのモデルを指定
    // 管理ユーザーに追加の情報や機能を加える場合。\Green\AdminBase\Models\AdminUserを継承すること。
    ->userModel(MyAdminUserModel::class)

    // #8 管理グループのモデルを指定
    // 管理グループに追加の情報や機能を加える場合。\Green\AdminBase\Models\AdminGroupを継承すること。
    ->userModel(MyAdminGroupModel::class)

    // #9 管理ユーザーの呼び方を指定
    // 運用の実情に合わせて指定
    ->userModelLabel('社員')

    // #10 管理グループの呼び方を指定
    // 運用の実情に合わせて指定
    ->groupModelLabel('部署')
```


