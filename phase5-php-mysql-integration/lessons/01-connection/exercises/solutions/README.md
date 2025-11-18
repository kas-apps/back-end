# Lesson 01: データベース接続 - 解答例 🔌

このファイルでは、Lesson 01の演習問題の詳細な解答例と解説を提供します！

---

## 🌱 基礎編

### 問題1-1：基本的な接続

```php
<?php
// データベース接続情報
$host = 'localhost';
$port = '8889';  // MAMPのデフォルトポート
$dbname = 'phase5_practice';
$username = 'root';
$password = 'root';

// DSN（Data Source Name）を作成
$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

// PDOで接続
$pdo = new PDO($dsn, $username, $password);

// 接続成功メッセージを表示
echo "接続成功！";
?>
```

**✅ セキュリティポイント**：
- ✅ `charset=utf8mb4`を指定：文字化けとSQLインジェクション対策
- ⚠️ エラーハンドリングがない：次の問題で改善します

**💡 コードのポイント**：
- DSNは「データソース名」で、接続先の情報を文字列で指定
- `utf8mb4`は絵文字も扱える完全なUTF-8エンコーディング
- MAMPではポート`8889`を明示的に指定する必要がある

---

### 問題1-2：エラーハンドリング

```php
<?php
// データベース接続情報
$host = 'localhost';
$port = '8889';
$dbname = 'phase5_practice';
$username = 'root';
$password = 'root';

$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

try {
    // PDOで接続（エラーモードを例外に設定）
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo "接続成功！";

} catch (PDOException $e) {
    // 接続エラー時の処理
    echo "接続エラー: " . $e->getMessage();
}
?>
```

**✅ セキュリティポイント**：
- ✅ try-catchでエラーハンドリング：予期しないエラーに対応
- ✅ `ERRMODE_EXCEPTION`を設定：エラーを例外として扱う
- ⚠️ 本番環境では詳細なエラーメッセージを表示しない（問題1-7で改善）

**💡 コードのポイント**：
- `PDOException`は PDO 関連のエラーをキャッチする専用の例外クラス
- エラーモードを例外に設定すると、SQL実行時のエラーも自動的に例外として投げられる
- `getMessage()`でエラーの詳細情報を取得できる

**🚨 よくある間違い**：
```php
// ❌ 悪い例：エラーハンドリングなし
$pdo = new PDO($dsn, $username, $password);
// → エラー時にFatal Errorで停止してしまう

// ✅ 良い例：try-catchで適切に処理
try {
    $pdo = new PDO($dsn, $username, $password);
} catch (PDOException $e) {
    // エラーを適切に処理
}
```

---

### 問題1-3：接続情報の表示

```php
<?php
$host = 'localhost';
$port = '8889';
$dbname = 'phase5_practice';
$username = 'root';
$password = 'root';

$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // 1. MySQLバージョンを取得
    $stmt = $pdo->query("SELECT VERSION()");
    $version = $stmt->fetchColumn();
    echo "MySQLバージョン: " . htmlspecialchars($version, ENT_QUOTES, 'UTF-8') . "<br>";

    // 2. 接続中のデータベース名を取得
    $stmt = $pdo->query("SELECT DATABASE()");
    $database = $stmt->fetchColumn();
    echo "接続中のデータベース: " . htmlspecialchars($database, ENT_QUOTES, 'UTF-8') . "<br>";

    // 3. 現在の文字コードを取得
    $stmt = $pdo->query("SELECT @@character_set_database");
    $charset = $stmt->fetchColumn();
    echo "データベース文字コード: " . htmlspecialchars($charset, ENT_QUOTES, 'UTF-8') . "<br>";

} catch (PDOException $e) {
    echo "エラー: " . $e->getMessage();
}
?>
```

**✅ セキュリティポイント**：
- ✅ `htmlspecialchars()`でXSS対策：出力時にエスケープ処理
- ✅ `ENT_QUOTES`と`UTF-8`を指定：完全なエスケープ

**💡 コードのポイント**：
- `query()`：単純なSELECT文を実行（パラメータなし）
- `fetchColumn()`：結果の最初のカラム（列）を取得
- `@@character_set_database`：MySQLのシステム変数を参照

**🎓 学習ポイント**：
```php
// fetchColumn() は結果の最初の列を取得
$stmt = $pdo->query("SELECT VERSION()");
$version = $stmt->fetchColumn();  // "8.0.28" のような文字列

// fetch() は1行全体を配列で取得
$stmt = $pdo->query("SELECT VERSION() AS version");
$row = $stmt->fetch(PDO::FETCH_ASSOC);  // ['version' => '8.0.28']
```

---

## 🚀 応用編

### 問題1-4：接続オプションの設定

