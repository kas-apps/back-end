<?php
/**
 * ブログシステム - 記事追加
 *
 * セキュリティ対策:
 * - ログイン必須
 * - CSRF対策
 * - バリデーション
 * - XSS対策
 */

require_once 'functions.php';

// ログイン必須
requireLogin();

$errors = [];
$form_data = [
    'title' => '',
    'content' => '',
    'excerpt' => '',
    'category_id' => '',
    'status' => 'draft'
];

// カテゴリ一覧を取得
$categories = getAllCategories();

// POSTリクエスト処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRFトークン検証
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        $errors[] = '不正なリクエストです。';
    } else {
        // フォームデータを取得
        $form_data['title'] = trim($_POST['title'] ?? '');
        $form_data['content'] = trim($_POST['content'] ?? '');
        $form_data['excerpt'] = trim($_POST['excerpt'] ?? '');
        $form_data['category_id'] = $_POST['category_id'] ?? '';
        $form_data['status'] = $_POST['status'] ?? 'draft';

        // バリデーション
        $title_errors = validateTitle($form_data['title']);
        if (!empty($title_errors)) {
            $errors = array_merge($errors, $title_errors);
        }

        $content_errors = validateContent($form_data['content']);
        if (!empty($content_errors)) {
            $errors = array_merge($errors, $content_errors);
        }

        if (!validateStatus($form_data['status'])) {
            $errors[] = 'ステータスが不正です。';
        }

        // カテゴリIDのバリデーション（任意）
        if (!empty($form_data['category_id']) && !getCategoryById($form_data['category_id'])) {
            $errors[] = 'カテゴリが不正です。';
        }

        // 抜粋の長さチェック
        if (!empty($form_data['excerpt']) && mb_strlen($form_data['excerpt']) > 500) {
            $errors[] = '抜粋は500文字以内で入力してください。';
        }

        // エラーがなければ作成
        if (empty($errors)) {
            $post_data = [
                'user_id' => $_SESSION['user_id'],
                'title' => $form_data['title'],
                'content' => $form_data['content'],
                'excerpt' => !empty($form_data['excerpt']) ? $form_data['excerpt'] : generateExcerpt($form_data['content']),
                'category_id' => !empty($form_data['category_id']) ? $form_data['category_id'] : null,
                'status' => $form_data['status']
            ];

            $post_id = createPost($post_data);

            if ($post_id) {
                setFlashMessage('success', '記事を投稿しました。');
                regenerateCsrfToken();
                redirect('view.php?id=' . $post_id);
            } else {
                $errors[] = '記事の投稿に失敗しました。';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>記事を書く - ブログシステム</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5; }
        .header { background-color: #2196F3; color: white; padding: 20px 0; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .header-content { max-width: 900px; margin: 0 auto; padding: 0 20px; }
        .header a { color: white; text-decoration: none; }
        .header a:hover { text-decoration: underline; }
        .container { max-width: 900px; margin: 30px auto; padding: 0 20px; }
        .form-container { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        h1 { margin-bottom: 30px; color: #333; }
        .error-messages { background-color: #ffebee; color: #c62828; padding: 15px; border-radius: 4px; margin-bottom: 20px; border-left: 4px solid #c62828; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        .form-group input[type="text"],
        .form-group textarea,
        .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; font-family: inherit; }
        .form-group textarea { min-height: 300px; resize: vertical; }
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus { outline: none; border-color: #2196F3; }
        .help-text { font-size: 12px; color: #666; margin-top: 5px; }
        .form-actions { display: flex; gap: 10px; margin-top: 30px; }
        .btn { padding: 12px 30px; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; font-weight: bold; text-decoration: none; display: inline-block; text-align: center; }
        .btn-primary { background-color: #2196F3; color: white; }
        .btn-primary:hover { background-color: #1976D2; }
        .btn-secondary { background-color: #9E9E9E; color: white; }
        .btn-secondary:hover { background-color: #757575; }
        .btn-success { background-color: #4CAF50; color: white; }
        .btn-success:hover { background-color: #45a049; }
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
        <a href="index.php" class="back-link">← 記事一覧へ戻る</a>

        <div class="form-container">
            <h1>✏️ 記事を書く</h1>

            <?php if (!empty($errors)): ?>
                <div class="error-messages">
                    <?php foreach ($errors as $error): ?>
                        <?php echo h($error); ?><br>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo h($_SESSION['csrf_token']); ?>">

                <div class="form-group">
                    <label for="title">タイトル <span style="color: red;">*</span></label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="<?php echo h($form_data['title']); ?>"
                        required
                        maxlength="255"
                    >
                </div>

                <div class="form-group">
                    <label for="content">内容 <span style="color: red;">*</span></label>
                    <textarea
                        id="content"
                        name="content"
                        required
                    ><?php echo h($form_data['content']); ?></textarea>
                    <div class="help-text">Markdown形式で記述できます（見出し: # ## ###、コードブロック: ```）</div>
                </div>

                <div class="form-group">
                    <label for="excerpt">抜粋（任意）</label>
                    <textarea
                        id="excerpt"
                        name="excerpt"
                        style="min-height: 80px;"
                        maxlength="500"
                    ><?php echo h($form_data['excerpt']); ?></textarea>
                    <div class="help-text">記事一覧に表示される要約文。空白の場合は自動生成されます。</div>
                </div>

                <div class="form-group">
                    <label for="category_id">カテゴリ</label>
                    <select name="category_id" id="category_id">
                        <option value="">選択してください</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo h($category['id']); ?>"
                                <?php echo ($form_data['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo h($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status">公開設定 <span style="color: red;">*</span></label>
                    <select name="status" id="status" required>
                        <option value="draft" <?php echo ($form_data['status'] === 'draft') ? 'selected' : ''; ?>>下書き</option>
                        <option value="published" <?php echo ($form_data['status'] === 'published') ? 'selected' : ''; ?>>公開</option>
                    </select>
                    <div class="help-text">下書きは自分のみ閲覧できます。</div>
                </div>

                <div class="form-actions">
                    <button type="submit" name="status" value="published" class="btn btn-success">✅ 公開する</button>
                    <button type="submit" name="status" value="draft" class="btn btn-primary">💾 下書き保存</button>
                    <a href="index.php" class="btn btn-secondary">キャンセル</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
