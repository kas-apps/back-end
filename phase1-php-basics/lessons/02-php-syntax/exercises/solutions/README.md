# Lesson 02: 演習問題の解答例と解説 📚

各問題の解答例とポイントを解説するよ！

---

## 基礎編の解答

### 問題1：自己紹介変数

**解答例**：[02-01-variables.php](02-01-variables.php)

```php
<?php
// 個人情報を変数に入れる
$name = "山田太郎";
$age = 25;
$hometown = "東京都";

// 自己紹介文を表示
echo "私の名前は" . $name . "です。<br>";
echo "年齢は" . $age . "歳で、" . $hometown . "出身です。";
?>
```

**ポイント**：
✨ 変数名がわかりやすい（`$name`、`$age`、`$hometown`）
✨ 文字列連結（`.`）を使っている
✨ `<br>` で改行

---

### 問題2：計算機

**解答例**：[02-02-operators.php](02-02-operators.php)

```php
<?php
// 2つの数値
$num1 = 10;
$num2 = 3;

// 結果を表示
echo "数値1：" . $num1 . "<br>";
echo "数値2：" . $num2 . "<br>";
echo "足し算：" . ($num1 + $num2) . "<br>";
echo "引き算：" . ($num1 - $num2) . "<br>";
echo "掛け算：" . ($num1 * $num2) . "<br>";
echo "割り算：" . ($num1 / $num2) . "<br>";
echo "余り：" . ($num1 % $num2) . "<br>";
?>
```

**ポイント**：
✨ 算術演算子をすべて使用
✨ 変数を使って、値を変更しやすい

---

### 問題5：挨拶関数

**解答例**：[02-05-functions.php](02-05-functions.php)

```php
<?php
// 挨拶を表示する関数
function greet($name) {
    echo "こんにちは、" . $name . "さん！<br>";
}

// 関数を呼び出す
greet("太郎");
greet("花子");
?>
```

---

## 応用編の解答

### 問題7：成績評価システム

**解答例**：[02-07-grade-system.php](02-07-grade-system.php)

```php
<?php
// 点数から評価を返す関数
function getGrade($score) {
    if ($score >= 90) {
        return "A";
    } elseif ($score >= 80) {
        return "B";
    } elseif ($score >= 70) {
        return "C";
    } elseif ($score >= 60) {
        return "D";
    } else {
        return "F";
    }
}

// 評価に応じたコメントを返す関数
function getComment($grade) {
    switch ($grade) {
        case "A":
            return "素晴らしい！";
        case "B":
            return "良いですね！";
        case "C":
            return "合格です";
        case "D":
            return "ギリギリ合格";
        default:
            return "残念、不合格です";
    }
}

// テスト
$score = 85;
$grade = getGrade($score);
$comment = getComment($grade);

echo "点数：" . $score . "点<br>";
echo "評価：" . $grade . "<br>";
echo "コメント：" . $comment;
?>
```

**ポイント**：
✨ 関数を2つに分けて、役割を明確にしている
✨ `return` で結果を返している
✨ `if ... elseif` と `switch` の両方を使っている

---

## 総合チャレンジの解答

### 問題11：FizzBuzz

**解答例**：[02-11-fizzbuzz.php](02-11-fizzbuzz.php)

```php
<?php
// FizzBuzz
for ($i = 1; $i <= 30; $i++) {
    if ($i % 15 == 0) {
        echo "FizzBuzz ";
    } elseif ($i % 3 == 0) {
        echo "Fizz ";
    } elseif ($i % 5 == 0) {
        echo "Buzz ";
    } else {
        echo $i . " ";
    }
}
?>
```

**ポイント**：
✨ 15の倍数を最初にチェック（順番重要！）
✨ `%`（余り）演算子を活用

---

**Let's vibe and code! 🎉**