```php
<?php
$host = 'localhost';
$port = '8889';
$dbname = 'phase5_practice';
$username = 'root';
$password = 'root';

$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

try {
    // セキュアな接続オプションを設定
    $pdo = new PDO($dsn, $username, $password, [
        // エラーモード：例外を投げる
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

        // デフォルトフェッチモード：連想配列
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

        // エミュレートプリペア：無効化（真のプリペアドステートメントを使用）
        PDO::ATTR_EMULATE_PREPARES => false
    ]);

    echo "接続成功！セキュアな設定が適用されました。<br>";

    // 設定を確認
    echo "エラーモード: " . $pdo->getAttribute(PDO::ATTR_ERRMODE) . "<br>";
    echo "デフォルトフェッチモード: " . $pdo->getAttribute(PDO::ATTR_DEFAULT_FETCH_MODE) . "<br>";
    echo "エミュレートプリペア: " . ($pdo->getAttribute(PDO::ATTR_EMULATE_PREPARES) ? 'true' : 'false') . "<br>";

} catch (PDOException $e) {
    echo "接続エラー: " . $e->getMessage();
}
?>
```

**✅ セキュリティポイント**：
- ✅ `ATTR_ERRMODE => ERRMODE_EXCEPTION`：エラーを例外として扱う（セキュアなエラー処理）
- ✅ `ATTR_EMULATE_PREPARES => false`：真のプリペアドステートメントを使用（SQLインジェクション対策）
- ✅ `ATTR_DEFAULT_FETCH_MODE => FETCH_ASSOC`：連想配列で取得（数値添字の混在を防ぐ）

**💡 コードのポイント**：
- 接続オプションは PDO コンストラクタの第4引数に配列で渡す
- `getAttribute()`で設定値を確認できる
- これらのオプションは**すべてのPDO接続で設定すべき**重要な設定

**🎓 各オプションの詳細**：

1. **ATTR_ERRMODE（エラーモード）**：
   ```php
   // ERRMODE_SILENT: エラーを無視（デフォルト、非推奨）
   // ERRMODE_WARNING: 警告を発生（非推奨）
   // ERRMODE_EXCEPTION: 例外を投げる（推奨）✅
   ```

2. **ATTR_DEFAULT_FETCH_MODE（フェッチモード）**：
   ```php
   // FETCH_BOTH: 連想配列と数値配列の両方（デフォルト、非推奨）
   // FETCH_ASSOC: 連想配列のみ（推奨）✅
   // FETCH_NUM: 数値配列のみ
   // FETCH_OBJ: オブジェクトとして取得
   ```

3. **ATTR_EMULATE_PREPARES（エミュレートプリペア）**：
   ```php
   // true: PHPでエミュレート（デフォルト、非推奨）
   // false: データベースで真のプリペアドステートメント（推奨）✅
   ```

**📊 設定比較**：
```php
// ❌ デフォルト設定（非推奨）
$pdo = new PDO($dsn, $username, $password);
// - エラーは無視される
// - fetch()で連想配列と数値配列が混在
// - プリペアドステートメントがエミュレートされる

// ✅ セキュアな設定（推奨）
$pdo = new PDO($dsn, $username, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
]);
// - エラーは例外として投げられる
// - fetch()で連想配列のみ取得
// - 真のプリペアドステートメントを使用
```

---

### 問題1-5：設定ファイルの分離（config.php）

**config.php**：
```php
<?php
// データベース接続情報を定数で定義
define('DB_HOST', 'localhost');
define('DB_PORT', '8889');
define('DB_NAME', 'phase5_practice');
define('DB_USER', 'root');
define('DB_PASS', 'root');

// DSNを作成
$dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';

try {
    // セキュアな接続オプションでPDOインスタンスを作成
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);

} catch (PDOException $e) {
    // 本番環境では詳細なエラーメッセージを表示しない
    die("データベース接続エラーが発生しました。");
    // 開発環境では詳細を表示する場合：
    // die("データベース接続エラー: " . $e->getMessage());
}
?>
```

**test.php**：
```php
<?php
// 設定ファイルを読み込み
require_once 'config.php';

// $pdoはconfig.phpで作成済み
try {
    // データベース名を取得
    $stmt = $pdo->query("SELECT DATABASE()");
    $dbname = $stmt->fetchColumn();
    echo "接続中のデータベース: " . htmlspecialchars($dbname, ENT_QUOTES, 'UTF-8') . "<br>";

    // MySQLバージョンを取得
    $stmt = $pdo->query("SELECT VERSION()");
    $version = $stmt->fetchColumn();
    echo "MySQLバージョン: " . htmlspecialchars($version, ENT_QUOTES, 'UTF-8') . "<br>";

    echo "<br>接続は正常に動作しています！";

} catch (PDOException $e) {
    echo "クエリエラー: " . $e->getMessage();
}
?>
```

