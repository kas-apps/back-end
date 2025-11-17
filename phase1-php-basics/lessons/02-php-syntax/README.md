# Lesson 02: PHP基礎文法 📝

**学習目標**：PHPの基本的な文法（変数、条件分岐、ループ、関数）をマスターして、AIに的確な指示を出せるようになる！

---

## 📖 このレッスンで学ぶこと

- PHPファイルの基本構造（`<?php ?>`タグ）
- 変数とデータ型（文字列、数値、真偽値、null）
- 変数の命名規則
- 演算子（算術、比較、論理、文字列連結）
- 条件分岐（if、else、elseif、switch）
- ループ（for、while、foreach）
- 関数の基本（関数定義、引数、戻り値、スコープ）
- echo/printでの出力
- HTMLとPHPの組み合わせ

---

## 🎯 なぜPHP基礎文法を学ぶの？（Why）

### プログラミングの「共通言語」を手に入れる！

プログラミング言語って、人間の言語と似ているんだ！

**人間の言語**：
- 名詞（モノ）：「りんご」「車」「太郎」
- 動詞（動作）：「食べる」「走る」「話す」
- 接続詞：「もし」「だから」「そして」

**プログラミング言語（PHP）**：
- 変数（データの入れ物）：`$apple`、`$car`、`$name`
- 関数（処理）：`eat()`、`run()`、`speak()`
- 制御構文：`if`、`for`、`while`

この「共通言語」を理解すれば：

✨ **AIへの指示が超具体的になる**
```text
悪い指示：「計算するコードを書いて」
良い指示：「誕生年を変数$birthYearに入れて、現在の年から引き算して年齢を計算して、echoで表示して」
```

✨ **生成されたコードが読める**
```php
// AIが生成したこのコードが読める！
$birthYear = 2000;
$currentYear = date('Y');
$age = $currentYear - $birthYear;
echo "あなたは" . $age . "歳です。";
```

「あ、birthYearに2000を入れて、currentYearに今年を入れて、引き算してるんだな」ってわかるようになる！

### バックエンド開発における重要性

PHPの基礎文法は、**すべてのバックエンド処理の土台**！

- データベースから取得したデータを処理（変数）
- ユーザーの権限をチェック（条件分岐）
- 商品リストを一覧表示（ループ）
- 価格計算（関数）

基礎がしっかりしていれば、Phase 5でデータベースと連携するときもスムーズ！

---

## 🏗️ PHPファイルの基本構造（What）

### PHPタグ

PHPコードは、**PHPタグ**で囲む必要があるよ！

```php
<?php
// ここにPHPコードを書く
?>
```

**`<?php`**：「ここからPHPコードだよ」っていうスタート地点
**`?>`**：「ここでPHPコード終わり」っていう終了地点

### HTMLとの混在

PHPとHTMLは一緒に使える！

```php
<!DOCTYPE html>
<html>
<head>
    <title>サンプル</title>
</head>
<body>
    <h1>ようこそ！</h1>
    <?php
    // ここはPHPコード
    $name = "太郎";
    echo "<p>こんにちは、" . $name . "さん！</p>";
    ?>
    <p>これはHTMLです</p>
</body>
</html>
```

**ポイント**：
- HTMLの中に `<?php ?>` を埋め込める
- PHPの中で `echo` を使ってHTMLを出力できる

### コメントの書き方

コードに「メモ書き」を残せるよ！

```php
<?php
// これは一行コメント（この行は実行されない）

/*
これは
複数行コメント
何行でもOK！
*/

$name = "太郎";  // コードの後ろにもコメントを書ける

?>
```

**コメントの目的**：
- コードの説明を書く
- 自分や他の人が後で見てもわかるようにする
- AIが生成したコードに、自分のメモを追加する

---

## 💾 変数とデータ型

### 変数とは？

**変数**は、「データの入れ物」！

レストランで例えると：
- 「皿」があって、そこに「料理」を載せる
- 「皿」= 変数、「料理」= データ

PHPでは：
- 変数は `$` で始まる（`$name`、`$age` など）
- 変数にデータを「入れる」ことを「代入」という

