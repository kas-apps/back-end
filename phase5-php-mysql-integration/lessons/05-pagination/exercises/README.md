# Lesson 05: ページング処理 - 演習問題 📄

大量データを効率的に表示するページング処理を実装して、ユーザーフレンドリーなアプリケーションを作ろう！

---

## 📝 準備

演習を始める前に、サンプルデータを準備しよう！

```sql
CREATE DATABASE IF NOT EXISTS phase5_practice CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE phase5_practice;

CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    category VARCHAR(50),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 100件のサンプルデータを挿入（テスト用）
INSERT INTO articles (title, content, category) VALUES
('記事タイトル 1', 'これは記事1の内容です。', 'テクノロジー'),
('記事タイトル 2', 'これは記事2の内容です。', 'ライフスタイル'),
('記事タイトル 3', 'これは記事3の内容です。', 'ビジネス');
-- ... （実際には100件分のデータを挿入）

-- 100件のデータを一気に挿入するSQL（コピペ用）
-- DELIMITER $$で区切って実行
DELIMITER $$
CREATE PROCEDURE insert_sample_articles()
BEGIN
    DECLARE i INT DEFAULT 1;
    WHILE i <= 100 DO
        INSERT INTO articles (title, content, category) VALUES
        (
            CONCAT('記事タイトル ', i),
            CONCAT('これは記事', i, 'の内容です。サンプルテキストを含んでいます。'),
            CASE (i % 3)
                WHEN 0 THEN 'テクノロジー'
                WHEN 1 THEN 'ライフスタイル'
                ELSE 'ビジネス'
            END
        );
        SET i = i + 1;
    END WHILE;
END$$
DELIMITER ;

CALL insert_sample_articles();
DROP PROCEDURE insert_sample_articles;
```

---

## 🌱 基礎編

### 問題5-1：基本的なページング処理

**課題**：

記事一覧に基本的なページング処理を実装してください（1ページ10件表示）。

**要件**：

- 1ページ10件表示
- URLパラメータ（`?page=1`）でページ番号を受け取る
- LIMITとOFFSETを使ってデータを取得
- プリペアドステートメントでSELECT
- ページ番号が不正な場合はページ1にリダイレクト

**ヒント**：

```php
// ページ番号を取得
$current_page = $_GET['page'] ?? 1;
$current_page = (int)$current_page;

// 不正な値のチェック
if ($current_page < 1) {
    $current_page = 1;
}

// 1ページあたりの件数
$per_page = 10;

// OFFSETを計算
$offset = ($current_page - 1) * $per_page;

// データを取得
$stmt = $pdo->prepare("SELECT * FROM articles ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

---

### 問題5-2：総ページ数の計算

**課題**：

総ページ数を計算して表示してください。

**要件**：

- `COUNT(*)`で総件数を取得
- 総ページ数を計算（`ceil(総件数 / 1ページあたりの件数)`）
- 「全○件 / ○ページ中○ページ目」と表示
- 次ページ・前ページへのリンクを表示（存在する場合のみ）

**ヒント**：

```php
// 総件数を取得
$stmt = $pdo->prepare("SELECT COUNT(*) FROM articles");
$stmt->execute();
$total_count = $stmt->fetchColumn();

// 総ページ数を計算
$total_pages = ceil($total_count / $per_page);