**✅ セキュリティポイント**：
- ✅ 接続情報を定数で管理：変更が容易
- ✅ `config.php`を`.gitignore`に追加：機密情報の漏洩を防ぐ
- ✅ 本番環境では詳細なエラーメッセージを表示しない
- ✅ `require_once`を使用：重複読み込みを防ぐ

**💡 コードのポイント**：
- `define()`で定数を定義：変更できない値として管理
- `require_once`：ファイルを1回だけ読み込む（重複防止）
- `require`と`include`の違い：`require`はファイルが見つからない場合、Fatal Errorで停止

**🎓 実践的な改善**：

**.gitignore に追加**：
```
# データベース設定ファイル（機密情報）
config.php

# サンプルファイルはコミット
config.sample.php
```

**config.sample.php**（リポジトリにコミット）：
```php
<?php
// データベース接続情報のサンプル
// このファイルをコピーして config.php を作成してください

define('DB_HOST', 'localhost');
define('DB_PORT', '8889');
define('DB_NAME', 'your_database_name');  // ← 変更してください
define('DB_USER', 'your_username');       // ← 変更してください
define('DB_PASS', 'your_password');       // ← 変更してください

// ... 以下同じ ...
?>
```

**📋 使い方**：
1. `config.sample.php`を`config.php`にコピー
2. 接続情報を実際の値に変更
3. `config.php`は`.gitignore`で除外されているため、Gitにコミットされない

---

### 問題1-6：複数データベースへの接続

```php
<?php
$host = 'localhost';
$port = '8889';
$username = 'root';
$password = 'root';

try {
    // 1つ目のデータベースに接続
    $dsn1 = "mysql:host=$host;port=$port;dbname=phase5_practice;charset=utf8mb4";
    $pdo1 = new PDO($dsn1, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // 2つ目のデータベースに接続
    $dsn2 = "mysql:host=$host;port=$port;dbname=mysql;charset=utf8mb4";
    $pdo2 = new PDO($dsn2, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // 1つ目のデータベース情報を表示
    $stmt = $pdo1->query("SELECT DATABASE()");
    $dbname1 = $stmt->fetchColumn();
    echo "データベース1: " . htmlspecialchars($dbname1, ENT_QUOTES, 'UTF-8') . "<br>";

    // 2つ目のデータベース情報を表示
    $stmt = $pdo2->query("SELECT DATABASE()");
    $dbname2 = $stmt->fetchColumn();
    echo "データベース2: " . htmlspecialchars($dbname2, ENT_QUOTES, 'UTF-8') . "<br>";

    // 両方のデータベースから情報を取得する例
    echo "<br><strong>phase5_practice のテーブル一覧:</strong><br>";
    $stmt = $pdo1->query("SHOW TABLES");
    while ($table = $stmt->fetchColumn()) {
        echo "- " . htmlspecialchars($table, ENT_QUOTES, 'UTF-8') . "<br>";
    }

    echo "<br><strong>mysql データベースのユーザー数:</strong><br>";
    $stmt = $pdo2->query("SELECT COUNT(*) FROM user");
    $userCount = $stmt->fetchColumn();
    echo "ユーザー数: " . htmlspecialchars($userCount, ENT_QUOTES, 'UTF-8') . "<br>";

} catch (PDOException $e) {
    echo "エラー: " . $e->getMessage();
}
?>
```

**✅ セキュリティポイント**：
- ✅ 各接続に適切なオプションを設定
- ✅ 出力時に`htmlspecialchars()`でエスケープ
- ⚠️ システムデータベース（mysql）への接続は慎重に（読み取り専用推奨）

**💡 コードのポイント**：
- 異なる変数名（`$pdo1`、`$pdo2`）で複数の接続を管理
- それぞれ独立した接続なので、トランザクションも独立
- `SHOW TABLES`：データベース内のテーブル一覧を取得

**🎓 実用例**：
```php
// 複数データベースを使った実用例：マイグレーションツール

// 旧データベースから取得
$stmt1 = $pdo1->query("SELECT * FROM old_users");
$oldUsers = $stmt1->fetchAll();

// 新データベースに挿入
$stmt2 = $pdo2->prepare("INSERT INTO new_users (name, email) VALUES (:name, :email)");

foreach ($oldUsers as $user) {
    $stmt2->execute([
        ':name' => $user['name'],
        ':email' => $user['email']
    ]);
}

echo "マイグレーション完了！";
```

**⚠️ 注意点**：
- 複数接続はメモリを消費するため、必要な時だけ使う
- 接続が不要になったら`$pdo = null;`で明示的に切断する
- トランザクションは各接続で独立して管理される

---

## 🛡️ セキュリティチャレンジ