```php
<?php
$name = "太郎";  // $name という変数に "太郎" を代入
$age = 25;       // $age という変数に 25 を代入
?>
```

### データ型

PHPには、いろんな種類のデータがあるよ！

#### 1. 文字列（String）

文字の並び。クォート `"` または `'` で囲む。

```php
<?php
$name = "山田太郎";
$message = 'こんにちは！';

echo $name;     // 山田太郎
echo $message;  // こんにちは！
?>
```

**ダブルクォート vs シングルクォート**：

```php
<?php
$name = "太郎";

// ダブルクォート：変数が展開される
echo "こんにちは、$name さん";  // こんにちは、太郎さん

// シングルクォート：変数が展開されない
echo 'こんにちは、$name さん';  // こんにちは、$name さん
?>
```

**初心者向けアドバイス**：迷ったらダブルクォート `"` を使おう！

#### 2. 数値（Integer / Float）

**Integer（整数）**：
```php
<?php
$age = 25;
$year = 2025;
$temperature = -5;
?>
```

**Float（浮動小数点数）**：
```php
<?php
$price = 19.99;
$pi = 3.14159;
$rate = 0.05;
?>
```

**ポイント**：
- 数値にはクォートをつけない
- カンマ（`,`）は使わない（`1,000` ではなく `1000`）

#### 3. 真偽値（Boolean）

`true`（真）または `false`（偽）の2つだけ。

```php
<?php
$isLoggedIn = true;   // ログイン済み
$hasError = false;    // エラーなし
?>
```

**使い道**：
- ログイン状態の管理
- エラーチェック
- 条件分岐で使う

#### 4. null

「何もない」「未定義」を表す特別な値。

```php
<?php
$value = null;  // 何も入っていない
?>
```

### 変数の命名規則

**ルール**：
1. `$` で始める（必須）
2. 英数字とアンダースコア `_` が使える
3. 数字で始められない
4. 日本語も使えるけど、英語を推奨

**良い例**：
```php
<?php
$userName = "太郎";
$user_name = "太郎";  // スネークケース
$age = 25;
$totalPrice = 1000;
?>
```

**悪い例**：
```php
<?php
$1name = "太郎";   // NG：数字で始まっている
$user-name = "太郎";  // NG：ハイフンは使えない
$名前 = "太郎";    // OK だけど英語推奨
?>
```

**命名のコツ**：
- わかりやすい名前をつける（`$a` より `$age`）
- 長すぎない、短すぎない
- 一貫性を保つ（`userName` か `user_name` かを統一）

---

## 🔢 演算子

### 算術演算子（計算）

```php
<?php
$a = 10;
$b = 3;

echo $a + $b;  // 13（足し算）
echo $a - $b;  // 7（引き算）
echo $a * $b;  // 30（掛け算）
echo $a / $b;  // 3.333...（割り算）
echo $a % $b;  // 1（余り）
?>
```

**余り（%）の使い道**：
- 偶数・奇数の判定（`$n % 2 == 0` なら偶数）
- 3の倍数の判定（`$n % 3 == 0` なら3の倍数）

### 文字列連結演算子

文字列をつなげるときは `.`（ドット）を使う！

```php
<?php
$firstName = "山田";
$lastName = "太郎";

$fullName = $firstName . $lastName;  // 山田太郎

echo "こんにちは、" . $fullName . "さん！";
// こんにちは、山田太郎さん！
?>
```

### 比較演算子

2つの値を比較するよ！

```php
<?php
$a = 10;
$b = 5;

$a == $b   // false（等しい）
$a != $b   // true（等しくない）
$a > $b    // true（より大きい）
$a < $b    // false（より小さい）
$a >= $b   // true（以上）
$a <= $b   // false（以下）
?>
```

**`==` と `===` の違い**：

```php
<?php
$a = 5;
$b = "5";

$a == $b   // true（値が同じ）
$a === $b  // false（値も型も同じかチェック）
?>
```

**初心者向けアドバイス**：迷ったら `===` を使おう（より厳密）！

### 論理演算子

