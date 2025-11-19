<?php
/**
 * ブログシステム - 認証関連関数
 *
 * ユーザー登録、ログイン、ログアウトなどの認証機能を提供
 * すべての関数でセキュリティ対策を実施
 */

require_once 'config.php';

/**
 * ユーザーを登録
 *
 * @param array $data ユーザーデータ（name, email, password）
 * @return array ['success' => bool, 'message' => string, 'user_id' => int]
 */
function registerUser($data) {
    $pdo = getDB();

    // バリデーション
    $errors = [];

    // 名前のバリデーション
    if (empty($data['name'])) {
        $errors[] = '名前は必須です。';
    } elseif (mb_strlen($data['name']) > 100) {
        $errors[] = '名前は100文字以内で入力してください。';
    }

    // メールアドレスのバリデーション
    if (empty($data['email'])) {
        $errors[] = 'メールアドレスは必須です。';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'メールアドレスの形式が正しくありません。';
    } elseif (mb_strlen($data['email']) > 255) {
        $errors[] = 'メールアドレスは255文字以内で入力してください。';
    } elseif (isEmailTaken($data['email'])) {
        $errors[] = 'このメールアドレスは既に登録されています。';
    }

    // パスワードのバリデーション
    if (empty($data['password'])) {
        $errors[] = 'パスワードは必須です。';
    } elseif (strlen($data['password']) < 8) {
        $errors[] = 'パスワードは8文字以上で入力してください。';
    }

    // パスワード確認のバリデーション
    if (empty($data['password_confirm'])) {
        $errors[] = 'パスワード（確認）は必須です。';
    } elseif ($data['password'] !== $data['password_confirm']) {
        $errors[] = 'パスワードが一致しません。';
    }

    if (!empty($errors)) {
        return ['success' => false, 'message' => implode('<br>', $errors)];
    }

    try {
        // パスワードをハッシュ化（bcryptアルゴリズム使用）
        $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);

        // ユーザーを登録
        $sql = "INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :password_hash)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindValue(':email', $data['email'], PDO::PARAM_STR);
        $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
        $stmt->execute();

        $user_id = $pdo->lastInsertId();

        return [
            'success' => true,
            'message' => '登録が完了しました。ログインしてください。',
            'user_id' => $user_id
        ];
    } catch (PDOException $e) {
        error_log('User registration error: ' . $e->getMessage());
        return ['success' => false, 'message' => '登録に失敗しました。'];
    }
}

/**
 * メールアドレスが既に使用されているかチェック
 *
 * @param string $email メールアドレス
 * @return bool 使用されている場合true
 */
function isEmailTaken($email) {
    $pdo = getDB();

    try {
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    } catch (PDOException $e) {
        error_log('Email check error: ' . $e->getMessage());
        return false;
    }
}

/**
 * ユーザーをログイン
 *
 * @param string $email メールアドレス
 * @param string $password パスワード
 * @return array ['success' => bool, 'message' => string]
 */
function loginUser($email, $password) {
    $pdo = getDB();

    // バリデーション
    if (empty($email) || empty($password)) {
        return ['success' => false, 'message' => 'メールアドレスとパスワードを入力してください。'];
    }

    try {
        // ユーザーを検索
        $sql = "SELECT id, name, email, password_hash FROM users WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch();

        if (!$user) {
            return ['success' => false, 'message' => 'メールアドレスまたはパスワードが正しくありません。'];
        }

        // パスワード検証
        if (!password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'message' => 'メールアドレスまたはパスワードが正しくありません。'];
        }

        // セッションにユーザー情報を保存
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];

        // セッション固定化攻撃対策
        session_regenerate_id(true);

        return ['success' => true, 'message' => 'ログインしました。'];
    } catch (PDOException $e) {
        error_log('Login error: ' . $e->getMessage());
        return ['success' => false, 'message' => 'ログインに失敗しました。'];
    }
}

/**
 * ログアウト
 *
 * @return void
 */
function logoutUser() {
    // セッション変数をクリア
    $_SESSION = [];

    // セッションクッキーを削除
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 42000, '/');
    }

    // セッションを破棄
    session_destroy();
}

/**
 * ログインしているかチェック
 *
 * @return bool ログインしている場合true
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * ログインを必須とする（未ログインの場合はログインページへリダイレクト）
 *
 * @param string $redirect_to リダイレクト先（デフォルト: login.php）
 * @return void
 */
function requireLogin($redirect_to = 'login.php') {
    if (!isLoggedIn()) {
        header('Location: ' . $redirect_to);
        exit;
    }
}

/**
 * 現在ログイン中のユーザー情報を取得
 *
 * @return array|null ユーザー情報（未ログインの場合null）
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }

    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email']
    ];
}

/**
 * 指定されたユーザーIDが現在のユーザーと一致するかチェック
 *
 * @param int $user_id チェックするユーザーID
 * @return bool 一致する場合true
 */
function isCurrentUser($user_id) {
    return isLoggedIn() && $_SESSION['user_id'] == $user_id;
}

/**
 * ユーザーIDからユーザー情報を取得
 *
 * @param int $user_id ユーザーID
 * @return array|null ユーザー情報（存在しない場合null）
 */
function getUserById($user_id) {
    $pdo = getDB();

    try {
        $sql = "SELECT id, name, email, created_at FROM users WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log('Get user error: ' . $e->getMessage());
        return null;
    }
}
