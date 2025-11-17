# Lesson 04: エラーハンドリング - 解答例 ✅

## 演習 04-01: try-catchの基本

### ポイント

```php
try {
    if (!file_exists($filename)) {
        throw new Exception("ファイルが存在しません");
    }
    $content = file_get_contents($filename);
} catch (Exception $e) {
    echo "エラー：" . htmlspecialchars($e->getMessage());
    error_log($e->getMessage());
}
```

---

## 演習 04-02: カスタムエラーハンドラ

### ポイント

```php
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    $message = "[$errno] $errstr in $errfile:$errline";
    error_log($message, 3, "error.log");
    return true;
}
set_error_handler("customErrorHandler");
```

---

## 演習 04-03: 情報漏洩の修正

### 修正後

```php
ini_set('display_errors', 0);
ini_set('log_errors', 1);

try {
    $file = file_get_contents("/path/to/secret.txt");
} catch (Exception $e) {
    echo "申し訳ございません。エラーが発生しました。";
    error_log($e->getMessage());
}
```

---

**Let's vibe and code! 🎉**