複数の条件を組み合わせるときに使う！

```php
<?php
$age = 20;
$hasLicense = true;

// AND（かつ）：両方trueならtrue
$canDrive = ($age >= 18) && $hasLicense;  // true

// OR（または）：どちらかtrueならtrue
$isChild = ($age < 13) || ($age > 65);    // false

// NOT（否定）：trueとfalseを反転
$isNotChild = !($age < 13);               // true
?>
```

---

## 🔀 条件分岐（if / else / elseif）

### if文の基本

「もし〜なら、〜する」という処理！

```php
<?php
$age = 20;

if ($age >= 18) {
    echo "あなたは成人です";
}
?>
```

**構文**：
```php
if (条件) {
    // 条件がtrueのときに実行される処理
}
```

### if ... else

「もし〜なら、〜する。そうでなければ、〜する」

```php
<?php
$age = 15;

if ($age >= 18) {
    echo "あなたは成人です";
} else {
    echo "あなたは未成年です";
}
?>
```

### if ... elseif ... else

複数の条件を順番にチェック！

```php
<?php
$score = 85;

if ($score >= 90) {
    echo "A評価：素晴らしい！";
} elseif ($score >= 80) {
    echo "B評価：良いです！";
} elseif ($score >= 70) {
    echo "C評価：合格！";
} elseif ($score >= 60) {
    echo "D評価：ギリギリ合格";
} else {
    echo "F評価：不合格";
}
// 出力：B評価：良いです！
?>
```

**実用例：ログイン判定**

```php
<?php
$isLoggedIn = true;
$userName = "太郎";

if ($isLoggedIn) {
    echo "ようこそ、" . $userName . "さん！";
} else {
    echo "ログインしてください";
}
?>
```

### switch文

値によって処理を分岐するときに便利！

```php
<?php
$day = "月";

switch ($day) {
    case "月":
        echo "今週が始まった！";
        break;
    case "金":
        echo "明日は週末！";
        break;
    case "土":
    case "日":
        echo "休日だ！";
        break;
    default:
        echo "普通の平日";
        break;
}
?>
```

**ポイント**：
- `break;` を忘れずに！（ないと次のcaseも実行される）
- `default:` は、どのcaseにも当てはまらないときの処理

---

## 🔁 ループ（繰り返し処理）

### for文

**決まった回数**だけ繰り返すとき！

```php
<?php
// 1から10まで表示
for ($i = 1; $i <= 10; $i++) {
    echo $i . " ";
}
// 出力：1 2 3 4 5 6 7 8 9 10
?>
```

**構文**：
```php
for (初期化; 条件; 増減) {
    // 繰り返す処理
}
```

**解説**：
- `$i = 1`：最初に$iを1にする
- `$i <= 10`：$iが10以下の間、繰り返す
- `$i++`：ループごとに$iを1増やす

**実用例：九九の表**

```php
<?php
for ($i = 1; $i <= 9; $i++) {
    for ($j = 1; $j <= 9; $j++) {
        echo $i * $j . " ";
    }
    echo "<br>";  // 改行
}
?>
```

### while文

**条件がtrueの間**繰り返す！

```php
<?php
$count = 1;

while ($count <= 5) {
    echo "カウント: " . $count . "<br>";
    $count++;  // 必ず更新しないと無限ループ！
}
// 出力：
// カウント: 1
// カウント: 2
// カウント: 3
// カウント: 4
// カウント: 5
?>
```

**注意**：条件が常にtrueだと**無限ループ**になる！

```php
<?php
// 危険：無限ループ！
while (true) {
    echo "永遠に繰り返す";  // ブラウザがフリーズする
}
?>
```

### foreach文（配列のループ）

配列の各要素に対して処理を行う！（Lesson 03で詳しく学ぶ）

```php
<?php
$colors = ["赤", "青", "緑"];

foreach ($colors as $color) {
    echo $color . " ";
}
// 出力：赤 青 緑
?>
```

---

## 🎪 関数の基本

### 関数とは？

**関数**は、「処理をまとめたもの」！

