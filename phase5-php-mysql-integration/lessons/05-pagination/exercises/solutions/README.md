# Lesson 05: ページング処理 - 解答例 📄

このファイルには、Lesson 05のすべての演習問題の詳細な解答例と解説が含まれています。

---

## 目次

- [基礎編](#基礎編)
  - [問題5-1: 基本的なページング処理](#問題5-1基本的なページング処理)
  - [問題5-2: 総ページ数の計算](#問題5-2総ページ数の計算)
  - [問題5-3: ページネーションリンクの表示](#問題5-3ページネーションリンクの表示)
- [応用編](#応用編)
  - [問題5-4: 省略表示（...）のページネーション](#問題5-4省略表示のページネーション)
  - [問題5-5: 1ページあたりの件数を変更可能にする](#問題5-51ページあたりの件数を変更可能にする)
  - [問題5-6: 検索結果のページング](#問題5-6検索結果のページング)
- [セキュリティチャレンジ](#セキュリティチャレンジ)
  - [問題5-7: カテゴリフィルタ付きページング](#問題5-7カテゴリフィルタ付きページング)
  - [問題5-8: ページネーション関数の作成](#問題5-8ページネーション関数の作成)
- [総合チャレンジ](#総合チャレンジ)
  - [問題5-9: 完全な記事検索・一覧システム](#問題5-9完全な記事検索一覧システム)
  - [問題5-10: 無限スクロール](#問題5-10無限スクロール)

---

## 基礎編

### 問題5-1：基本的なページング処理

記事一覧に基本的なページング処理を実装します（1ページ10件表示）。

#### ✅ 解答例

**pagination_basic.php**

```php
<?php
// データベース接続
$host = 'localhost';
$port = '8889';
$dbname = 'phase5_practice';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die('データベース接続エラー: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

// ページ番号を取得
$current_page = $_GET['page'] ?? 1;
$current_page = (int)$current_page;

// 不正な値のチェック
if ($current_page < 1) {
    // ページ1にリダイレクト
    header('Location: ?page=1');
    exit;
}

// 1ページあたりの件数
$per_page = 10;

// OFFSETを計算（重要：ページ1の場合は0件スキップ）
$offset = ($current_page - 1) * $per_page;

// データを取得（プリペアドステートメント）
$stmt = $pdo->prepare("SELECT * FROM articles ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>記事一覧 - ページング</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .article {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .article h2 {
            margin-top: 0;
        }
        .article-meta {
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <h1>記事一覧</h1>

    <p>現在のページ: <?php echo htmlspecialchars($current_page, ENT_QUOTES, 'UTF-8'); ?></p>

    <?php if (empty($articles)): ?>
        <p>記事が見つかりませんでした。</p>
    <?php else: ?>
        <?php foreach ($articles as $article): ?>
            <div class="article">
                <h2><?php echo htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                <p><?php echo htmlspecialchars($article['content'], ENT_QUOTES, 'UTF-8'); ?></p>
                <div class="article-meta">
                    カテゴリ: <?php echo htmlspecialchars($article['category'], ENT_QUOTES, 'UTF-8'); ?> |
                    投稿日: <?php echo htmlspecialchars($article['created_at'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
```

#### ✅ セキュリティポイント

1. **型キャスト**: `$current_page = (int)$current_page;` でページ番号を整数に変換
2. **バリデーション**: ページ番号が1未満の場合はリダイレクト
3. **プリペアドステートメント**: SQLインジェクション対策
4. **bindValue() with PDO::PARAM_INT**: LIMIT/OFFSETには必ず型指定
5. **XSS対策**: すべての出力に `htmlspecialchars()` を使用
6. **エラーハンドリング**: データベース接続エラーを安全に表示

#### 💡 コードのポイント

1. **OFFSETの計算**
```php
$offset = ($current_page - 1) * $per_page;
// ページ1: (1 - 1) * 10 = 0  → 0件スキップ（1〜10件目）
// ページ2: (2 - 1) * 10 = 10 → 10件スキップ（11〜20件目）
// ページ3: (3 - 1) * 10 = 20 → 20件スキップ（21〜30件目）
```

2. **bindValue() vs bindParam()**
```php
// ✅ 推奨: bindValue() - 値渡し
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);

// ❌ 避ける: bindParam() - 参照渡し（変数が変わると問題）
$stmt->bindParam(':limit', $per_page, PDO::PARAM_INT);
```

3. **LIMIT/OFFSETに型指定が必要**
```php
// PDO::PARAM_INT を指定しないとエラーになる場合がある
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
```

#### 🎓 学習ポイント

**ページングの仕組み**

ページングは大量のデータを小分けにして表示する技術です。

```
全100件のデータがある場合（1ページ10件）:

ページ1: LIMIT 10 OFFSET 0  → 1〜10件目
ページ2: LIMIT 10 OFFSET 10 → 11〜20件目
ページ3: LIMIT 10 OFFSET 20 → 21〜30件目
...
ページ10: LIMIT 10 OFFSET 90 → 91〜100件目
```

**よくある間違い**

```php
// ❌ 間違い: ページ1で10件スキップしてしまう
$offset = $current_page * $per_page;

// ✅ 正解: ページ1で0件スキップ
$offset = ($current_page - 1) * $per_page;
```

---

### 問題5-2：総ページ数の計算

総ページ数を計算して、「全○件 / ○ページ中○ページ目」と表示します。

#### ✅ 解答例

**pagination_with_count.php**

```php
<?php
// データベース接続（省略 - 問題5-1と同じ）
require_once 'config.php';

// ページ番号を取得
$current_page = $_GET['page'] ?? 1;
$current_page = (int)$current_page;

// 不正な値のチェック
if ($current_page < 1) {
    $current_page = 1;
}

// 1ページあたりの件数
$per_page = 10;

// 総件数を取得
$stmt = $pdo->prepare("SELECT COUNT(*) FROM articles");
$stmt->execute();
$total_count = (int)$stmt->fetchColumn();

// 総ページ数を計算
$total_pages = ceil($total_count / $per_page);

// 現在のページが総ページ数を超えていないかチェック
if ($current_page > $total_pages && $total_pages > 0) {
    // 最終ページにリダイレクト
    header("Location: ?page=$total_pages");
    exit;
}

// OFFSETを計算
$offset = ($current_page - 1) * $per_page;

// データを取得
$stmt = $pdo->prepare("SELECT * FROM articles ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>記事一覧 - ページング</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .pagination-info {
            background: #f0f0f0;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .pagination-nav {
            margin-top: 20px;
            text-align: center;
        }
        .pagination-nav a,
        .pagination-nav span {
            display: inline-block;
            padding: 8px 12px;
            margin: 0 5px;
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        .pagination-nav a {
            background: #fff;
            color: #333;
        }
        .pagination-nav a:hover {
            background: #007bff;
            color: #fff;
            border-color: #007bff;
        }
        .pagination-nav span.disabled {
            background: #f0f0f0;
            color: #999;
        }
        .article {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .article h2 {
            margin-top: 0;
        }
        .article-meta {
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <h1>記事一覧</h1>

    <!-- ページング情報 -->
    <div class="pagination-info">
        全<?php echo number_format($total_count); ?>件 /
        <?php echo number_format($total_pages); ?>ページ中
        <?php echo number_format($current_page); ?>ページ目
    </div>

    <?php if (empty($articles)): ?>
        <p>記事が見つかりませんでした。</p>
    <?php else: ?>
        <?php foreach ($articles as $article): ?>
            <div class="article">
                <h2><?php echo htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                <p><?php echo htmlspecialchars($article['content'], ENT_QUOTES, 'UTF-8'); ?></p>
                <div class="article-meta">
                    カテゴリ: <?php echo htmlspecialchars($article['category'], ENT_QUOTES, 'UTF-8'); ?> |
                    投稿日: <?php echo htmlspecialchars($article['created_at'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- ページネーション -->
    <div class="pagination-nav">
        <!-- 前へボタン -->
        <?php if ($current_page > 1): ?>
            <a href="?page=<?php echo $current_page - 1; ?>">« 前へ</a>
        <?php else: ?>
            <span class="disabled">« 前へ</span>
        <?php endif; ?>

        <!-- 次へボタン -->
        <?php if ($current_page < $total_pages): ?>
            <a href="?page=<?php echo $current_page + 1; ?>">次へ »</a>
        <?php else: ?>
            <span class="disabled">次へ »</span>
        <?php endif; ?>
    </div>
</body>
</html>
```

#### ✅ セキュリティポイント

1. **総件数の型キャスト**: `(int)$stmt->fetchColumn()` で整数化
2. **ページ数のバリデーション**: 総ページ数を超える場合は最終ページにリダイレクト
3. **number_format()**: 数値のフォーマット（XSS対策は不要だが、見やすさのため使用）

#### 💡 コードのポイント

1. **総ページ数の計算**
```php
$total_pages = ceil($total_count / $per_page);

// 例：
// 100件 ÷ 10件/ページ = 10ページ
// 105件 ÷ 10件/ページ = 10.5 → ceil() で11ページ
// 95件 ÷ 10件/ページ = 9.5 → ceil() で10ページ
```

2. **ceil() 関数の重要性**
```php
// ceil() は切り上げ関数
ceil(10.1) // 11
ceil(10.5) // 11
ceil(10.9) // 11
ceil(10.0) // 10

// ❌ floor() や round() は使わない
floor(10.9) // 10 - 最後の0.9ページ分が表示されない！
round(10.4) // 10 - 同様に表示されないデータが出る
```

3. **前へ・次へボタンの条件分岐**
```php
// 前へボタン: ページ1では無効化
<?php if ($current_page > 1): ?>
    <a href="?page=<?php echo $current_page - 1; ?>">« 前へ</a>
<?php else: ?>
    <span class="disabled">« 前へ</span>
<?php endif; ?>

// 次へボタン: 最終ページでは無効化
<?php if ($current_page < $total_pages): ?>
    <a href="?page=<?php echo $current_page + 1; ?>">次へ »</a>
<?php else: ?>
    <span class="disabled">次へ »</span>
<?php endif; ?>
```

#### 🎓 学習ポイント

**総ページ数の計算例**

```
100件のデータ、1ページ10件の場合:
総ページ数 = ceil(100 / 10) = ceil(10) = 10ページ

105件のデータ、1ページ10件の場合:
総ページ数 = ceil(105 / 10) = ceil(10.5) = 11ページ
（最後のページには5件だけ表示される）

0件のデータの場合:
総ページ数 = ceil(0 / 10) = ceil(0) = 0ページ
（この場合の処理に注意！）
```

**エッジケースの処理**

```php
// データが0件の場合
if ($total_pages === 0) {
    // ページングを表示しない
    // または「データがありません」と表示
}

// 現在のページが総ページ数を超えている場合
if ($current_page > $total_pages && $total_pages > 0) {
    // 最終ページにリダイレクト
    header("Location: ?page=$total_pages");
    exit;
}
```

---

### 問題5-3：ページネーションリンクの表示

「前へ」「次へ」ボタンと、ページ番号のリンクを表示します。

#### ✅ 解答例

**pagination_with_links.php**

```php
<?php
// データベース接続（省略 - 問題5-1と同じ）
require_once 'config.php';

// ページ番号を取得
$current_page = $_GET['page'] ?? 1;
$current_page = (int)$current_page;

if ($current_page < 1) {
    $current_page = 1;
}

$per_page = 10;

// 総件数を取得
$stmt = $pdo->prepare("SELECT COUNT(*) FROM articles");
$stmt->execute();
$total_count = (int)$stmt->fetchColumn();

// 総ページ数を計算
$total_pages = ceil($total_count / $per_page);

if ($current_page > $total_pages && $total_pages > 0) {
    header("Location: ?page=$total_pages");
    exit;
}

// OFFSETを計算
$offset = ($current_page - 1) * $per_page;

// データを取得
$stmt = $pdo->prepare("SELECT * FROM articles ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>記事一覧 - ページネーション</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .pagination-info {
            background: #f0f0f0;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        .pagination a,
        .pagination span,
        .pagination strong {
            display: inline-block;
            padding: 8px 12px;
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 3px;
            min-width: 40px;
            text-align: center;
        }
        .pagination a {
            background: #fff;
            color: #333;
        }
        .pagination a:hover {
            background: #007bff;
            color: #fff;
            border-color: #007bff;
        }
        .pagination strong {
            background: #007bff;
            color: #fff;
            border-color: #007bff;
        }
        .pagination span.disabled {
            background: #f0f0f0;
            color: #999;
        }
        .article {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .article h2 {
            margin-top: 0;
            color: #333;
        }
        .article-meta {
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <h1>記事一覧</h1>

    <div class="pagination-info">
        全<?php echo number_format($total_count); ?>件 /
        <?php echo number_format($total_pages); ?>ページ中
        <?php echo number_format($current_page); ?>ページ目
    </div>

    <?php if (empty($articles)): ?>
        <p>記事が見つかりませんでした。</p>
    <?php else: ?>
        <?php foreach ($articles as $article): ?>
            <div class="article">
                <h2><?php echo htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                <p><?php echo htmlspecialchars($article['content'], ENT_QUOTES, 'UTF-8'); ?></p>
                <div class="article-meta">
                    カテゴリ: <?php echo htmlspecialchars($article['category'], ENT_QUOTES, 'UTF-8'); ?> |
                    投稿日: <?php echo htmlspecialchars($article['created_at'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- ページネーション -->
    <div class="pagination">
        <!-- 前へボタン -->
        <?php if ($current_page > 1): ?>
            <a href="?page=<?php echo $current_page - 1; ?>">« 前へ</a>
        <?php else: ?>
            <span class="disabled">« 前へ</span>
        <?php endif; ?>

        <!-- ページ番号リンク -->
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <?php if ($i === $current_page): ?>
                <strong><?php echo $i; ?></strong>
            <?php else: ?>
                <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <!-- 次へボタン -->
        <?php if ($current_page < $total_pages): ?>
            <a href="?page=<?php echo $current_page + 1; ?>">次へ »</a>
        <?php else: ?>
            <span class="disabled">次へ »</span>
        <?php endif; ?>
    </div>
</body>
</html>
```

#### ✅ セキュリティポイント

1. **ページ番号の出力**: `<?php echo $i; ?>` は数値なのでXSS の心配はないが、念のため検証済み
2. **XSS対策**: すべてのユーザー入力（記事データ）に `htmlspecialchars()` を使用
3. **現在のページの識別**: `===` で厳密な比較を使用

#### 💡 コードのポイント

1. **ページ番号リンクの生成**
```php
<?php for ($i = 1; $i <= $total_pages; $i++): ?>
    <?php if ($i === $current_page): ?>
        <!-- 現在のページは強調表示（リンクなし） -->
        <strong><?php echo $i; ?></strong>
    <?php else: ?>
        <!-- 他のページはリンク -->
        <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
    <?php endif; ?>
<?php endfor; ?>
```

2. **Flexboxでのレイアウト**
```css
.pagination {
    display: flex;
    justify-content: center;  /* 中央揃え */
    align-items: center;       /* 垂直方向の中央揃え */
    gap: 5px;                  /* 要素間の間隔 */
    flex-wrap: wrap;           /* ページ数が多い場合は折り返し */
}
```

#### 🎓 学習ポイント

**ページネーションのUI/UXデザイン**

良いページネーションの条件：
1. **現在のページが明確**: 強調表示やスタイルで区別
2. **前へ・次へが分かりやすい**: 記号（«, »）で視覚的に表現
3. **無効な操作を防ぐ**: 最初/最後のページでボタンを無効化
4. **クリックしやすい**: 十分な大きさとパディング
5. **モバイル対応**: flex-wrap で折り返し対応

**よくある改善ポイント**

```php
// ❌ 悪い例: ページ数が100ページあると100個のリンクが表示される
<?php for ($i = 1; $i <= $total_pages; $i++): ?>
    <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
<?php endfor; ?>

// ✅ 良い例: ページ数が多い場合は省略表示（問題5-4で実装）
// 例: 1 ... 8 9 10 11 12 ... 100
```

---

## 応用編

### 問題5-4：省略表示（...）のページネーション

ページ数が多い場合に、省略表示（...）を使ったページネーションを実装します。

#### ✅ 解答例

**pagination_ellipsis.php**

```php
<?php
// データベース接続
require_once 'config.php';

// ページ番号を取得
$current_page = $_GET['page'] ?? 1;
$current_page = (int)$current_page;

if ($current_page < 1) {
    $current_page = 1;
}

$per_page = 10;

// 総件数を取得
$stmt = $pdo->prepare("SELECT COUNT(*) FROM articles");
$stmt->execute();
$total_count = (int)$stmt->fetchColumn();

// 総ページ数を計算
$total_pages = ceil($total_count / $per_page);

if ($current_page > $total_pages && $total_pages > 0) {
    header("Location: ?page=$total_pages");
    exit;
}

// OFFSETを計算
$offset = ($current_page - 1) * $per_page;

// データを取得
$stmt = $pdo->prepare("SELECT * FROM articles ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>記事一覧 - 省略表示ページネーション</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .pagination-info {
            background: #f0f0f0;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        .pagination a,
        .pagination span,
        .pagination strong {
            display: inline-block;
            padding: 8px 12px;
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 3px;
            min-width: 40px;
            text-align: center;
        }
        .pagination a {
            background: #fff;
            color: #333;
        }
        .pagination a:hover {
            background: #007bff;
            color: #fff;
            border-color: #007bff;
        }
        .pagination strong {
            background: #007bff;
            color: #fff;
            border-color: #007bff;
        }
        .pagination span.disabled {
            background: #f0f0f0;
            color: #999;
        }
        .pagination span.ellipsis {
            border: none;
            background: transparent;
        }
        .article {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .article h2 {
            margin-top: 0;
        }
        .article-meta {
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <h1>記事一覧</h1>

    <div class="pagination-info">
        全<?php echo number_format($total_count); ?>件 /
        <?php echo number_format($total_pages); ?>ページ中
        <?php echo number_format($current_page); ?>ページ目
    </div>

    <?php if (empty($articles)): ?>
        <p>記事が見つかりませんでした。</p>
    <?php else: ?>
        <?php foreach ($articles as $article): ?>
            <div class="article">
                <h2><?php echo htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                <p><?php echo htmlspecialchars($article['content'], ENT_QUOTES, 'UTF-8'); ?></p>
                <div class="article-meta">
                    カテゴリ: <?php echo htmlspecialchars($article['category'], ENT_QUOTES, 'UTF-8'); ?> |
                    投稿日: <?php echo htmlspecialchars($article['created_at'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- 省略表示ページネーション -->
    <div class="pagination">
        <!-- 前へボタン -->
        <?php if ($current_page > 1): ?>
            <a href="?page=<?php echo $current_page - 1; ?>">« 前へ</a>
        <?php else: ?>
            <span class="disabled">« 前へ</span>
        <?php endif; ?>

        <?php
        // 表示するページ番号の範囲を計算
        $range = 2; // 現在のページの前後何ページ表示するか
        $start = max(1, $current_page - $range);
        $end = min($total_pages, $current_page + $range);

        // 最初のページ
        if ($start > 1) {
            echo '<a href="?page=1">1</a>';
            if ($start > 2) {
                echo '<span class="ellipsis">...</span>';
            }
        }

        // 範囲内のページ
        for ($i = $start; $i <= $end; $i++) {
            if ($i === $current_page) {
                echo "<strong>{$i}</strong>";
            } else {
                echo "<a href='?page={$i}'>{$i}</a>";
            }
        }

        // 最後のページ
        if ($end < $total_pages) {
            if ($end < $total_pages - 1) {
                echo '<span class="ellipsis">...</span>';
            }
            echo "<a href='?page={$total_pages}'>{$total_pages}</a>";
        }
        ?>

        <!-- 次へボタン -->
        <?php if ($current_page < $total_pages): ?>
            <a href="?page=<?php echo $current_page + 1; ?>">次へ »</a>
        <?php else: ?>
            <span class="disabled">次へ »</span>
        <?php endif; ?>
    </div>
</body>
</html>
```

#### ✅ セキュリティポイント

1. **数値の出力**: `$i` や `$total_pages` は計算済みの整数なので安全
2. **エスケープ**: ユーザー入力由来のデータは `htmlspecialchars()` で処理

#### 💡 コードのポイント

1. **省略表示のロジック**
```php
$range = 2; // 現在のページの前後2ページを表示
$start = max(1, $current_page - $range);
$end = min($total_pages, $current_page + $range);

// 例: 現在10ページ目、総20ページの場合
// $start = max(1, 10 - 2) = 8
// $end = min(20, 10 + 2) = 12
// 結果: 8 9 10 11 12 が表示される
```

2. **省略記号（...）の表示条件**
```php
// 最初のページとの間に隙間がある場合
if ($start > 1) {
    echo '<a href="?page=1">1</a>';
    if ($start > 2) {
        // ページ1と表示範囲の間に2ページ以上の隙間がある
        echo '<span class="ellipsis">...</span>';
    }
}
```

3. **表示パターンの例**
```
ページ1の場合（range=2）:
1 2 3 ... 20

ページ2の場合:
1 2 3 4 ... 20

ページ10の場合:
1 ... 8 9 10 11 12 ... 20

ページ19の場合:
1 ... 17 18 19 20

ページ20の場合:
1 ... 18 19 20
```

#### 🎓 学習ポイント

**max() と min() 関数の活用**

```php
// 範囲が配列の外に出ないように制限
$start = max(1, $current_page - $range);
// 例:
// ページ1の場合: max(1, 1 - 2) = max(1, -1) = 1
// ページ5の場合: max(1, 5 - 2) = max(1, 3) = 3

$end = min($total_pages, $current_page + $range);
// 例: 総20ページの場合
// ページ19の場合: min(20, 19 + 2) = min(20, 21) = 20
// ページ10の場合: min(20, 10 + 2) = min(20, 12) = 12
```

**rangeの値による表示の違い**

```php
// range = 1 の場合（狭い）
1 ... 9 10 11 ... 100

// range = 2 の場合（標準）
1 ... 8 9 10 11 12 ... 100

// range = 3 の場合（広い）
1 ... 7 8 9 10 11 12 13 ... 100
```

**パフォーマンスへの影響**

```php
// ❌ 悪い例: 10,000ページ全部のリンクを生成
for ($i = 1; $i <= 10000; $i++) {
    echo "<a href='?page={$i}'>{$i}</a>";
}
// → HTMLが巨大になり、ページの読み込みが遅い

// ✅ 良い例: 必要な部分だけ生成（省略表示）
// → 常に10個前後のリンクだけなので高速
```

---

### 問題5-5：1ページあたりの件数を変更可能にする

ユーザーが1ページあたりの表示件数を選択できるようにします。

#### ✅ 解答例

**pagination_configurable.php**

```php
<?php
// データベース接続
require_once 'config.php';

// 許可する表示件数の配列（ホワイトリスト）
$allowed_per_page = [10, 25, 50, 100];

// 1ページあたりの件数を取得
$per_page = $_GET['per_page'] ?? 10;
$per_page = (int)$per_page;

// 不正な値のチェック（ホワイトリスト方式）
if (!in_array($per_page, $allowed_per_page, true)) {
    $per_page = 10; // デフォルト値
}

// ページ番号を取得
$current_page = $_GET['page'] ?? 1;
$current_page = (int)$current_page;

if ($current_page < 1) {
    $current_page = 1;
}

// 総件数を取得
$stmt = $pdo->prepare("SELECT COUNT(*) FROM articles");
$stmt->execute();
$total_count = (int)$stmt->fetchColumn();

// 総ページ数を計算
$total_pages = ceil($total_count / $per_page);

if ($current_page > $total_pages && $total_pages > 0) {
    header("Location: ?page=$total_pages&per_page=$per_page");
    exit;
}

// OFFSETを計算
$offset = ($current_page - 1) * $per_page;

// データを取得
$stmt = $pdo->prepare("SELECT * FROM articles ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>記事一覧 - 表示件数変更可能</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .per-page-selector select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 3px;
            font-size: 14px;
        }
        .pagination-info {
            background: #f0f0f0;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        .pagination a,
        .pagination span,
        .pagination strong {
            display: inline-block;
            padding: 8px 12px;
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 3px;
            min-width: 40px;
            text-align: center;
        }
        .pagination a {
            background: #fff;
            color: #333;
        }
        .pagination a:hover {
            background: #007bff;
            color: #fff;
            border-color: #007bff;
        }
        .pagination strong {
            background: #007bff;
            color: #fff;
            border-color: #007bff;
        }
        .pagination span.disabled {
            background: #f0f0f0;
            color: #999;
        }
        .pagination span.ellipsis {
            border: none;
            background: transparent;
        }
        .article {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .article h2 {
            margin-top: 0;
        }
        .article-meta {
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <h1>記事一覧</h1>

    <!-- 表示件数選択 -->
    <div class="controls">
        <div class="per-page-selector">
            <label for="per_page">表示件数: </label>
            <select id="per_page" name="per_page" onchange="location.href='?page=1&per_page=' + this.value;">
                <?php foreach ($allowed_per_page as $option): ?>
                    <option value="<?php echo $option; ?>" <?php echo ($option === $per_page) ? 'selected' : ''; ?>>
                        <?php echo $option; ?>件表示
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="pagination-info">
            全<?php echo number_format($total_count); ?>件 /
            <?php echo number_format($total_pages); ?>ページ中
            <?php echo number_format($current_page); ?>ページ目
        </div>
    </div>

    <?php if (empty($articles)): ?>
        <p>記事が見つかりませんでした。</p>
    <?php else: ?>
        <?php foreach ($articles as $article): ?>
            <div class="article">
                <h2><?php echo htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                <p><?php echo htmlspecialchars($article['content'], ENT_QUOTES, 'UTF-8'); ?></p>
                <div class="article-meta">
                    カテゴリ: <?php echo htmlspecialchars($article['category'], ENT_QUOTES, 'UTF-8'); ?> |
                    投稿日: <?php echo htmlspecialchars($article['created_at'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- ページネーション -->
    <div class="pagination">
        <!-- 前へボタン -->
        <?php if ($current_page > 1): ?>
            <a href="?page=<?php echo $current_page - 1; ?>&per_page=<?php echo $per_page; ?>">« 前へ</a>
        <?php else: ?>
            <span class="disabled">« 前へ</span>
        <?php endif; ?>

        <?php
        // 省略表示のページネーション
        $range = 2;
        $start = max(1, $current_page - $range);
        $end = min($total_pages, $current_page + $range);

        if ($start > 1) {
            echo '<a href="?page=1&per_page=' . $per_page . '">1</a>';
            if ($start > 2) {
                echo '<span class="ellipsis">...</span>';
            }
        }

        for ($i = $start; $i <= $end; $i++) {
            if ($i === $current_page) {
                echo "<strong>{$i}</strong>";
            } else {
                echo "<a href='?page={$i}&per_page={$per_page}'>{$i}</a>";
            }
        }

        if ($end < $total_pages) {
            if ($end < $total_pages - 1) {
                echo '<span class="ellipsis">...</span>';
            }
            echo "<a href='?page={$total_pages}&per_page={$per_page}'>{$total_pages}</a>";
        }
        ?>

        <!-- 次へボタン -->
        <?php if ($current_page < $total_pages): ?>
            <a href="?page=<?php echo $current_page + 1; ?>&per_page=<?php echo $per_page; ?>">次へ »</a>
        <?php else: ?>
            <span class="disabled">次へ »</span>
        <?php endif; ?>
    </div>
</body>
</html>
```

#### ✅ セキュリティポイント

1. **ホワイトリスト方式**: `$allowed_per_page` で許可する値を明示的に指定
2. **in_array() の厳密比較**: 第3引数に `true` を指定して型も比較
3. **すべてのリンクにper_pageを含める**: URLパラメータを保持

#### 💡 コードのポイント

1. **ホワイトリスト方式によるバリデーション**
```php
// 許可する値を配列で定義
$allowed_per_page = [10, 25, 50, 100];

// ユーザー入力をチェック
if (!in_array($per_page, $allowed_per_page, true)) {
    $per_page = 10; // 不正な値ならデフォルトに戻す
}
```

2. **すべてのリンクにper_pageパラメータを追加**
```php
// ページネーションリンク
<a href="?page=<?php echo $i; ?>&per_page=<?php echo $per_page; ?>">

// 次へボタン
<a href="?page=<?php echo $current_page + 1; ?>&per_page=<?php echo $per_page; ?>">

// 表示件数変更時はページ1に戻る
<select onchange="location.href='?page=1&per_page=' + this.value;">
```

3. **selectタグでの選択状態の維持**
```php
<option value="<?php echo $option; ?>" <?php echo ($option === $per_page) ? 'selected' : ''; ?>>
    <?php echo $option; ?>件表示
</option>
```

#### 🎓 学習ポイント

**ホワイトリスト vs ブラックリスト**

```php
// ✅ ホワイトリスト方式（推奨）- 許可する値を明示
$allowed_per_page = [10, 25, 50, 100];
if (!in_array($per_page, $allowed_per_page, true)) {
    $per_page = 10;
}

// ❌ 範囲チェック方式（危険）- 予期しない値を許可してしまう
if ($per_page < 1 || $per_page > 1000) {
    $per_page = 10;
}
// → ユーザーが999を指定すると、データベースに負荷がかかる
```

**URLパラメータの保持**

ページング機能では、URLパラメータを適切に保持することが重要です。

```php
// 悪い例：per_pageが失われる
<a href="?page=2">次へ</a>
// → ページ2に移動すると、per_pageがデフォルトに戻ってしまう

// 良い例：すべてのパラメータを保持
<a href="?page=2&per_page=<?php echo $per_page; ?>">次へ</a>
// → ユーザーが選択した表示件数が維持される
```

---

### 問題5-6：検索結果のページング

検索機能とページング処理を組み合わせます。

#### ✅ 解答例

**pagination_search.php**

```php
<?php
// データベース接続
require_once 'config.php';

// 検索キーワードを取得
$keyword = $_GET['keyword'] ?? '';
$keyword = trim($keyword); // 前後の空白を削除

// ページ番号を取得
$current_page = $_GET['page'] ?? 1;
$current_page = (int)$current_page;

if ($current_page < 1) {
    $current_page = 1;
}

$per_page = 10;

// SQLクエリを構築
if (!empty($keyword)) {
    // 検索キーワードがある場合
    $sql = "SELECT * FROM articles WHERE title LIKE :keyword OR content LIKE :keyword ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
    $count_sql = "SELECT COUNT(*) FROM articles WHERE title LIKE :keyword OR content LIKE :keyword";

    // LIKE検索用のパターン（部分一致）
    $search_pattern = '%' . $keyword . '%';
} else {
    // 検索キーワードがない場合（全件表示）
    $sql = "SELECT * FROM articles ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
    $count_sql = "SELECT COUNT(*) FROM articles";
}

// 総件数を取得
$count_stmt = $pdo->prepare($count_sql);
if (!empty($keyword)) {
    $count_stmt->bindValue(':keyword', $search_pattern, PDO::PARAM_STR);
}
$count_stmt->execute();
$total_count = (int)$count_stmt->fetchColumn();

// 総ページ数を計算
$total_pages = ceil($total_count / $per_page);

if ($current_page > $total_pages && $total_pages > 0) {
    $redirect_url = "?page=$total_pages";
    if (!empty($keyword)) {
        $redirect_url .= "&keyword=" . urlencode($keyword);
    }
    header("Location: $redirect_url");
    exit;
}

// OFFSETを計算
$offset = ($current_page - 1) * $per_page;

// データを取得
$stmt = $pdo->prepare($sql);
if (!empty($keyword)) {
    $stmt->bindValue(':keyword', $search_pattern, PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>記事検索 - ページング</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .search-form {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .search-form input[type="text"] {
            width: 70%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 3px;
            font-size: 14px;
        }
        .search-form button {
            padding: 10px 20px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 14px;
        }
        .search-form button:hover {
            background: #0056b3;
        }
        .search-info {
            background: #e7f3ff;
            padding: 10px 15px;
            border-left: 4px solid #007bff;
            margin-bottom: 20px;
        }
        .pagination-info {
            background: #f0f0f0;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        .pagination a,
        .pagination span,
        .pagination strong {
            display: inline-block;
            padding: 8px 12px;
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 3px;
            min-width: 40px;
            text-align: center;
        }
        .pagination a {
            background: #fff;
            color: #333;
        }
        .pagination a:hover {
            background: #007bff;
            color: #fff;
            border-color: #007bff;
        }
        .pagination strong {
            background: #007bff;
            color: #fff;
            border-color: #007bff;
        }
        .pagination span.disabled {
            background: #f0f0f0;
            color: #999;
        }
        .pagination span.ellipsis {
            border: none;
            background: transparent;
        }
        .article {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .article h2 {
            margin-top: 0;
        }
        .article-meta {
            color: #666;
            font-size: 0.9em;
        }
        .no-results {
            text-align: center;
            padding: 40px 20px;
            background: #f8f9fa;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>記事検索</h1>

    <!-- 検索フォーム -->
    <div class="search-form">
        <form method="GET" action="">
            <input type="text" name="keyword" value="<?php echo htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8'); ?>" placeholder="キーワードを入力...">
            <button type="submit">検索</button>
        </form>
    </div>

    <?php if (!empty($keyword)): ?>
        <div class="search-info">
            「<?php echo htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8'); ?>」の検索結果: <?php echo number_format($total_count); ?>件
        </div>
    <?php endif; ?>

    <div class="pagination-info">
        全<?php echo number_format($total_count); ?>件 /
        <?php echo number_format($total_pages); ?>ページ中
        <?php echo number_format($current_page); ?>ページ目
    </div>

    <?php if (empty($articles)): ?>
        <div class="no-results">
            <p>記事が見つかりませんでした。</p>
            <?php if (!empty($keyword)): ?>
                <p><a href="?">すべての記事を表示</a></p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <?php foreach ($articles as $article): ?>
            <div class="article">
                <h2><?php echo htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                <p><?php echo htmlspecialchars($article['content'], ENT_QUOTES, 'UTF-8'); ?></p>
                <div class="article-meta">
                    カテゴリ: <?php echo htmlspecialchars($article['category'], ENT_QUOTES, 'UTF-8'); ?> |
                    投稿日: <?php echo htmlspecialchars($article['created_at'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- ページネーション -->
    <?php if ($total_pages > 0): ?>
        <div class="pagination">
            <!-- 前へボタン -->
            <?php if ($current_page > 1): ?>
                <a href="?page=<?php echo $current_page - 1; ?>&keyword=<?php echo urlencode($keyword); ?>">« 前へ</a>
            <?php else: ?>
                <span class="disabled">« 前へ</span>
            <?php endif; ?>

            <?php
            // 省略表示のページネーション
            $range = 2;
            $start = max(1, $current_page - $range);
            $end = min($total_pages, $current_page + $range);

            if ($start > 1) {
                echo '<a href="?page=1&keyword=' . urlencode($keyword) . '">1</a>';
                if ($start > 2) {
                    echo '<span class="ellipsis">...</span>';
                }
            }

            for ($i = $start; $i <= $end; $i++) {
                if ($i === $current_page) {
                    echo "<strong>{$i}</strong>";
                } else {
                    echo "<a href='?page={$i}&keyword=" . urlencode($keyword) . "'>{$i}</a>";
                }
            }

            if ($end < $total_pages) {
                if ($end < $total_pages - 1) {
                    echo '<span class="ellipsis">...</span>';
                }
                echo "<a href='?page={$total_pages}&keyword=" . urlencode($keyword) . "'>{$total_pages}</a>";
            }
            ?>

            <!-- 次へボタン -->
            <?php if ($current_page < $total_pages): ?>
                <a href="?page=<?php echo $current_page + 1; ?>&keyword=<?php echo urlencode($keyword); ?>">次へ »</a>
            <?php else: ?>
                <span class="disabled">次へ »</span>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</body>
</html>
```

#### ✅ セキュリティポイント

1. **プリペアドステートメント**: LIKE検索でもプレースホルダーを使用
2. **urlencode()**: URLパラメータには必ず `urlencode()` を使用
3. **htmlspecialchars()**: HTML出力には `htmlspecialchars()` を使用
4. **trim()**: 検索キーワードの前後の空白を削除

#### 💡 コードのポイント

1. **LIKE検索のパターン**
```php
// ユーザー入力
$keyword = $_GET['keyword'] ?? '';

// LIKE検索用のパターン（部分一致）
$search_pattern = '%' . $keyword . '%';

// プリペアドステートメントで安全に検索
$stmt = $pdo->prepare("SELECT * FROM articles WHERE title LIKE :keyword OR content LIKE :keyword");
$stmt->bindValue(':keyword', $search_pattern, PDO::PARAM_STR);
```

2. **URLエンコードの使い分け**
```php
// URLパラメータ: urlencode()
<a href="?page=1&keyword=<?php echo urlencode($keyword); ?>">

// HTML表示: htmlspecialchars()
<p>検索キーワード: <?php echo htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8'); ?></p>
```

3. **検索結果が0件の場合の処理**
```php
<?php if (empty($articles)): ?>
    <div class="no-results">
        <p>記事が見つかりませんでした。</p>
        <?php if (!empty($keyword)): ?>
            <p><a href="?">すべての記事を表示</a></p>
        <?php endif; ?>
    </div>
<?php else: ?>
    <!-- 記事一覧を表示 -->
<?php endif; ?>
```

#### 🎓 学習ポイント

**LIKE検索とSQLインジェクション**

```php
// ❌ 危険: 直接SQL文に埋め込む
$sql = "SELECT * FROM articles WHERE title LIKE '%$keyword%'";
// → SQLインジェクションの脆弱性！

// ✅ 安全: プリペアドステートメントを使う
$search_pattern = '%' . $keyword . '%';
$stmt = $pdo->prepare("SELECT * FROM articles WHERE title LIKE :keyword");
$stmt->bindValue(':keyword', $search_pattern, PDO::PARAM_STR);
```

**LIKE検索のパターン**

```php
// 部分一致（前方・後方一致）
$search_pattern = '%' . $keyword . '%';
// 例: $keyword = 'PHP' → '%PHP%' → 「PHP入門」「はじめてのPHP」「PHPとMySQL」すべてマッチ

// 前方一致
$search_pattern = $keyword . '%';
// 例: $keyword = 'PHP' → 'PHP%' → 「PHP入門」「PHPとMySQL」はマッチ、「はじめてのPHP」はマッチしない

// 後方一致
$search_pattern = '%' . $keyword;
// 例: $keyword = 'PHP' → '%PHP' → 「はじめてのPHP」はマッチ、「PHP入門」はマッチしない
```

**パフォーマンスの注意点**

```sql
-- ❌ 遅い: 前方ワイルドカード付きLIKE検索はインデックスが使えない
SELECT * FROM articles WHERE title LIKE '%PHP%';

-- ✅ 速い: 前方一致検索はインデックスが使える
SELECT * FROM articles WHERE title LIKE 'PHP%';

-- ✅ もっと速い: FULLTEXT インデックスを使う（日本語対応に注意）
CREATE FULLTEXT INDEX idx_fulltext ON articles(title, content);
SELECT * FROM articles WHERE MATCH(title, content) AGAINST('PHP' IN NATURAL LANGUAGE MODE);
```

---

## セキュリティチャレンジ

### 問題5-7：カテゴリフィルタ付きページング

カテゴリフィルタとページングを組み合わせます。

#### ✅ 解答例（重要部分のみ）

```php
<?php
require_once 'config.php';

// カテゴリを取得（ホワイトリスト方式）
$allowed_categories = ['テクノロジー', 'ライフスタイル', 'ビジネス', ''];
$category = $_GET['category'] ?? '';

// セキュリティ: ホワイトリストでチェック
if (!in_array($category, $allowed_categories, true)) {
    $category = '';
}

// ページ番号を取得
$current_page = $_GET['page'] ?? 1;
$current_page = (int)$current_page;
if ($current_page < 1) {
    $current_page = 1;
}

$per_page = 10;

// SQLクエリを構築
if (!empty($category)) {
    $sql = "SELECT * FROM articles WHERE category = :category ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
    $count_sql = "SELECT COUNT(*) FROM articles WHERE category = :category";
} else {
    $sql = "SELECT * FROM articles ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
    $count_sql = "SELECT COUNT(*) FROM articles";
}

// 総件数を取得
$count_stmt = $pdo->prepare($count_sql);
if (!empty($category)) {
    $count_stmt->bindValue(':category', $category, PDO::PARAM_STR);
}
$count_stmt->execute();
$total_count = (int)$count_stmt->fetchColumn();

// 総ページ数を計算
$total_pages = ceil($total_count / $per_page);
$offset = ($current_page - 1) * $per_page;

// データを取得
$stmt = $pdo->prepare($sql);
if (!empty($category)) {
    $stmt->bindValue(':category', $category, PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- カテゴリフィルタUI -->
<div class="category-filter">
    <a href="?" class="<?php echo empty($category) ? 'active' : ''; ?>">すべて</a>
    <a href="?category=<?php echo urlencode('テクノロジー'); ?>"
       class="<?php echo $category === 'テクノロジー' ? 'active' : ''; ?>">テクノロジー</a>
    <a href="?category=<?php echo urlencode('ライフスタイル'); ?>"
       class="<?php echo $category === 'ライフスタイル' ? 'active' : ''; ?>">ライフスタイル</a>
    <a href="?category=<?php echo urlencode('ビジネス'); ?>"
       class="<?php echo $category === 'ビジネス' ? 'active' : ''; ?>">ビジネス</a>
</div>

<!-- ページネーションリンク（カテゴリを保持） -->
<a href="?page=<?php echo $i; ?>&category=<?php echo urlencode($category); ?>">
    <?php echo $i; ?>
</a>
```

#### ✅ セキュリティポイント

1. **ホワイトリスト方式**: 許可するカテゴリを配列で定義
2. **in_array() の厳密比較**: 型も含めて比較
3. **urlencode()**: URLパラメータのエンコード
4. **プリペアドステートメント**: SQLインジェクション対策

#### 💡 学習ポイント

**ホワイトリスト方式の重要性**

```php
// ✅ ホワイトリスト方式（推奨）
$allowed_categories = ['テクノロジー', 'ライフスタイル', 'ビジネス', ''];
if (!in_array($category, $allowed_categories, true)) {
    $category = '';
}

// ❌ バリデーションなし（危険）
$category = $_GET['category'] ?? '';
// → SQLインジェクションやデータベースエラーの可能性
```

---

### 問題5-8：ページネーション関数の作成

再利用可能なページネーション関数を作成します。

#### ✅ 解答例

**functions.php**

```php
<?php
/**
 * ページネーションHTMLを生成
 *
 * @param int $current_page 現在のページ
 * @param int $total_pages 総ページ数
 * @param array $params 追加のURLパラメータ（例: ['keyword' => 'PHP', 'category' => 'テクノロジー']）
 * @param int $range 現在のページの前後何ページ表示するか（デフォルト: 2）
 * @return string ページネーションのHTML
 */
function generatePagination($current_page, $total_pages, $params = [], $range = 2) {
    // バリデーション
    $current_page = (int)$current_page;
    $total_pages = (int)$total_pages;
    $range = (int)$range;

    if ($current_page < 1) {
        $current_page = 1;
    }

    if ($total_pages < 1) {
        return ''; // ページがない場合は何も表示しない
    }

    if ($current_page > $total_pages) {
        $current_page = $total_pages;
    }

    // URLパラメータを構築（セキュリティ: http_build_query を使う）
    $query_params = array_filter($params, function($value) {
        return $value !== '' && $value !== null;
    });

    $query_string = http_build_query($query_params);
    $separator = empty($query_string) ? '' : '&';

    $html = '<div class="pagination">';

    // 前へボタン
    if ($current_page > 1) {
        $prev_url = '?page=' . ($current_page - 1) . $separator . $query_string;
        $html .= '<a href="' . htmlspecialchars($prev_url, ENT_QUOTES, 'UTF-8') . '">« 前へ</a>';
    } else {
        $html .= '<span class="disabled">« 前へ</span>';
    }

    // ページ番号（省略表示）
    $start = max(1, $current_page - $range);
    $end = min($total_pages, $current_page + $range);

    // 最初のページ
    if ($start > 1) {
        $first_url = '?page=1' . $separator . $query_string;
        $html .= '<a href="' . htmlspecialchars($first_url, ENT_QUOTES, 'UTF-8') . '">1</a>';
        if ($start > 2) {
            $html .= '<span class="ellipsis">...</span>';
        }
    }

    // 範囲内のページ
    for ($i = $start; $i <= $end; $i++) {
        if ($i === $current_page) {
            $html .= "<strong>{$i}</strong>";
        } else {
            $page_url = "?page={$i}" . $separator . $query_string;
            $html .= '<a href="' . htmlspecialchars($page_url, ENT_QUOTES, 'UTF-8') . '">' . $i . '</a>';
        }
    }

    // 最後のページ
    if ($end < $total_pages) {
        if ($end < $total_pages - 1) {
            $html .= '<span class="ellipsis">...</span>';
        }
        $last_url = "?page={$total_pages}" . $separator . $query_string;
        $html .= '<a href="' . htmlspecialchars($last_url, ENT_QUOTES, 'UTF-8') . '">' . $total_pages . '</a>';
    }

    // 次へボタン
    if ($current_page < $total_pages) {
        $next_url = '?page=' . ($current_page + 1) . $separator . $query_string;
        $html .= '<a href="' . htmlspecialchars($next_url, ENT_QUOTES, 'UTF-8') . '">次へ »</a>';
    } else {
        $html .= '<span class="disabled">次へ »</span>';
    }

    $html .= '</div>';

    return $html;
}

// 使用例
echo generatePagination($current_page, $total_pages, [
    'keyword' => $keyword,
    'category' => $category,
    'per_page' => $per_page
]);
```

#### ✅ セキュリティポイント

1. **http_build_query()**: URLパラメータの安全な構築
2. **htmlspecialchars()**: URL出力時のXSS対策
3. **array_filter()**: 空のパラメータを除外
4. **型キャスト**: すべての数値パラメータを整数化

#### 💡 学習ポイント

**http_build_query() の利点**

```php
// ✅ 安全: http_build_query() を使う
$params = ['keyword' => 'PHP & MySQL', 'category' => 'テクノロジー'];
$query_string = http_build_query($params);
// 結果: keyword=PHP+%26+MySQL&category=%E3%83%86%E3%82%AF%E3%83%8E%E3%83%AD%E3%82%B8%E3%83%BC

// ❌ 危険: 手動で構築
$query_string = "keyword={$keyword}&category={$category}";
// → URLエンコードされず、特殊文字で問題が発生
```

---

## 総合チャレンジ

### 問題5-9：完全な記事検索・一覧システム

すべての機能を統合した完全な記事検索・一覧システムを作成します。

#### ✅ 解答例（統合版）

この問題は、これまでのすべての技術を組み合わせた総合的な実装です。

**index.php**（メインファイル）

```php
<?php
require_once 'config.php';
require_once 'functions.php'; // generatePagination() 関数

// パラメータを取得（すべてホワイトリスト/バリデーション）
$keyword = $_GET['keyword'] ?? '';
$keyword = trim($keyword);

$allowed_categories = ['テクノロジー', 'ライフスタイル', 'ビジネス', ''];
$category = $_GET['category'] ?? '';
if (!in_array($category, $allowed_categories, true)) {
    $category = '';
}

$allowed_per_page = [10, 25, 50, 100];
$per_page = (int)($_GET['per_page'] ?? 10);
if (!in_array($per_page, $allowed_per_page, true)) {
    $per_page = 10;
}

$allowed_sort = ['new', 'old'];
$sort = $_GET['sort'] ?? 'new';
if (!in_array($sort, $allowed_sort, true)) {
    $sort = 'new';
}

$current_page = (int)($_GET['page'] ?? 1);
if ($current_page < 1) {
    $current_page = 1;
}

// SQLクエリを動的に構築
$where_conditions = [];
$bind_params = [];

if (!empty($keyword)) {
    $where_conditions[] = "(title LIKE :keyword OR content LIKE :keyword)";
    $bind_params[':keyword'] = '%' . $keyword . '%';
}

if (!empty($category)) {
    $where_conditions[] = "category = :category";
    $bind_params[':category'] = $category;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
$order_clause = $sort === 'new' ? 'ORDER BY created_at DESC' : 'ORDER BY created_at ASC';

// 総件数を取得
$count_sql = "SELECT COUNT(*) FROM articles {$where_clause}";
$count_stmt = $pdo->prepare($count_sql);
foreach ($bind_params as $key => $value) {
    $count_stmt->bindValue($key, $value, PDO::PARAM_STR);
}
$count_stmt->execute();
$total_count = (int)$count_stmt->fetchColumn();

// 総ページ数を計算
$total_pages = ceil($total_count / $per_page);
if ($current_page > $total_pages && $total_pages > 0) {
    $current_page = $total_pages;
}

$offset = ($current_page - 1) * $per_page;

// データを取得
$sql = "SELECT * FROM articles {$where_clause} {$order_clause} LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
foreach ($bind_params as $key => $value) {
    $stmt->bindValue($key, $value, PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>記事検索・一覧システム</title>
</head>
<body>
    <h1>記事検索・一覧</h1>

    <!-- 検索フォーム -->
    <form method="GET">
        <input type="text" name="keyword" value="<?php echo htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8'); ?>" placeholder="キーワード検索">

        <select name="category">
            <option value="">すべてのカテゴリ</option>
            <?php foreach (['テクノロジー', 'ライフスタイル', 'ビジネス'] as $cat): ?>
                <option value="<?php echo htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?>"
                        <?php echo $category === $cat ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="per_page">
            <?php foreach ($allowed_per_page as $option): ?>
                <option value="<?php echo $option; ?>" <?php echo $per_page === $option ? 'selected' : ''; ?>>
                    <?php echo $option; ?>件表示
                </option>
            <?php endforeach; ?>
        </select>

        <select name="sort">
            <option value="new" <?php echo $sort === 'new' ? 'selected' : ''; ?>>新着順</option>
            <option value="old" <?php echo $sort === 'old' ? 'selected' : ''; ?>>古い順</option>
        </select>

        <button type="submit">検索</button>
        <a href="?">クリア</a>
    </form>

    <p>全<?php echo number_format($total_count); ?>件の記事</p>

    <!-- 記事一覧 -->
    <?php if (empty($articles)): ?>
        <p>記事が見つかりませんでした。</p>
    <?php else: ?>
        <?php foreach ($articles as $article): ?>
            <article>
                <h2><?php echo htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                <p><?php echo htmlspecialchars($article['content'], ENT_QUOTES, 'UTF-8'); ?></p>
                <footer>
                    <?php echo htmlspecialchars($article['category'], ENT_QUOTES, 'UTF-8'); ?> |
                    <?php echo htmlspecialchars($article['created_at'], ENT_QUOTES, 'UTF-8'); ?>
                </footer>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- ページネーション（関数を使用） -->
    <?php
    echo generatePagination($current_page, $total_pages, [
        'keyword' => $keyword,
        'category' => $category,
        'per_page' => $per_page,
        'sort' => $sort
    ]);
    ?>
</body>
</html>
```

#### ✅ セキュリティポイント

1. **すべてのパラメータをバリデーション**: ホワイトリスト方式
2. **プリペアドステートメント**: 動的なWHERE句でも安全
3. **XSS対策**: すべての出力に `htmlspecialchars()`
4. **関数の再利用**: `generatePagination()` で安全なHTML生成

#### 💡 学習ポイント

**動的WHERE句の安全な構築**

```php
// 条件を配列で管理
$where_conditions = [];
$bind_params = [];

if (!empty($keyword)) {
    $where_conditions[] = "(title LIKE :keyword OR content LIKE :keyword)";
    $bind_params[':keyword'] = '%' . $keyword . '%';
}

if (!empty($category)) {
    $where_conditions[] = "category = :category";
    $bind_params[':category'] = $category;
}

// WHERE句を構築
$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// プリペアドステートメントでバインド
$stmt = $pdo->prepare("SELECT * FROM articles {$where_clause}");
foreach ($bind_params as $key => $value) {
    $stmt->bindValue($key, $value, PDO::PARAM_STR);
}
```

---

### 問題5-10：無限スクロール

JavaScriptを使って無限スクロールを実装します（発展課題）。

#### ✅ 解答例

**api.php**（JSON APIエンドポイント）

```php
<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'config.php';

try {
    $page = (int)($_GET['page'] ?? 1);
    if ($page < 1) {
        $page = 1;
    }

    $per_page = 10;
    $offset = ($page - 1) * $per_page;

    $stmt = $pdo->prepare("SELECT * FROM articles ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // XSS対策: JSON内のHTMLエンティティをエスケープ
    $safe_articles = array_map(function($article) {
        return [
            'id' => (int)$article['id'],
            'title' => htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8'),
            'content' => htmlspecialchars($article['content'], ENT_QUOTES, 'UTF-8'),
            'category' => htmlspecialchars($article['category'], ENT_QUOTES, 'UTF-8'),
            'created_at' => htmlspecialchars($article['created_at'], ENT_QUOTES, 'UTF-8')
        ];
    }, $articles);

    echo json_encode([
        'success' => true,
        'articles' => $safe_articles,
        'page' => $page
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'データベースエラーが発生しました'
    ], JSON_UNESCAPED_UNICODE);
}
```

**index.html**（フロントエンド）

```html
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>無限スクロール - 記事一覧</title>
    <style>
        .article { border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; }
        .loading { text-align: center; padding: 20px; }
    </style>
</head>
<body>
    <h1>記事一覧（無限スクロール）</h1>
    <div id="articles"></div>
    <div id="loading" class="loading" style="display:none;">読み込み中...</div>

    <script>
    let currentPage = 1;
    let isLoading = false;

    // 記事を読み込む
    async function loadArticles() {
        if (isLoading) return;

        isLoading = true;
        document.getElementById('loading').style.display = 'block';

        try {
            const response = await fetch(`api.php?page=${currentPage}`);
            const data = await response.json();

            if (data.success && data.articles.length > 0) {
                const container = document.getElementById('articles');

                data.articles.forEach(article => {
                    const articleEl = document.createElement('div');
                    articleEl.className = 'article';
                    // データはサーバー側でエスケープ済み
                    articleEl.innerHTML = `
                        <h2>${article.title}</h2>
                        <p>${article.content}</p>
                        <footer>${article.category} | ${article.created_at}</footer>
                    `;
                    container.appendChild(articleEl);
                });

                currentPage++;
            }
        } catch (error) {
            console.error('エラー:', error);
        } finally {
            isLoading = false;
            document.getElementById('loading').style.display = 'none';
        }
    }

    // スクロールイベント
    window.addEventListener('scroll', () => {
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 100) {
            loadArticles();
        }
    });

    // 初回読み込み
    loadArticles();
    </script>
</body>
</html>
```

#### ✅ セキュリティポイント

1. **サーバー側でエスケープ**: JSON出力前に `htmlspecialchars()`
2. **エラーハンドリング**: 詳細なエラーメッセージを隠す
3. **JSONレスポンス**: `Content-Type: application/json` を明示
4. **XSS対策**: クライアント側でもエスケープ済みデータを使用

#### 💡 学習ポイント

**JSON APIのセキュリティ**

```php
// ✅ 安全: データをエスケープしてからJSON化
$safe_articles = array_map(function($article) {
    return [
        'title' => htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8'),
        'content' => htmlspecialchars($article['content'], ENT_QUOTES, 'UTF-8')
    ];
}, $articles);
echo json_encode($safe_articles);

// ❌ 危険: 生のデータをJSON化
echo json_encode($articles);
// → フロントエンドでXSS脆弱性が発生する可能性
```

---

## まとめ 🎉

**Lesson 05で学んだこと**

✅ **基礎編**
- LIMIT/OFFSETを使った基本的なページング
- 総ページ数の計算とページネーションリンク
- 前へ・次へボタンの実装

✅ **応用編**
- 省略表示（...）を使った効率的なページネーション
- 表示件数の変更機能
- 検索機能とページングの組み合わせ

✅ **セキュリティ**
- ホワイトリスト方式によるバリデーション
- プリペアドステートメントでのSQLインジェクション対策
- urlencode() と htmlspecialchars() の使い分け

✅ **総合**
- 再利用可能な関数の作成
- 複数の条件を組み合わせた検索システム
- 無限スクロールによるモダンなUI

**次のステップ**

ページング処理をマスターしたら、次は：
1. **Phase 6**: 実践プロジェクトで統合的なアプリケーションを構築
2. **Phase 7**: REST APIでさらに発展的な実装に挑戦

**Let's vibe and code! 🚀**