### 問題1-7：本番環境とエラーメッセージ

```php
<?php
// 環境設定（本番環境ではtrue、開発環境ではfalse）
$is_production = false;  // ← 本番環境ではtrueに変更

// エラーログのパス
$error_log_file = __DIR__ . '/logs/database_errors.log';

// データベース接続情報
$host = 'localhost';
$port = '8889';
$dbname = 'phase5_practice';
$username = 'root';
$password = 'root';

$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);

    echo "接続成功！";

} catch (PDOException $e) {
    if ($is_production) {
        // 本番環境：ユーザーフレンドリーなメッセージのみ表示

        // エラー詳細をログファイルに記録
        $error_message = date('Y-m-d H:i:s') . " - データベース接続エラー: " . $e->getMessage() . "\n";
        error_log($error_message, 3, $error_log_file);

        // ユーザーには一般的なメッセージのみ表示
        die("データベース接続エラーが発生しました。しばらくしてから再度お試しください。");

    } else {
        // 開発環境：詳細なエラーメッセージを表示
        echo "<h3>開発環境 - エラー詳細</h3>";
        echo "<strong>エラーメッセージ:</strong> " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "<br>";
        echo "<strong>ファイル:</strong> " . htmlspecialchars($e->getFile(), ENT_QUOTES, 'UTF-8') . "<br>";
        echo "<strong>行番号:</strong> " . $e->getLine() . "<br>";
        echo "<strong>スタックトレース:</strong><br><pre>" . htmlspecialchars($e->getTraceAsString(), ENT_QUOTES, 'UTF-8') . "</pre>";
    }
}
?>
```

**✅ セキュリティポイント**：
- ✅ 本番環境では詳細なエラーメッセージを表示しない：機密情報の漏洩を防ぐ
- ✅ エラーをログファイルに記録：後で調査可能
- ✅ ユーザーフレンドリーなメッセージ：技術的な詳細を隠す
- ✅ 環境変数で本番/開発を切り替え：柔軟な運用

**💡 コードのポイント**：
- `error_log()`の第2引数に`3`：ファイルに追記
- `__DIR__`：現在のファイルのディレクトリパス
- `date('Y-m-d H:i:s')`：現在の日時をフォーマット
- スタックトレース：エラーが発生した経路を追跡

**🎓 環境変数での管理（推奨）**：
```php
// .env ファイル（本番環境と開発環境で異なる値）
APP_ENV=production  // または development

// config.php
$is_production = ($_ENV['APP_ENV'] === 'production');
```

**📂 ログファイルの準備**：
```bash
# logsディレクトリを作成
mkdir logs

# パーミッションを設定（書き込み可能）
chmod 755 logs
```

**.gitignore に追加**：
```
# ログファイル
logs/*.log
```

**🚨 本番環境で絶対にやってはいけないこと**：
```php
// ❌ 絶対にダメ！機密情報が漏洩する
catch (PDOException $e) {
    echo "エラー: " . $e->getMessage();  // データベース名、ホスト名が漏洩
}

// ✅ 正しい：一般的なメッセージのみ
catch (PDOException $e) {
    error_log($e->getMessage());  // ログに記録
    die("エラーが発生しました。");  // ユーザーには一般的なメッセージ
}
```

---

### 問題1-8：mysqli vs PDOの比較

まず、テーブルを作成します：

```sql
USE phase5_practice;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users (name, email) VALUES
('太郎', 'taro@example.com'),
('花子', 'hanako@example.com'),
('次郎', 'jiro@example.com');
```

**mysqli版**：
```php
<?php
// mysqli で接続
$mysqli = new mysqli('localhost', 'root', 'root', 'phase5_practice', 8889);

// 接続エラーチェック
if ($mysqli->connect_error) {
    die('接続エラー (' . $mysqli->connect_errno . '): ' . $mysqli->connect_error);
}

// 文字コードを設定
$mysqli->set_charset('utf8mb4');

echo "<h3>mysqli版</h3>";

// データを取得
$result = $mysqli->query("SELECT * FROM users");

if ($result) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>名前</th><th>メール</th><th>作成日時</th></tr>";

    // 結果を1行ずつ取得
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "</tr>";
    }

    echo "</table>";

    // 結果セットを解放
    $result->free();
} else {
    echo "クエリエラー: " . $mysqli->error;
}

// 接続を閉じる
$mysqli->close();
?>
```

**PDO版**：
```php
<?php
// PDO で接続
try {
    $pdo = new PDO(
        'mysql:host=localhost;port=8889;dbname=phase5_practice;charset=utf8mb4',
        'root',
        'root',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    echo "<h3>PDO版</h3>";

    // データを取得
    $stmt = $pdo->query("SELECT * FROM users");
    $users = $stmt->fetchAll();

    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>名前</th><th>メール</th><th>作成日時</th></tr>";

    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars($user['created_at'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "</tr>";
    }

    echo "</table>";

} catch (PDOException $e) {
    echo "エラー: " . $e->getMessage();
}

// PDOは自動的に接続を閉じる（明示的な close() は不要）
?>
```