レストランで例えると：
- 「ハンバーガーセットください」って注文する
- 店員さんが「ハンバーガー作る → ポテト揚げる → ドリンク用意する」を全部やってくれる
- あなたは細かい手順を知らなくてOK

プログラミングでも同じ！
- 関数を「呼び出す」だけで、中の処理が全部実行される

### 関数の定義

```php
<?php
// 関数の定義
function greet() {
    echo "こんにちは！";
}

// 関数の呼び出し
greet();  // こんにちは！
?>
```

**構文**：
```php
function 関数名() {
    // 実行する処理
}
```

### 引数（ひきすう）

関数に「材料」を渡すことができる！

```php
<?php
// 引数を受け取る関数
function greet($name) {
    echo "こんにちは、" . $name . "さん！";
}

// 関数を呼び出すときに引数を渡す
greet("太郎");  // こんにちは、太郎さん！
greet("花子");  // こんにちは、花子さん！
?>
```

**複数の引数**：

```php
<?php
function introduce($name, $age) {
    echo $name . "さんは" . $age . "歳です。";
}

introduce("太郎", 25);  // 太郎さんは25歳です。
?>
```

### 戻り値（return）

関数から「結果」を返すことができる！

```php
<?php
// 年齢を計算して返す関数
function calculateAge($birthYear) {
    $currentYear = date('Y');
    $age = $currentYear - $birthYear;
    return $age;  // 計算結果を返す
}

// 関数の戻り値を変数に入れる
$myAge = calculateAge(2000);
echo "私は" . $myAge . "歳です。";  // 私は25歳です。
?>
```

**実用例：税込価格の計算**

```php
<?php
function calculateTax($price) {
    $taxRate = 0.1;  // 消費税10%
    $totalPrice = $price + ($price * $taxRate);
    return $totalPrice;
}

$price = 1000;
$withTax = calculateTax($price);
echo "税込価格：" . $withTax . "円";  // 税込価格：1100円
?>
```

### スコープ（変数の有効範囲）

関数の中と外では、変数の範囲が違う！

```php
<?php
$globalVar = "グローバル変数";  // 関数の外の変数

function test() {
    $localVar = "ローカル変数";  // 関数の中の変数
    echo $localVar;  // OK：関数の中で使える
    // echo $globalVar;  // NG：関数の外の変数は使えない
}

test();  // ローカル変数

echo $globalVar;  // OK：グローバル変数は関数の外で使える
// echo $localVar;  // NG：関数の中の変数は外で使えない
?>
```

**ポイント**：
- 関数の外の変数は「グローバル変数」
- 関数の中の変数は「ローカル変数」
- ローカル変数は関数の中だけで有効
- 引数と戻り値でデータをやり取りする

---

## 💻 コード例：実践編（How）

### 例1：割引価格の計算

```php
<?php
// 割引価格を計算する関数
function calculateDiscount($price, $discountRate) {
    $discountAmount = $price * $discountRate;
    $finalPrice = $price - $discountAmount;
    return $finalPrice;
}

// 使用例
$originalPrice = 5000;
$discount = 0.2;  // 20%オフ

$salePrice = calculateDiscount($originalPrice, $discount);

echo "元の価格：" . $originalPrice . "円<br>";
echo "割引率：" . ($discount * 100) . "%<br>";
echo "割引後：" . $salePrice . "円<br>";

// 出力：
// 元の価格：5000円
// 割引率：20%
// 割引後：4000円
?>
```

### 例2：成績判定システム

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

// 評価に応じたメッセージを返す関数
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

// 使用例
$score = 85;
$grade = getGrade($score);
$comment = getComment($grade);

echo "点数：" . $score . "点<br>";
echo "評価：" . $grade . "<br>";
echo "コメント：" . $comment . "<br>";

// 出力：
// 点数：85点
// 評価：B
// コメント：良いですね！
?>
```

### 例3：FizzBuzz（プログラミングの定番練習問題）

```php
<?php
// 1から30まで、以下のルールで表示：
// - 3の倍数のとき："Fizz"
// - 5の倍数のとき："Buzz"
// - 15の倍数のとき："FizzBuzz"
// - それ以外：数字そのまま

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

