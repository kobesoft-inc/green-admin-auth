<?php

declare(strict_types=1);

return [
    'navigation_group' => '管理',
    'validations' => [
        'node_parent' => ':attribute の指定が不正です',
    ],
    'pages' => [
        'login' => [
            'title' => 'ログイン',
            'username_or_email' => 'ユーザー名またはメールアドレス',
        ],
    ],
    'permissions' => [
        'admin' => [
            'group' => '管理',
            'super' => '全ての操作',
            'manage_admin_user' => '全てのユーザーを管理',
            'manage_admin_user_in_group' => '所属グループのユーザーを管理',
            'edit_admin_user_role' => 'ユーザーにロールを割当',
            'reset_admin_user_password' => 'パスワードをリセット',
            'delete_admin_user' => 'ユーザーを削除',
            'manage_admin_group' => 'グループを管理',
            'manage_admin_role' => 'ロールを管理',
        ],
    ],
    'admin_user' => [
        'model' => 'ユーザー',
        'name' => '表示名',
        'email' => 'メールアドレス',
        'username' => 'ユーザー名',
        'password' => 'パスワード',
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
        ]
    ],
    'admin_group' => [
        'model' => 'グループ',
        'name' => '名前',
        'created_at' => '登録日',
        'updated_at' => '更新日',
        'parent_id' => '親の管理グループ',
        'users' => '所属ユーザー',
        'roles' => 'ロール',
        'initial_group' => 'デフォルトのグループ',
    ],
    'admin_role' => [
        'model' => 'ロール',
        'name' => '名前',
        'created_at' => '登録日',
        'updated_at' => '更新日',
        'users' => 'ユーザー',
        'groups' => 'グループ',
        'initial_role' => '特権',
    ],
];