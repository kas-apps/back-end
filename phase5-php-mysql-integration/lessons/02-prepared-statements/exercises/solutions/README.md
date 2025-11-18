# 解答例 - プリペアドステートメント

## 基本的なSELECT
```php
<?php
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
?>
```
