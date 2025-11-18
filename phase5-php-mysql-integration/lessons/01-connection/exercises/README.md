# Lesson 01: データベース接続 - 演習問題 🔌

PHPからMySQLへの安全な接続をマスターして、セキュアなバックエンド開発の第一歩を踏み出そう！

---

## 📝 準備

演習を始める前に、データベースを準備しよう！

```sql
CREATE DATABASE IF NOT EXISTS phase5_practice CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

phpMyAdminまたはMySQLクライアントで実行してね！

---

## 🌱 基礎編

### 問題1-1：基本的な接続

**課題**：

PHPからMySQLに接続し、「接続成功！」と表示するプログラムを作成してください。

**要件**：
- MAMP環境で動作すること（ポート8889）
- PDOを使用
- DSNに文字コード（`charset=utf8mb4`）を指定
- 接続成功時にメッセージを表示

**ヒント**：

```php
$dsn = "mysql:host=localhost;port=8889;dbname=phase5_practice;charset=utf8mb4";
$pdo = new PDO($dsn, 'root', 'root');
```

---

### 問題1-2：エラーハンドリング

**課題**：

try-catchでエラーハンドリングを追加してください。

**要件**：
- try-catchブロックを使用
- `PDOException`をキャッチ
- 接続成功時：「接続成功！」を表示
- 接続失敗時：「接続エラー: [エラーメッセージ]」を表示

---

### 問題1-3：接続情報の表示

**課題**：

データベースに接続し、以下の情報を表示してください。

**表示する情報**：
1. MySQLのバージョン（`SELECT VERSION()`）
2. 接続中のデータベース名（`SELECT DATABASE()`）
3. 現在の文字コード（`SELECT @@character_set_database`）

**ヒント**：

```php
$stmt = $pdo->query("SELECT VERSION()");
$version = $stmt->fetchColumn();
echo "MySQLバージョン: " . htmlspecialchars($version, ENT_QUOTES, 'UTF-8');
```

---

## 🚀 応用編

### 問題1-4：接続オプションの設定

**課題**：

以下の接続オプションを設定したデータベース接続を作成してください。

**設定する接続オプション**：
1. **エラーモード**：例外を投げる（`ERRMODE_EXCEPTION`）
2. **デフォルトフェッチモード**：連想配列（`FETCH_ASSOC`）
3. **エミュレートプリペア**：無効化（`EMULATE_PREPARES => false`）

**ヒント**：

```php
$pdo = new PDO($dsn, $username, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
]);
```

**設定後の確認方法**：

エラーモードが正しく設定されているかテストしてみよう！

```php
// わざと存在しないデータベースに接続してみる
try {
    $pdo = new PDO("mysql:host=localhost;dbname=nonexistent", 'root', 'root', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    echo "エラーがキャッチされました: " . $e->getMessage();
}
```

---

### 問題1-5：設定ファイルの分離（config.php）

**課題**：

データベース接続情報を`config.php`として分離し、他のPHPファイルから使えるようにしてください。

**config.php の要件**：
- `define()`で定数を定義（DB_HOST、DB_PORT、DB_NAME、DB_USER、DB_PASS）
- PDOインスタンスを作成して`$pdo`変数に格納
- セキュアな接続オプションを設定
- try-catchでエラーハンドリング

**test.php の要件**：
- `require_once 'config.php'`で読み込み
- `$pdo`を使ってデータベース操作を実行
- 接続が成功していることを確認

**ヒント**：

```php
// config.php
<?php
define('DB_HOST', 'localhost');
define('DB_PORT', '8889');
define('DB_NAME', 'phase5_practice');
define('DB_USER', 'root');
define('DB_PASS', 'root');

$dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch (PDOException $e) {
    die("データベース接続エラー");
}
?>

// test.php
<?php
require_once 'config.php';

$stmt = $pdo->query("SELECT DATABASE()");
$dbname = $stmt->fetchColumn();
echo "接続中のデータベース: " . htmlspecialchars($dbname, ENT_QUOTES, 'UTF-8');
?>
```

---

### 問題1-6：複数データベースへの接続

**課題**：

複数のデータベースに接続し、それぞれのデータベース名を表示してください。

**要件**：
- `phase5_practice`と`mysql`（システムデータベース）に接続
- それぞれのPDOインスタンスを作成
- それぞれのデータベース名を表示
- 異なる変数名を使用（`$pdo1`、`$pdo2`）

**ヒント**：

```php
$pdo1 = new PDO("mysql:host=localhost;port=8889;dbname=phase5_practice;charset=utf8mb4", 'root', 'root');
$pdo2 = new PDO("mysql:host=localhost;port=8889;dbname=mysql;charset=utf8mb4", 'root', 'root');
```

---

## 🛡️ セキュリティチャレンジ

### 問題1-7：本番環境とエラーメッセージ

**課題**：

本番環境用のエラーハンドリングを実装してください。

**要件**：
- 本番環境では詳細なエラーメッセージを表示しない
- 開発環境では詳細なエラーメッセージを表示
- 環境を切り替える変数（`$is_production`）を使用
- 本番環境ではエラーをログファイルに記録

**ヒント**：

```php
$is_production = false; // 本番環境ではtrue

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    if ($is_production) {
        // 本番環境：ユーザーフレンドリーなメッセージ
        error_log($e->getMessage()); // ログに記録
        die("データベース接続エラーが発生しました。管理者に連絡してください。");
    } else {
        // 開発環境：詳細なエラーメッセージ
        die("接続エラー: " . $e->getMessage());
    }
}
```

---

### 問題1-8：mysqli vs PDOの比較

**課題**：

同じデータベース操作を、mysqli と PDO の両方で実装し、違いを理解してください。

**タスク**：

1. **mysqli版**を作成：
   - mysqliで接続
   - `users`テーブルから全件取得
   - 結果を表示

2. **PDO版**を作成：
   - PDOで接続
   - 同じ`users`テーブルから全件取得
   - 結果を表示

3. **比較レポート**を書く：
   - コードの違い
   - エラーハンドリングの違い
   - どちらを使うべきか、その理由

**テーブル作成SQL**：

```sql
USE phase5_practice;