**📊 比較レポート**：

| 項目 | mysqli | PDO | 推奨 |
|-----|--------|-----|-----|
| **対応データベース** | MySQLのみ | MySQL、PostgreSQL、SQLite、Oracle など | PDO ✅ |
| **接続方法** | `new mysqli()` | `new PDO()` | - |
| **エラーハンドリング** | if文でチェック | try-catch（例外） | PDO ✅ |
| **文字コード設定** | `set_charset()`を別途呼ぶ必要 | DSNで指定可能 | PDO ✅ |
| **プリペアドステートメント** | `prepare()`, `bind_param()` | `prepare()`, `bindParam()` または `execute([])` | PDO ✅ |
| **フェッチ方法** | `fetch_assoc()` | `fetch()`, `fetchAll()` | PDO ✅ |
| **接続の切断** | `close()`を明示的に呼ぶ | 自動（スコープ外で自動切断） | PDO ✅ |
| **エラー情報** | `$mysqli->error` | 例外のメッセージ | PDO ✅ |
| **学習曲線** | やや急（手続き型とOOP混在） | 緩やか（一貫したOOP） | PDO ✅ |

**💡 コードの違い**：

1. **接続方法**：
   ```php
   // mysqli: 引数の順番が分かりにくい
   $mysqli = new mysqli('localhost', 'root', 'root', 'phase5_practice', 8889);

   // PDO: DSN形式で明確
   $pdo = new PDO('mysql:host=localhost;port=8889;dbname=phase5_practice;charset=utf8mb4', 'root', 'root');
   ```

2. **エラーハンドリング**：
   ```php
   // mysqli: if文で毎回チェック
   if ($mysqli->connect_error) {
       die('接続エラー: ' . $mysqli->connect_error);
   }

   // PDO: try-catch で一括処理
   try {
       $pdo = new PDO($dsn, $username, $password);
   } catch (PDOException $e) {
       die('接続エラー: ' . $e->getMessage());
   }
   ```

3. **プリペアドステートメント**：
   ```php
   // mysqli: 型を指定する必要がある
   $stmt = $mysqli->prepare("SELECT * FROM users WHERE email = ?");
   $stmt->bind_param("s", $email);  // "s" は string の意味
   $stmt->execute();

   // PDO: 型の指定は不要（自動判定）
   $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
   $stmt->execute([':email' => $email]);
   ```

**✅ 結論：どちらを使うべきか**

**PDOを推奨する理由**：
1. ✅ **データベースの移植性**：MySQL以外のデータベースにも対応
2. ✅ **一貫したエラーハンドリング**：例外ベースで扱いやすい
3. ✅ **シンプルな記法**：プリペアドステートメントが簡潔
4. ✅ **将来性**：PHPコミュニティで推奨されている
5. ✅ **セキュリティ**：デフォルトで安全な設定が可能

**mysqliを使う場合**：
- ⚠️ MySQLの特殊機能（マルチクエリなど）を使う必要がある場合
- ⚠️ 既存のレガシーコードがmysqliで書かれている場合

**🎓 学習のポイント**：
- バイブコーダーとして、AIに「PDOを使って」と明示的に指示することが重要
- mysqli のコードが生成された場合は、PDO への書き換えを依頼する
- 新規プロジェクトでは**必ずPDOを選択**

---

### 問題1-9：接続プールとパフォーマンス

```php
<?php
$host = 'localhost';
$port = '8889';
$dbname = 'phase5_practice';
$username = 'root';
$password = 'root';

$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

echo "<h3>接続パフォーマンスの比較</h3>";

// =====================
// 1. 通常の接続
// =====================
$start = microtime(true);

for ($i = 0; $i < 5; $i++) {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    $stmt = $pdo->query("SELECT DATABASE()");
    $dbname_result = $stmt->fetchColumn();
    $pdo = null;  // 接続を切断
}

$end = microtime(true);
$normal_time = $end - $start;

echo "通常の接続（5回）: " . number_format($normal_time, 6) . " 秒<br>";

// =====================
// 2. 持続的接続
// =====================
$start = microtime(true);

for ($i = 0; $i < 5; $i++) {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_PERSISTENT => true  // 持続的接続を有効化
    ]);
    $stmt = $pdo->query("SELECT DATABASE()");
    $dbname_result = $stmt->fetchColumn();
    // 持続的接続は close() しても接続が残る
}

$end = microtime(true);
$persistent_time = $end - $start;

echo "持続的接続（5回）: " . number_format($persistent_time, 6) . " 秒<br>";

// =====================
// パフォーマンス比較
// =====================
$improvement = (($normal_time - $persistent_time) / $normal_time) * 100;
echo "<br><strong>パフォーマンス改善率: " . number_format($improvement, 2) . "%</strong><br>";

// =====================
// 3. 再利用による最適化
// =====================
$start = microtime(true);

$pdo = new PDO($dsn, $username, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

for ($i = 0; $i < 5; $i++) {
    $stmt = $pdo->query("SELECT DATABASE()");
    $dbname_result = $stmt->fetchColumn();
}

$end = microtime(true);
$reuse_time = $end - $start;

echo "<br>接続再利用（1回接続、5回クエリ）: " . number_format($reuse_time, 6) . " 秒<br>";

$improvement2 = (($normal_time - $reuse_time) / $normal_time) * 100;
echo "<strong>パフォーマンス改善率: " . number_format($improvement2, 2) . "%</strong><br>";

?>
```