// 現在のページ数が総ページ数を超えていないかチェック
if ($current_page > $total_pages && $total_pages > 0) {
    $current_page = $total_pages;
}
```

---

### 問題5-3：ページネーションリンクの表示

**課題**：

「前へ」「次へ」ボタンと、ページ番号のリンクを表示してください。

**要件**：

- 「前へ」ボタン（1ページ目では無効化）
- ページ番号のリンク（現在のページは強調表示）
- 「次へ」ボタン（最終ページでは無効化）
- すべてのリンクでXSS対策（`htmlspecialchars()`）

**ヒント**：

```php
<div class="pagination">
    <!-- 前へボタン -->
    <?php if ($current_page > 1): ?>
        <a href="?page=<?php echo $current_page - 1; ?>">« 前へ</a>
    <?php else: ?>
        <span class="disabled">« 前へ</span>
    <?php endif; ?>

    <!-- ページ番号 -->
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
```

---

## 🚀 応用編

### 問題5-4：省略表示（...）のページネーション

**課題**：

ページ数が多い場合に、省略表示（...）を使ったページネーションを実装してください。

**表示例**：

- ページ1の場合：`1 2 3 4 5 ... 20`
- ページ10の場合：`1 ... 8 9 10 11 12 ... 20`
- ページ20の場合：`1 ... 16 17 18 19 20`

**要件**：

- 現在のページの前後2ページを表示
- 最初と最後のページは常に表示
- 省略部分は「...」で表示
- 100ページ以上でも効率的に表示

**ヒント**：

```php
// 表示するページ番号の範囲を計算
$range = 2; // 現在のページの前後何ページ表示するか
$start = max(1, $current_page - $range);
$end = min($total_pages, $current_page + $range);

// 最初のページ
if ($start > 1) {
    echo '<a href="?page=1">1</a>';
    if ($start > 2) {
        echo '<span>...</span>';
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
        echo '<span>...</span>';
    }
    echo "<a href='?page={$total_pages}'>{$total_pages}</a>";
}
```

---

### 問題5-5：1ページあたりの件数を変更可能にする

**課題**：

ユーザーが1ページあたりの表示件数を選択できるようにしてください。

**要件**：

- ドロップダウンで表示件数を選択（10件、25件、50件、100件）
- URLパラメータ（`?page=1&per_page=25`）で状態を保持
- 件数変更時はページ1に戻る
- セキュリティ対策（不正な値のチェック）

**ヒント**：

```php
// 許可する表示件数の配列
$allowed_per_page = [10, 25, 50, 100];

// 1ページあたりの件数を取得
$per_page = $_GET['per_page'] ?? 10;
$per_page = (int)$per_page;

// 不正な値のチェック
if (!in_array($per_page, $allowed_per_page)) {
    $per_page = 10;
}

// 表示件数選択ドロップダウン
<select name="per_page" onchange="location.href='?page=1&per_page=' + this.value;">
    <?php foreach ($allowed_per_page as $option): ?>
        <option value="<?php echo $option; ?>" <?php echo ($option === $per_page) ? 'selected' : ''; ?>>
            <?php echo $option; ?>件表示
        </option>
    <?php endforeach; ?>
</select>
```

---

### 問題5-6：検索結果のページング

**課題**：

検索機能とページング処理を組み合わせてください。

**要件**：

- キーワード検索（タイトルまたは内容で部分一致）
- 検索結果をページング表示
- URLパラメータで検索条件を保持（`?page=1&keyword=PHP`）
- 検索結果が0件の場合のメッセージ
- プリペアドステートメントでSELECT

**ヒント**：

```php
// 検索キーワードを取得
$keyword = $_GET['keyword'] ?? '';

// 検索条件を含むSQL
if (!empty($keyword)) {
    $sql = "SELECT * FROM articles WHERE title LIKE :keyword OR content LIKE :keyword ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
    $count_sql = "SELECT COUNT(*) FROM articles WHERE title LIKE :keyword OR content LIKE :keyword";

    $search_pattern = '%' . $keyword . '%';

    // データ取得
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':keyword', $search_pattern, PDO::PARAM_STR);
    $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    // 総件数取得
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->bindValue(':keyword', $search_pattern, PDO::PARAM_STR);
    $count_stmt->execute();
    $total_count = $count_stmt->fetchColumn();
}

