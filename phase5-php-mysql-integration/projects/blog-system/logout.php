<?php
/**
 * ブログシステム - ログアウト
 *
 * セキュリティ対策:
 * - セッション破棄
 * - クッキー削除
 */

require_once 'functions.php';

// ログインしていない場合はログインページへリダイレクト
if (!isLoggedIn()) {
    redirect('login.php');
}

// ログアウト処理
logoutUser();

// ログインページへリダイレクト
setFlashMessage('success', 'ログアウトしました。');
redirect('login.php');