**実行結果の例**：
```
接続パフォーマンスの比較
通常の接続（5回）: 0.025000 秒
持続的接続（5回）: 0.005000 秒
パフォーマンス改善率: 80.00%

接続再利用（1回接続、5回クエリ）: 0.003000 秒
パフォーマンス改善率: 88.00%
```

**✅ セキュリティポイント**：
- ⚠️ 持続的接続はトランザクション状態が残る可能性がある
- ⚠️ 接続プールの管理を適切に行う必要がある
- ✅ 接続再利用は最も安全で高速

**💡 コードのポイント**：
- `microtime(true)`：マイクロ秒単位の現在時刻を取得
- `ATTR_PERSISTENT => true`：持続的接続を有効化
- 持続的接続は、PHPスクリプトが終了しても接続が維持される

**📊 各方式の比較**：

| 方式 | メリット | デメリット | 推奨度 |
|-----|---------|-----------|-------|
| **通常の接続** | - シンプル<br>- トランザクション状態がクリーン | - 毎回接続コストがかかる | ✅ 標準 |
| **持続的接続** | - 接続時間の短縮<br>- パフォーマンス向上 | - トランザクション状態が残る<br>- リソースを占有 | ⚠️ 慎重に |
| **接続再利用** | - 最も高速<br>- リソース効率が良い | - リクエスト内でのみ有効 | ✅✅ 最推奨 |

**🎓 実践的なアドバイス**：

**推奨パターン**（接続再利用）：
```php
// config.php で1回だけ接続
$pdo = new PDO($dsn, $username, $password, [...]);

// 各スクリプトで再利用
require_once 'config.php';
$stmt = $pdo->query("SELECT ...");  // 同じ $pdo を使い回す
```

**持続的接続を使う場合の注意**：
```php
$pdo = new PDO($dsn, $username, $password, [
    PDO::ATTR_PERSISTENT => true
]);

// トランザクションを必ずロールバック
if ($pdo->inTransaction()) {
    $pdo->rollBack();  // 前のトランザクションをクリア
}
```

**⚠️ 持続的接続の問題例**：
```php
// リクエスト1: トランザクション開始
$pdo->beginTransaction();
$pdo->exec("INSERT INTO users ...");
// スクリプト終了（トランザクション未完了）

// リクエスト2: 同じ持続的接続を再利用
// → トランザクションが残っている状態で開始される（バグの原因）
```

**✅ 結論**：
- 通常のWebアプリケーションでは**接続再利用**が最適
- 持続的接続は特殊なケースのみ（高頻度アクセス、低レイテンシ要求）
- パフォーマンスよりも**正確性とセキュリティ**を優先

---

## 💪 総合チャレンジ

### 問題1-10：完全なデータベース接続クラスの作成