// ページネーションリンクにキーワードを含める
<a href="?page=<?php echo $i; ?>&keyword=<?php echo urlencode($keyword); ?>"><?php echo $i; ?></a>
```

---

## 🛡️ セキュリティチャレンジ

### 問題5-7：カテゴリフィルタ付きページング

**課題**：

カテゴリフィルタとページングを組み合わせてください。

**要件**：

- カテゴリで絞り込み（テクノロジー、ライフスタイル、ビジネス）
- 絞り込み結果をページング表示
- URLパラメータで状態を保持（`?page=1&category=テクノロジー`）
- すべてのカテゴリを表示するリンク（フィルタなし）
- SQLインジェクション対策、XSS対策

---

### 問題5-8：ページネーション関数の作成

**課題**：

再利用可能なページネーション関数を作成してください。

**要件**：

- 関数名：`generatePagination()`
- 引数：現在のページ、総ページ数、追加のURLパラメータ
- 戻り値：ページネーションのHTMLコード
- すべてのセキュリティ対策を実装

**ヒント**：

```php
/**
 * ページネーションHTMLを生成
 *
 * @param int $current_page 現在のページ
 * @param int $total_pages 総ページ数
 * @param array $params 追加のURLパラメータ
 * @return string ページネーションのHTML
 */
function generatePagination($current_page, $total_pages, $params = []) {
    $html = '<div class="pagination">';

    // URLパラメータを構築
    $query_string = http_build_query($params);
    $separator = empty($query_string) ? '' : '&';

    // 前へボタン
    if ($current_page > 1) {
        $html .= '<a href="?page=' . ($current_page - 1) . $separator . $query_string . '">« 前へ</a>';
    }

    // ページ番号（省略表示）
    // ... 実装 ...

    // 次へボタン
    if ($current_page < $total_pages) {
        $html .= '<a href="?page=' . ($current_page + 1) . $separator . $query_string . '">次へ »</a>';
    }

    $html .= '</div>';

    return $html;
}

// 使用例
echo generatePagination($current_page, $total_pages, ['keyword' => $keyword, 'category' => $category]);
```

---

## 💪 総合チャレンジ

### 問題5-9：完全な記事検索・一覧システム

**課題**：

以下の機能を持つ、完全な記事検索・一覧システムを作成してください。

**機能一覧**：

1. **記事一覧表示**
   - ページング（省略表示あり）
   - 1ページあたりの件数を選択可能
   - 新着順、古い順の並び替え

2. **検索機能**
   - キーワード検索（タイトル・内容）
   - カテゴリフィルタ
   - 検索結果もページング表示

3. **URL状態管理**
   - すべての条件をURLパラメータで保持
   - ブラウザの戻る・進むボタンに対応
   - URLをコピーして共有可能

4. **ユーザーエクスペリエンス**
   - 検索結果が0件の場合のメッセージ
   - 現在の検索条件を表示
   - 検索条件をクリアするボタン

5. **パフォーマンス**
   - インデックスを活用（created_at、category）
   - 効率的なCOUNT(*)クエリ

**セキュリティ要件**：

- SQLインジェクション対策（プリペアドステートメント）
- XSS対策（`htmlspecialchars()`、`urlencode()`）
- バリデーション（ページ番号、表示件数、並び順）

**ファイル構成例**：

```text
/article-system/
├── config.php              # データベース接続
├── functions.php           # ページネーション関数
├── index.php              # 記事一覧（ページング付き）
└── styles.css             # スタイルシート
```

**データベースのインデックス**：

```sql
-- パフォーマンス向上のためのインデックス
CREATE INDEX idx_created_at ON articles(created_at);
CREATE INDEX idx_category ON articles(category);
CREATE FULLTEXT INDEX idx_fulltext ON articles(title, content);
```

---

### 問題5-10：無限スクロール（オプション）

**課題**：

JavaScriptを使って無限スクロールを実装してください（発展課題）。

**要件**：

- スクロールが底に達したら次のデータを自動読み込み
- AjaxでJSON形式のデータを取得
- PHPでJSONレスポンスを返すAPI作成
- 読み込み中のローディング表示

**ヒント（PHP側）**：

```php
<?php
// api.php - JSON APIエンドポイント
header('Content-Type: application/json; charset=utf-8');

$page = $_GET['page'] ?? 1;
$page = (int)$page;
$per_page = 10;
$offset = ($page - 1) * $per_page;