// 出力：1 2 Fizz 4 Buzz Fizz 7 8 Fizz Buzz 11 Fizz 13 14 FizzBuzz 16 ...
?>
```

---

## 🤖 バイブコーディング実践（最重要セクション！）

### AIへの指示例

#### 良い指示の例1：具体的で明確

```text
「誕生年を変数$birthYearに入れて、現在の年（date関数で取得）から引き算して年齢を計算し、
echoで『あなたは○○歳です』と表示するPHPコードを書いてください。
コメントも日本語で入れてください。」
```

**生成されるコードの例**：
```php
<?php
// 誕生年
$birthYear = 2000;

// 現在の年を取得
$currentYear = date('Y');

// 年齢を計算
$age = $currentYear - $birthYear;

// 表示
echo "あなたは" . $age . "歳です";
?>
```

**なぜ良い？**
- ✅ 変数名を指定している
- ✅ 使う関数を指定している（date関数）
- ✅ 出力形式も指定している
- ✅ コメントの言語も指定している

#### 良い指示の例2：関数を使った処理

```text
「価格と税率を引数として受け取り、税込価格を計算して返す関数calculateTaxを作ってください。
価格1000円、税率10%でテストするコードも含めてください。」
```

**生成されるコードの例**：
```php
<?php
// 税込価格を計算する関数
function calculateTax($price, $taxRate) {
    $totalPrice = $price + ($price * $taxRate);
    return $totalPrice;
}

// テスト
$price = 1000;
$taxRate = 0.1;
$result = calculateTax($price, $taxRate);

echo "税込価格：" . $result . "円";
?>
```

#### 曖昧な指示の例（避けるべき）

```text
「年齢を計算して」
```

**なぜダメ？**
- ❌ 何をどう計算するのか不明
- ❌ 入力が何か（誕生年？誕生日？）不明
- ❌ 出力形式が不明

### 生成されたコードのチェックポイント

#### セキュリティチェック

このレッスンの段階では、まだユーザー入力を扱わないので、セキュリティチェックは最小限でOK！

Lesson 04（フォームデータ）で詳しく学ぶよ。

#### 機能チェック

- [ ] 指示した機能が実装されているか
- [ ] 変数名がわかりやすいか
- [ ] 関数名が適切か
- [ ] エッジケース（特殊な場合）を考慮しているか

**例：年齢計算のエッジケース**
```php
// 問題：誕生日がまだ来ていない場合、年齢が1歳多くなる
$birthYear = 2000;
$currentYear = 2025;
$age = $currentYear - $birthYear;  // 25歳（でも誕生日前なら24歳）
```

これは複雑なので、最初は「誕生年から計算」でOK！慣れてきたら改善しよう。

#### コード品質チェック

- [ ] **コメント**：重要な処理に説明があるか
- [ ] **変数名**：わかりやすいか（`$a` より `$age`）
- [ ] **インデント**：読みやすく整形されているか
- [ ] **重複**：同じコードが繰り返されていないか

### よくある問題と修正方法

#### 問題1：変数のスコープミス

**AIが生成しがちなコード**：
```php
<?php
function calculateAge($birthYear) {
    $age = 2025 - $birthYear;
}

echo $age;  // エラー：$ageは関数の中でしか使えない
?>
```

**原因**：関数の中の変数（ローカル変数）を外で使おうとしている

**修正**：
```php
<?php
function calculateAge($birthYear) {
    $age = 2025 - $birthYear;
    return $age;  // 戻り値で返す
}