**Database.php**：
```php
<?php
/**
 * データベース接続クラス（シングルトンパターン）
 *
 * 使用例:
 * $db = Database::getInstance();
 * $pdo = $db->getConnection();
 * $stmt = $pdo->query("SELECT * FROM users");
 */
class Database {
    // シングルトンインスタンス
    private static $instance = null;

    // PDO接続
    private $pdo;

    // 接続設定
    private $host = 'localhost';
    private $port = '8889';
    private $dbname = 'phase5_practice';
    private $username = 'root';
    private $password = 'root';
    private $charset = 'utf8mb4';

    /**
     * コンストラクタ（private: 外部からのインスタンス化を禁止）
     */
    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset={$this->charset}";

            $this->pdo = new PDO($dsn, $this->username, $this->password, [
                // エラーモード：例外を投げる
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

                // デフォルトフェッチモード：連想配列
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

                // エミュレートプリペア：無効化
                PDO::ATTR_EMULATE_PREPARES => false,

                // 持続的接続：無効（必要に応じて有効化）
                PDO::ATTR_PERSISTENT => false
            ]);

        } catch (PDOException $e) {
            // エラーログに記録
            error_log("データベース接続エラー: " . $e->getMessage());

            // ユーザーフレンドリーなエラーメッセージ
            die("データベース接続エラーが発生しました。管理者に連絡してください。");
        }
    }

    /**
     * クローンを禁止（シングルトンパターンの一部）
     */
    private function __clone() {
        throw new Exception("Clone is not allowed.");
    }

    /**
     * シリアライズを禁止（シングルトンパターンの一部）
     */
    public function __wakeup() {
        throw new Exception("Unserializing is not allowed.");
    }

    /**
     * インスタンスを取得（シングルトンパターン）
     *
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * PDO接続を取得
     *
     * @return PDO
     */
    public function getConnection() {
        return $this->pdo;
    }

    /**
     * 接続をテスト
     *
     * @return bool 接続が有効ならtrue
     */
    public function testConnection() {
        try {
            $stmt = $this->pdo->query("SELECT 1");
            return true;
        } catch (PDOException $e) {
            error_log("接続テスト失敗: " . $e->getMessage());
            return false;
        }
    }

    /**
     * データベース情報を取得
     *
     * @return array データベース情報
     */
    public function getInfo() {
        try {
            $info = [];

            // データベース名
            $stmt = $this->pdo->query("SELECT DATABASE()");
            $info['database'] = $stmt->fetchColumn();

            // MySQLバージョン
            $stmt = $this->pdo->query("SELECT VERSION()");
            $info['version'] = $stmt->fetchColumn();

            // 文字コード
            $stmt = $this->pdo->query("SELECT @@character_set_database");
            $info['charset'] = $stmt->fetchColumn();

            return $info;

        } catch (PDOException $e) {
            error_log("情報取得エラー: " . $e->getMessage());
            return [];
        }
    }

    /**
     * トランザクションを開始
     */
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    /**
     * トランザクションをコミット
     */
    public function commit() {
        return $this->pdo->commit();
    }

    /**
     * トランザクションをロールバック
     */
    public function rollBack() {
        return $this->pdo->rollBack();
    }

    /**
     * トランザクション中かどうかを確認
     *
     * @return bool
     */
    public function inTransaction() {
        return $this->pdo->inTransaction();
    }
}
?>
```

**test_database.php**（使用例）：
```php
<?php
require_once 'Database.php';

echo "<h3>データベース接続クラスのテスト</h3>";

// =====================
// 1. インスタンスを取得
// =====================
$db = Database::getInstance();

echo "✅ Databaseインスタンスを取得しました<br>";

// =====================
// 2. 接続をテスト
// =====================
if ($db->testConnection()) {
    echo "✅ データベース接続が正常です<br><br>";
} else {
    echo "❌ データベース接続に失敗しました<br><br>";
}

// =====================
// 3. データベース情報を取得
// =====================
$info = $db->getInfo();
echo "<strong>データベース情報:</strong><br>";
echo "- データベース名: " . htmlspecialchars($info['database'], ENT_QUOTES, 'UTF-8') . "<br>";
echo "- MySQLバージョン: " . htmlspecialchars($info['version'], ENT_QUOTES, 'UTF-8') . "<br>";
echo "- 文字コード: " . htmlspecialchars($info['charset'], ENT_QUOTES, 'UTF-8') . "<br><br>";

// =====================
// 4. PDO接続を使ってクエリ実行
// =====================
$pdo = $db->getConnection();

try {
    $stmt = $pdo->query("SELECT * FROM users LIMIT 3");
    $users = $stmt->fetchAll();

    echo "<strong>ユーザー一覧:</strong><br>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>名前</th><th>メール</th></tr>";

    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "</tr>";
    }

    echo "</table><br>";

} catch (PDOException $e) {
    echo "クエリエラー: " . $e->getMessage() . "<br>";
}

// =====================
// 5. シングルトンパターンの確認
// =====================
$db2 = Database::getInstance();

if ($db === $db2) {
    echo "✅ シングルトンパターンが正しく動作しています（同じインスタンス）<br>";
} else {
    echo "❌ シングルトンパターンが動作していません<br>";
}

// =====================
// 6. トランザクションのテスト
// =====================
try {
    $db->beginTransaction();
    echo "<br>✅ トランザクション開始<br>";

    // INSERT文を実行（テスト用）
    $stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");
    $stmt->execute([
        ':name' => 'テストユーザー',
        ':email' => 'test_' . time() . '@example.com'  // ユニーク制約対策
    ]);

    echo "✅ データ挿入成功<br>";

    // コミット
    $db->commit();
    echo "✅ トランザクションコミット完了<br>";

} catch (PDOException $e) {
    // エラー時はロールバック
    if ($db->inTransaction()) {
        $db->rollBack();
        echo "❌ エラー発生：ロールバックしました<br>";
    }
    echo "エラー: " . $e->getMessage() . "<br>";
}

echo "<br><strong>すべてのテストが完了しました！</strong>";
?>
```