CREATE TABLE users (
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

**mysqli版のヒント**：

```php
// mysqli版
$mysqli = new mysqli('localhost', 'root', 'root', 'phase5_practice', 8889);

if ($mysqli->connect_error) {
    die('接続エラー: ' . $mysqli->connect_error);
}

$result = $mysqli->query("SELECT * FROM users");

while ($row = $result->fetch_assoc()) {
    echo $row['name'] . "<br>";
}

$mysqli->close();
```

**PDO版のヒント**：

```php
// PDO版
try {
    $pdo = new PDO('mysql:host=localhost;port=8889;dbname=phase5_practice;charset=utf8mb4', 'root', 'root');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT * FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($users as $user) {
        echo $user['name'] . "<br>";
    }
} catch (PDOException $e) {
    die('接続エラー: ' . $e->getMessage());
}
```

---

### 問題1-9：接続プールとパフォーマンス

**課題**：

持続的接続（Persistent Connection）を設定し、通常の接続と比較してください。

**要件**：
- 通常の接続と持続的接続を実装
- 複数回のリクエストをシミュレート
- パフォーマンスを測定

**ヒント**：

```php
// 通常の接続
$start = microtime(true);
$pdo = new PDO($dsn, $username, $password);
$end = microtime(true);
echo "通常の接続時間: " . ($end - $start) . "秒<br>";

// 持続的接続
$start = microtime(true);
$pdo = new PDO($dsn, $username, $password, [
    PDO::ATTR_PERSISTENT => true
]);
$end = microtime(true);
echo "持続的接続時間: " . ($end - $start) . "秒<br>";
```

**注意**：
- 持続的接続は本番環境では慎重に使用する
- メリット：接続時間の短縮
- デメリット：リソースの占有、トランザクション状態の持ち越し

---

## 💪 総合チャレンジ

### 問題1-10：完全なデータベース接続クラスの作成

**課題**：

再利用可能な`Database`クラスを作成し、シングルトンパターンで実装してください。

**要件**：

**機能**：
- シングルトンパターン（1つのインスタンスのみ作成）
- 設定ファイルから接続情報を読み込み
- セキュアな接続オプション
- エラーハンドリング
- 接続テスト用メソッド

**使用例**：

```php
// 使用例
$db = Database::getInstance();
$pdo = $db->getConnection();

$stmt = $pdo->query("SELECT DATABASE()");
$dbname = $stmt->fetchColumn();
echo "接続中のデータベース: {$dbname}";
```

**ヒント**：

```php
class Database {
    private static $instance = null;
    private $pdo;

    // コンストラクタをprivateに（外部からインスタンス化を禁止）
    private function __construct() {
        $dsn = 'mysql:host=localhost;port=8889;dbname=phase5_practice;charset=utf8mb4';
        $username = 'root';
        $password = 'root';

        try {
            $this->pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            die("データベース接続エラー: " . $e->getMessage());
        }
    }

    // クローンを禁止
    private function __clone() {}

    // インスタンス取得
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // PDO接続を取得
    public function getConnection() {
        return $this->pdo;
    }

    // 接続テスト
    public function testConnection() {
        try {
            $stmt = $this->pdo->query("SELECT 1");
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
```

**追加タスク**：

1. `config.ini`ファイルから接続情報を読み込む機能を追加
2. 複数のデータベース接続をサポート
3. 接続プーリングを実装

---

## 🤖 バイブコーディングのヒント

### AIへの良い指示例

```text
✅ 良い指示：

「MAMP環境でMySQLに接続するPHPコードを書いてください。

要件：
1. PDOを使用（mysqliではなく）
2. ホスト: localhost、ポート: 8889、データベース名: phase5_practice
3. ユーザー名: root、パスワード: root
4. 文字コード: utf8mb4
5. 接続オプション:
   - エラーモード: ERRMODE_EXCEPTION
   - デフォルトフェッチモード: FETCH_ASSOC
   - エミュレートプリペア: 無効化
6. try-catchでエラーハンドリング
7. 接続成功時に「接続成功！」と表示
8. エラー時は詳細なエラーメッセージを表示

セキュリティを最優先してください。」
```

```text
❌ 悪い指示（問題あり）：

「データベースに接続するコードを書いて」

問題点：
- 接続方法が不明（mysqli? PDO?）
- 接続情報が不明
- セキュリティ要件がない
- エラーハンドリングの指示がない
```

### チェックポイント

✅ **接続設定チェック**
- [ ] PDOを使っている（mysqliではない）
- [ ] DSNに文字コード（`charset=utf8mb4`）を指定している
- [ ] ポート番号が正しい（MAMP: 8889）

✅ **接続オプションチェック**
- [ ] `ATTR_ERRMODE => ERRMODE_EXCEPTION`が設定されている
- [ ] `ATTR_DEFAULT_FETCH_MODE => FETCH_ASSOC`が設定されている
- [ ] `ATTR_EMULATE_PREPARES => false`が設定されている

✅ **セキュリティチェック**
- [ ] try-catchでエラーハンドリングしている
- [ ] 本番環境では詳細なエラーメッセージを表示していない
- [ ] パスワードがハードコードされていない（設定ファイル化）

✅ **コード品質チェック**
- [ ] 接続情報が設定ファイルに分離されている
- [ ] 変数名が分かりやすい
- [ ] コメントが適切に付けられている

---

## 💡 よくある問題

### 問題：「SQLSTATE[HY000] [2002] Connection refused」

**原因**：MySQLサーバーが起動していない

**解決**：
1. MAMPを起動
2. 「Start Servers」をクリック
3. MySQLサーバーが起動していることを確認

---

### 問題：「SQLSTATE[HY000] [1049] Unknown database 'phase5_practice'」

**原因**：指定したデータベースが存在しない

**解決**：
1. phpMyAdminにアクセス
2. 新しいデータベース「phase5_practice」を作成
3. 文字コード「utf8mb4_unicode_ci」を選択

---

### 問題：「SQLSTATE[HY000] [2002] No such file or directory」

**原因**：ポート番号が間違っている、またはソケットファイルが見つからない

**解決**：

```php
// MAMPの場合はポート8889を明示的に指定
$dsn = "mysql:host=localhost;port=8889;dbname=phase5_practice;charset=utf8mb4";
```

---

### 問題：文字化けが発生する

**原因**：文字コードの設定が不足

**解決**：

```php
// DSNにcharset=utf8mb4を追加
$dsn = "mysql:host=localhost;port=8889;dbname=phase5_practice;charset=utf8mb4";
```

---

## 📚 参考資料

### PDOの公式ドキュメント

- [PHP: PDO - Manual](https://www.php.net/manual/ja/book.pdo.php)
- [PHP: PDO::__construct - Manual](https://www.php.net/manual/ja/pdo.construct.php)

### 接続オプションの詳細

- `PDO::ATTR_ERRMODE`: エラー報告の方法
  - `PDO::ERRMODE_SILENT`: エラーを無視（デフォルト、非推奨）
  - `PDO::ERRMODE_WARNING`: 警告を発生
  - `PDO::ERRMODE_EXCEPTION`: 例外を投げる（推奨）

- `PDO::ATTR_DEFAULT_FETCH_MODE`: フェッチモード
  - `PDO::FETCH_ASSOC`: 連想配列
  - `PDO::FETCH_NUM`: 数値配列
  - `PDO::FETCH_BOTH`: 両方（デフォルト）

- `PDO::ATTR_EMULATE_PREPARES`: プリペアドステートメントのエミュレート
  - `false`: 真のプリペアドステートメント（推奨）
  - `true`: エミュレート（デフォルト）

---

👉 **[解答例を見る](solutions/README.md)**

**Let's vibe and code! 🎉**

データベース接続をマスターして、セキュアなバックエンド開発の第一歩を踏み出そう！
