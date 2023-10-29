<?php

declare(strict_types=1);

return [
    // 管理メニューの名前
    'navigation_group' => '管理',

    // バリデーションのエラーメッセージ
    'validations' => [
        'node_parent' => ':attribute の指定が不正です',
        'email_is_not_set' => '送信先メールアドレスが設定されていません',
    ],

    // ページ
    'pages' => [
        'login' => [
            'heading' => 'ログイン',
            'username' => 'ユーザー名',
            'email' => 'メールアドレス',
            'username_or_email' => 'ユーザー名またはメールアドレス',
        ],
        'password_expired' => [
            'heading' => 'パスワード変更',
            'subheading' => 'パスワードの有効期限が切れました',
            'current_password' => '現在のパスワード',
            'new_password' => '新しいパスワード',
            'change_password' => 'パスワードを変更',
            'invalid_password' => 'パスワードが違います',
        ]
    ],

    // メール
    'emails' => [
        'password_reset' => [
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
            'manage_admin_user' => '全ての管理ユーザーを管理',
            'manage_admin_user_in_group' => '所属グループの管理ユーザーを管理',
            'edit_admin_user_role' => '管理ユーザーにロールを割当',
            'reset_admin_user_password' => 'パスワードをリセット',
            'delete_admin_user' => '管理ユーザーを削除',
            'manage_admin_group' => 'グループを管理',
            'manage_admin_role' => 'ロールを管理',
        ],
    ],

    // 管理ユーザー
    'admin_user' => [
        'model' => '管理ユーザー',
        'name' => '表示名',
        'email' => 'メールアドレス',
        'username' => 'ユーザー名',
        'password' => 'パスワード',
        'generate_password' => 'パスワードを自動生成する',
        'require_change_password' => '初回ログイン時にパスワードの変更を要求する',
        'send_password' => '完了時にパスワードをメールで送信',
        'is_active' => '状態',
        'avatar' => 'アバター',
        'created_at' => '登録日',
        'updated_at' => '更新日',
        'groups' => '所属グループ',
        'roles' => 'ロール',
        'last_login_at' => '最終ログイン',
        'initial_user' => '管理者',
        'actions' => [
            'suspend' => 'ログインを停止',
            'suspend_succeed' => 'ログインを停止しました',
            'resume' => 'ログインを再開',
            'resume_succeed' => 'ログインを再開しました',
            'reset_password' => 'パスワードをリセット',
            'reset_password_succeed' => 'パスワードをリセットしました',
        ]
    ],

    // 管理グループ
    'admin_group' => [
        'model' => 'グループ',
        'name' => '名前',
        'created_at' => '登録日',
        'updated_at' => '更新日',
        'parent_id' => '親のグループ',
        'users' => '所属管理ユーザー',
        'roles' => 'ロール',
        'initial_group' => 'デフォルトのグループ',
    ],

    // 管理ロール
    'admin_role' => [
        'model' => 'ロール',
        'name' => '名前',
        'created_at' => '登録日',
        'updated_at' => '更新日',
        'users' => '管理ユーザー',
        'groups' => 'グループ',
        'initial_role' => '特権',
    ],
];