**✅ セキュリティポイント**：
- ✅ シングルトンパターン：インスタンスが1つのみ（接続の無駄を防ぐ）
- ✅ private コンストラクタ：外部からのインスタンス化を禁止
- ✅ セキュアな接続オプション：すべて設定済み
- ✅ エラーログ：本番環境でも追跡可能
- ✅ トランザクション機能：データ整合性を保証

**💡 コードのポイント**：
- **シングルトンパターン**：アプリケーション全体で1つのインスタンスのみ
- `__clone()`を禁止：クローンによる複製を防ぐ
- `__wakeup()`を禁止：シリアライズからの復元を防ぐ
- `static $instance`：クラス変数でインスタンスを保持

**🎓 シングルトンパターンの利点**：

1. **リソースの節約**：
   ```php
   // ❌ 悪い例：毎回新しい接続を作成（リソース無駄）
   $pdo1 = new PDO($dsn, $username, $password);
   $pdo2 = new PDO($dsn, $username, $password);
   $pdo3 = new PDO($dsn, $username, $password);

   // ✅ 良い例：1つの接続を再利用
   $db = Database::getInstance();  // 初回のみ接続作成
   $db2 = Database::getInstance();  // 既存の接続を再利用
   $db3 = Database::getInstance();  // 既存の接続を再利用
   ```

2. **一貫した設定**：
   ```php
   // すべてのコードで同じ接続オプションが適用される
   $db = Database::getInstance();
   $pdo = $db->getConnection();
   // → 必ず ERRMODE_EXCEPTION、FETCH_ASSOC などが設定されている
   ```

3. **グローバルアクセスポイント**：
   ```php
   // どこからでも同じインスタンスにアクセス可能

   // file1.php
   $db = Database::getInstance();

   // file2.php
   $db = Database::getInstance();  // 同じインスタンス
   ```

**📂 設定ファイル分離版**（追加課題）：

**config.ini**：
```ini
[database]
host = localhost
port = 8889
dbname = phase5_practice
username = root
password = root
charset = utf8mb4
```

**Database.php の改良版**：
```php
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct($configFile = 'config.ini') {
        // INIファイルから設定を読み込み
        $config = parse_ini_file($configFile, true);

        if ($config === false || !isset($config['database'])) {
            throw new Exception("設定ファイルの読み込みに失敗しました。");
        }

        $db = $config['database'];

        $dsn = "mysql:host={$db['host']};port={$db['port']};dbname={$db['dbname']};charset={$db['charset']}";

        $this->pdo = new PDO($dsn, $db['username'], $db['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
    }

    // ... 以下同じ ...
}
```

**🤖 バイブコーディングのポイント**：

**AIへの指示例**：
```
「シングルトンパターンでデータベース接続クラスを作成してください。

要件：
1. クラス名: Database
2. 機能:
   - getInstance() でインスタンス取得
   - getConnection() で PDO 接続取得
   - testConnection() で接続テスト
   - getInfo() でデータベース情報取得
3. シングルトンパターン:
   - private コンストラクタ
   - クローン禁止
   - シリアライズ禁止
4. セキュアな接続オプション:
   - ERRMODE_EXCEPTION
   - FETCH_ASSOC
   - EMULATE_PREPARES = false
5. エラーハンドリング:
   - try-catch で例外処理
   - error_log() でログ記録」
```

**生成コードのチェックポイント**：
- [ ] コンストラクタが`private`になっているか
- [ ] `getInstance()`がstatic メソッドか
- [ ] `__clone()`と`__wakeup()`が禁止されているか
- [ ] 接続オプションが適切に設定されているか
- [ ] エラーハンドリングがあるか

---

## 🎉 まとめ

お疲れ様でした！データベース接続の基礎から応用までマスターしました！

**学んだこと**：
- ✅ PDOでの基本的な接続方法
- ✅ try-catchでのエラーハンドリング
- ✅ セキュアな接続オプションの設定
- ✅ 設定ファイルの分離
- ✅ 複数データベースへの接続
- ✅ 本番環境でのエラーメッセージ管理
- ✅ mysqli vs PDOの比較（PDO推奨）
- ✅ 持続的接続とパフォーマンス最適化
- ✅ シングルトンパターンでの接続管理

**次のステップ**：
👉 **[Lesson 02: プリペアドステートメント](../../02-prepared-statements/README.md)** でSQLインジェクション対策を学ぼう！

**Let's vibe and code! 🎉**