$myAge = calculateAge(2000);  // 戻り値を変数に入れる
echo $myAge;  // これで使える
?>
```

**AIへの修正指示**：
```text
「関数calculateAgeは、計算結果をreturnで返すようにしてください。
そして、呼び出し側で戻り値を変数に入れて使うようにしてください。」
```

#### 問題2：データ型の混在

**AIが生成しがちなコード**：
```php
<?php
$age = "25";  // 文字列
$nextAge = $age + 1;
echo $nextAge;  // 26（動くけど、型が曖昧）
?>
```

**問題点**：
- $ageが文字列として定義されている
- PHPは自動的に型変換してくれるけど、意図しない動作の原因になる

**修正**：
```php
<?php
$age = 25;  // 数値
$nextAge = $age + 1;
echo $nextAge;  // 26
?>
```

**AIへの修正指示**：
```text
「変数$ageは数値型（クォートなし）で定義してください」
```

#### 問題3：無限ループ

**AIが生成しがちなコード**：
```php
<?php
$count = 1;
while ($count <= 10) {
    echo $count;
    // $count++; を忘れている！
}
?>
```

**原因**：ループ内で変数を更新していない

**修正**：
```php
<?php
$count = 1;
while ($count <= 10) {
    echo $count;
    $count++;  // これを忘れずに！
}
?>
```

**AIへの修正指示**：
```text
「whileループの中で、必ず$countをインクリメント（$count++）してください。
でないと無限ループになります。」
```

### カスタマイズポイント

AIが生成したコードを、自分好みにカスタマイズしよう！

#### 変数名の変更

```php
// AIが生成したコード
$a = 100;
$b = 20;
$c = $a - $b;

// 自分でわかりやすく変更
$price = 100;
$discount = 20;
$finalPrice = $price - $discount;
```

#### コメントの追加

```php
// AIが生成したコード
function calc($p, $r) {
    return $p * $r;
}

// 自分でコメントを追加
// 価格と税率から税込価格を計算する関数
function calc($price, $taxRate) {
    // 価格 × 税率 で税額を計算し、価格に加算
    return $price * (1 + $taxRate);
}
```

#### 出力形式の改善

```php
// AIが生成したコード
echo $age;

// HTMLで見やすく
echo "<p>あなたの年齢：<strong>" . $age . "</strong>歳</p>";
```

---

## 💪 演習

実際に手を動かして、PHP基礎文法をマスターしよう！

演習問題はこちら：
👉 **[演習問題を見る](exercises/README.md)**

---

## ✅ まとめ

このレッスンで学んだことを振り返ろう！

### PHPの基本構造

- ✅ `<?php ?>` でPHPコードを囲む
- ✅ HTMLとPHPを混在できる
- ✅ コメント（`//` と `/* */`）でメモを書ける

### 変数とデータ型

- ✅ 変数は `$` で始まる
- ✅ データ型：文字列、数値、真偽値、null
- ✅ わかりやすい変数名をつける

### 演算子

- ✅ 算術演算子（`+`、`-`、`*`、`/`、`%`）
- ✅ 文字列連結（`.`）
- ✅ 比較演算子（`==`、`===`、`!=`、`>`、`<`）
- ✅ 論理演算子（`&&`、`||`、`!`）

### 条件分岐

- ✅ `if`、`else`、`elseif` で条件分岐
- ✅ `switch` で値による分岐

### ループ

- ✅ `for` で決まった回数繰り返す
- ✅ `while` で条件がtrueの間繰り返す
- ✅ `foreach` で配列をループ（Lesson 03で詳しく）

### 関数

- ✅ `function` で関数を定義
- ✅ 引数で値を渡す
- ✅ `return` で結果を返す
- ✅ スコープ（変数の有効範囲）を理解

### バイブコーディング

- ✅ AIに具体的な指示を出せる
- ✅ 生成されたコードをチェックできる
- ✅ よくある問題を修正できる
- ✅ コードをカスタマイズできる

---

## 🚀 次のステップ

PHP基礎文法をマスターしたね！すごい！✨

次は**Lesson 03: 配列操作**で：
- 配列の基本（複数のデータをまとめて管理）
- 連想配列（キーと値のペア）
- 配列操作関数
- foreachループの詳細

を学んでいくよ！

Lesson 02で学んだ変数やループの知識を使って、もっと実践的なデータ管理ができるようになる！

👉 **[Lesson 03: 配列操作へ進む](../03-arrays/README.md)**

---

**Let's vibe and code! 🎉**

PHP文法の基礎が身についた！次は配列でデータを自在に操ろう！
