# Lesson 04: エラーハンドリング ⚠️

**学習目標**：エラーの種類を理解し、適切なエラーハンドリングで堅牢なコードを書けるようになる！

---

## 📖 このレッスンで学ぶこと

- PHPのエラーの種類（Parse error、Fatal error、Warning、Notice）
- try-catchによる例外処理
- カスタム例外クラス
- エラーログの活用（error_log）
- **本番環境でのエラー表示制御**（セキュリティ重要！） 🔒
- デバッグ方法（var_dump、print_r）

---

## 🎯 なぜエラーハンドリングを学ぶの？（Why）

### エラーは「火災報知器」みたいなもの！

**火災報知器の役割**：

- 火事を早期に検知
- 被害を最小限に抑える
- 適切な対処ができる

**エラーハンドリングの役割**：

- 問題を早期に検知
- システムのクラッシュを防ぐ
- ユーザーに適切なメッセージを表示
- 開発者がデバッグしやすくする

---

## 💻 エラーの種類（What）

### 1. Parse Error（構文エラー）

```php
<?php
// 🚨 Parse Error: セミコロン忘れ
echo "Hello World"  // セミコロンがない
?>
```

**特徴**：

- コードが実行される前に検出される
- 致命的（Fatal）
- すぐに修正が必要

### 2. Fatal Error（致命的エラー）

```php
<?php
// 🚨 Fatal Error: 存在しない関数を呼ぶ
nonExistentFunction();
?>
```

**特徴**：

- 実行時に発生
- スクリプトが停止する

### 3. Warning（警告）

```php
<?php
// ⚠️ Warning: 存在しないファイルを読む
file_get_contents("nonexistent.txt");
// ここは実行される
echo "Warning後も実行される";
?>
```

**特徴**：

- 警告だが、スクリプトは継続
- 無視すると問題になる可能性

### 4. Notice（通知）

```php
<?php
// ℹ️ Notice: 未定義の変数
echo $undefinedVariable;
?>
```

**特徴**：

- 軽度の警告
- スクリプトは継続
- 本番環境では非表示推奨

---

## 🛡️ try-catch による例外処理

### 基本構文

```php
<?php
try {
    // エラーが起きる可能性のあるコード
    $file = file_get_contents("data/sample.txt");

    if ($file === false) {
        throw new Exception("ファイルの読み込みに失敗しました");
    }

    echo $file;

} catch (Exception $e) {
    // エラーが起きた場合の処理
    echo "エラー：" . htmlspecialchars($e->getMessage());
}
?>
```

### 実用例：ファイル操作

```php
<?php
function readFile($filename) {
    if (!file_exists($filename)) {
        throw new Exception("ファイルが存在しません: " . $filename);
    }

    if (!is_readable($filename)) {
        throw new Exception("ファイルが読み込めません: " . $filename);
    }

    $content = file_get_contents($filename);

    if ($content === false) {
        throw new Exception("ファイルの読み込みに失敗しました");
    }

    return $content;
}

// 使用例
try {
    $content = readFile("data/sample.txt");
    echo nl2br(htmlspecialchars($content));
} catch (Exception $e) {
    echo "エラー：" . htmlspecialchars($e->getMessage());
    error_log($e->getMessage());  // ログに記録
}
?>
```

### 複数のcatch

```php
<?php
try {
    // 処理

} catch (InvalidArgumentException $e) {
    echo "引数エラー：" . $e->getMessage();

} catch (RuntimeException $e) {
    echo "実行時エラー：" . $e->getMessage();

} catch (Exception $e) {
    echo "その他のエラー：" . $e->getMessage();
}
?>
```

---

## 🔒 本番環境でのエラー表示制御（セキュリティ超重要！）

### 開発環境 vs 本番環境

**開発環境**：

```php
<?php
// エラーを全部表示（デバッグ用）
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
```

**本番環境**：

```php
<?php
// エラーを表示しない（セキュリティ）
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/error.log');
error_reporting(E_ALL);
?>
```

**なぜ本番環境でエラーを表示しないの？**

- 🚨 **情報漏洩**：エラーメッセージにファイルパスやDB接続情報が含まれる
- 🚨 **攻撃のヒント**：攻撃者に脆弱性の情報を与える

**正しい方法**：

```php
<?php
try {
    // 処理
} catch (Exception $e) {
    // ユーザーには一般的なメッセージ
    echo "申し訳ございません。エラーが発生しました。";

    // 詳細はログに記録
    error_log("エラー: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
}
?>
```

---

## 📝 エラーログの活用

### error_log() の使い方

```php
<?php
// シンプルなログ
error_log("エラーが発生しました");

// 詳細なログ
error_log("[" . date('Y-m-d H:i:s') . "] エラー: ファイルが見つかりません");

// ファイルに出力
error_log("エラーメッセージ", 3, "/path/to/custom.log");
?>
```

### 実用例：カスタムエラーハンドラ

```php
<?php
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    $message = "[" . date('Y-m-d H:i:s') . "] ";
    $message .= "Error [$errno]: $errstr in $errfile on line $errline";

    error_log($message, 3, "error.log");

    // ユーザーには一般的なメッセージ
    echo "エラーが発生しました。管理者にお問い合わせください。";

    return true;  // PHPの標準エラーハンドラを無効化
}

// カスタムエラーハンドラを設定
set_error_handler("customErrorHandler");
?>
```

---

## 🤖 バイブコーディング実践

### AIへの指示例

**良い指示**：

```text
「ファイル読み込み処理にtry-catchを使ってエラーハンドリングを実装してください。
ファイルが存在しない場合は例外をスローし、catchブロックでユーザーにわかりやすいエラーメッセージを表示してください。
また、エラーログにも詳細を記録してください。」
```

### セキュリティチェック

- [ ] **本番環境でエラーを表示しない**
- [ ] **詳細なエラーはログに記録**
- [ ] **ユーザーには一般的なメッセージ**
- [ ] **try-catchで適切にエラーをキャッチ**

---

## 💪 演習

👉 **[演習問題を見る](exercises/README.md)**

---

## ✅ まとめ

- ✅ try-catchで例外をキャッチ
- ✅ 本番環境ではエラーを表示しない
- ✅ error_log()でログに記録
- ✅ ユーザーには一般的なメッセージを表示

---

## 🚀 次のステップ

👉 **[Lesson 05: OOP基礎へ進む](../05-oop-basics/README.md)**

---

**Let's vibe and code! 🎉**