try {
    $stmt = $pdo->prepare("SELECT * FROM articles ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'articles' => $articles]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'データベースエラー']);
}
?>
```

**ヒント（JavaScript側）**：

```javascript
let currentPage = 1;
let loading = false;

window.addEventListener('scroll', function() {
    if (loading) return;

    // 底に達したかチェック
    if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 100) {
        loading = true;
        currentPage++;

        // データを読み込む
        fetch(`api.php?page=${currentPage}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // データを追加表示
                    data.articles.forEach(article => {
                        // DOM操作でarticleを追加
                    });
                }
                loading = false;
            });
    }
});
```

---

## 🤖 バイブコーディングのヒント

### AIへの良い指示例

```text
「記事一覧にページング機能を実装してください。

要件：
1. 1ページ10件表示
2. LIMITとOFFSETでデータ取得
3. 総ページ数を計算してページネーションリンクを表示
4. 現在のページを強調表示
5. 前へ・次へボタン（無効状態の処理も）
6. ページ番号が多い場合は省略表示（...）
7. URLパラメータでページ番号を保持
8. プリペアドステートメントでSQLインジェクション対策
9. htmlspecialchars()でXSS対策
10. 不正なページ番号のバリデーション

効率的でユーザーフレンドリーなページングを実装してください。」
```

### チェックポイント

✅ **データ取得**

- [ ] LIMITとOFFSETを使用
- [ ] プリペアドステートメントで安全に取得
- [ ] `bindValue()`で整数型をバインド

✅ **ページ計算**

- [ ] 総件数を取得（COUNT(*)）
- [ ] 総ページ数を計算（ceil()）
- [ ] OFFSETを正しく計算

✅ **表示**

- [ ] 前へ・次へボタンの表示制御
- [ ] 現在のページを強調表示
- [ ] ページ数が多い場合の省略表示

✅ **セキュリティ**

- [ ] ページ番号のバリデーション
- [ ] SQLインジェクション対策
- [ ] XSS対策（htmlspecialchars()、urlencode()）

---

## 💡 よくある問題

### 問題：OFFSETの計算が間違っている

**❌ 間違い**：

```php
$offset = $current_page * $per_page; // ページ1で10件スキップしてしまう
```

**✅ 正解**：

```php
$offset = ($current_page - 1) * $per_page; // ページ1で0件スキップ
```

---

### 問題：bindParam()でLIMITが動かない

**❌ 間違い**：

```php
$stmt->bindParam(':limit', $per_page); // 参照渡しで問題が起きることがある
```

**✅ 正解**：

```php
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT); // 値渡しで型指定
```

---

### 問題：ページネーションリンクが壊れる

**原因**：URLパラメータをエスケープしていない

**✅ 正解**：

```php
// URLパラメータはurlencode()を使う
<a href="?page=<?php echo $i; ?>&keyword=<?php echo urlencode($keyword); ?>">

// HTMLとして出力する場合はhtmlspecialchars()も
echo htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8');
```

---

## 📚 パフォーマンスのヒント

### COUNT(*)の最適化

```php
// ❌ 毎回COUNT(*)を実行（遅い）
$count = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();

// ✅ 結果をキャッシュ（速い）
$cache_key = 'article_count_' . md5($search_condition);
$count = $_SESSION[$cache_key] ?? null;

if ($count === null) {
    $count = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
    $_SESSION[$cache_key] = $count;
}
```

### インデックスの活用

```sql
-- よく使う検索条件にインデックスを作成
CREATE INDEX idx_created_at ON articles(created_at);
CREATE INDEX idx_category ON articles(category);

-- EXPLAIN で実行計画を確認
EXPLAIN SELECT * FROM articles WHERE category = 'テクノロジー' ORDER BY created_at DESC LIMIT 10;
```

---

👉 **[解答例を見る](solutions/README.md)**

**Let's vibe and code! 🎉**

ページング処理をマスターして、大量データを扱えるようになろう！
