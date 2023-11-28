<?php

declare(strict_types=1);

return [
    // 管理メニューの名前
    'navigation-group' => '管理',

    // バリデーションのエラーメッセージ
    'validations' => [
        'node-parent' => ':attribute の指定が不正です',
        'email-required' => '送信先メールアドレスが設定されていません',
    ],

    // ページ
    'pages' => [
        'login' => [
            'heading' => 'ログイン',
            'username' => 'ユーザー名',
            'email' => 'メールアドレス',
            'username-or-email' => 'ユーザー名またはメールアドレス',
        ],
        'password-expired' => [
            'heading' => 'パスワード変更',
            'subheading' => 'パスワードの有効期限が切れました',
            'current-password' => '現在のパスワード',
            'new-password' => '新しいパスワード',
            'change-password' => 'パスワードを変更',
            'incorrect-password' => 'パスワードが違います',
            'password-changed' => 'パスワードを変更しました',
        ]
    ],

    // メール
    'emails' => [
        'password-reset' => [
            'subject' => ':app のパスワードを発行しました',
            'email' => 'メールアドレス',
            'username' => 'ユーザー名',
            'password' => 'パスワード',
            'login' => ':app にログイン',
        ],
    ],

    // パーミッション
    'permissions' => [
        'admin' => [
            'group' => '管理',
            'super' => '全ての操作',
            'manage-admin-user' => '全ての管理ユーザーを管理',
            'manage-admin-user-in-group' => '所属グループの管理ユーザーを管理',
            'edit-admin-user-role' => '管理ユーザーにロールを割当',
            'reset-admin-user-password' => 'パスワードをリセット',
            'delete-admin-user' => '管理ユーザーを削除',
            'manage-admin-group' => 'グループを管理',
            'manage-admin-role' => 'ロールを管理',
        ],
    ],

    // 管理ユーザー
    'admin-user' => [
        'model' => '管理ユーザー',
        'name' => '表示名',
        'email' => 'メールアドレス',
        'username' => 'ユーザー名',
        'password' => 'パスワード',
        'generate-password' => 'パスワードを自動生成する',
        'force-change-password' => '初回ログイン時にパスワードの変更を要求する',
        'send-password' => '完了時にパスワードをメールで送信',
        'is-active' => '状態',
        'avatar' => 'アバター',
        'created-at' => '登録日',
        'updated-at' => '更新日',
        'groups' => '所属グループ',
        'roles' => 'ロール',
        'last-login-at' => '最終ログイン',
        'initial-user' => '管理者',
        'actions' => [
            'suspend' => 'ログインを停止',
            'suspend-succeed' => 'ログインを停止しました',
            'resume' => 'ログインを再開',
            'resume-succeed' => 'ログインを再開しました',
            'reset-password' => 'パスワードをリセット',
            'reset-password-succeed' => 'パスワードをリセットしました',
        ]
    ],

    // 管理グループ
    'admin-group' => [
        'model' => 'グループ',
        'name' => '名前',
        'created-at' => '登録日',
        'updated-at' => '更新日',
        'parent-id' => '親のグループ',
        'users' => '所属管理ユーザー',
        'roles' => 'ロール',
        'initial-group' => 'デフォルトのグループ',
    ],

    // 管理ロール
    'admin-role' => [
        'model' => 'ロール',
        'name' => '名前',
        'created-at' => '登録日',
        'updated-at' => '更新日',
        'users' => '管理ユーザー',
        'groups' => 'グループ',
        'initial-role' => '特権',
    ],
];
