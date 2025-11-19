<?php
/**
 * ブログシステム - 記事削除
 *
 * セキュリティ対策:
 * - ログイン必須
 * - 作者のみ削除可能
 * - GETリクエストでも動作（確認画面用）
 * - POSTリクエストで実際に削除（CSRF対策）
 */

require_once 'functions.php';

// ログイン必須
requireLogin();

// 記事IDを取得
$post_id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;

if ($post_id <= 0) {
    setFlashMessage('error', '不正な記事IDです。');
    redirect('index.php');
}

// 記事を取得
$post = getPostById($post_id);

if (!$post) {
    setFlashMessage('error', '記事が見つかりませんでした。');
    redirect('index.php');
}

// 作者のみ削除可能
if (!isCurrentUser($post['user_id'])) {
    setFlashMessage('error', 'この記事を削除する権限がありません。');
    redirect('index.php');
}

// POSTリクエストで実際に削除
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRFトークン検証
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        setFlashMessage('error', '不正なリクエストです。');
        redirect('index.php');
    }

    if (deletePost($post_id)) {
        setFlashMessage('success', '記事を削除しました。');
        regenerateCsrfToken();
        redirect('index.php');
    } else {
        setFlashMessage('error', '記事の削除に失敗しました。');
        redirect('view.php?id=' . $post_id);
    }
}

// GETリクエストの場合は確認画面を表示
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>記事を削除 - ブログシステム</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5; }
        .header { background-color: #2196F3; color: white; padding: 20px 0; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .header-content { max-width: 900px; margin: 0 auto; padding: 0 20px; }
        .header a { color: white; text-decoration: none; }
        .header a:hover { text-decoration: underline; }
        .container { max-width: 900px; margin: 50px auto; padding: 0 20px; }
        .confirm-box { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-top: 4px solid #f44336; }
        h1 { color: #f44336; margin-bottom: 20px; }
        .post-info { background-color: #f5f5f5; padding: 20px; border-radius: 4px; margin: 20px 0; }
        .post-info h2 { color: #333; margin-bottom: 10px; }
        .post-info p { color: #666; line-height: 1.6; }
        .warning { background-color: #fff3e0; color: #e65100; padding: 15px; border-radius: 4px; margin: 20px 0; border-left: 4px solid #e65100; }
        .form-actions { display: flex; gap: 10px; margin-top: 30px; }
        .btn { padding: 12px 30px; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; font-weight: bold; text-decoration: none; display: inline-block; text-align: center; }
        .btn-danger { background-color: #f44336; color: white; }
        .btn-danger:hover { background-color: #d32f2f; }
        .btn-secondary { background-color: #9E9E9E; color: white; }
        .btn-secondary:hover { background-color: #757575; }
        .back-link { display: inline-block; margin-bottom: 20px; color: #2196F3; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <a href="index.php">📝 ブログシステム</a>
        </div>
    </div>

    <div class="container">
        <a href="view.php?id=<?php echo h($post_id); ?>" class="back-link">← 記事に戻る</a>

        <div class="confirm-box">
            <h1>🗑️ 記事を削除</h1>

            <div class="post-info">
                <h2><?php echo h($post['title']); ?></h2>
                <p>
                    カテゴリ: <?php echo h($post['category_name'] ?: '未分類'); ?><br>
                    作成日: <?php echo date('Y年m月d日 H:i', strtotime($post['created_at'])); ?><br>
                    閲覧数: <?php echo h($post['view_count']); ?>回
                </p>
            </div>

            <div class="warning">
                ⚠️ この操作は取り消せません。本当にこの記事を削除してもよろしいですか？
            </div>

            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo h($_SESSION['csrf_token']); ?>">
                <input type="hidden" name="id" value="<?php echo h($post_id); ?>">

                <div class="form-actions">
                    <button type="submit" class="btn btn-danger">🗑️ 削除する</button>
                    <a href="view.php?id=<?php echo h($post_id); ?>" class="btn btn-secondary">キャンセル</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
