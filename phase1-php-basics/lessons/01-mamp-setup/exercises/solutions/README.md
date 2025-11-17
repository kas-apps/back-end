# Lesson 01: 演習問題の解答例と解説 📚

各問題の解答例とポイントを解説するよ！

---

## 基礎編の解答

### 問題1：自分の名前を表示しよう

**解答例**：[01-01-myname.php](01-01-myname.php)

```php
<?php
// 自分の名前を表示するプログラム
echo "私の名前は山田太郎です。";
?>
```

**解説**：

- `<?php` と `?>` でPHPコードを囲む
- `echo` で文字列を出力
- 文字列はダブルクォート `"` またはシングルクォート `'` で囲む
- セミコロン `;` で命令を終了

**ポイント**：
✨ **コメント**：`//` で一行コメントを書ける
✨ **文字列**：日本語も問題なく使える
✨ **シンプル**：最小限のコードで動く

**別の書き方**：

```php
<?php
// 変数を使った書き方
$name = "山田太郎";
echo "私の名前は" . $name . "です。";
?>
```

文字列連結（`.`）を使って、変数と文字列をつなげることもできる！

---

### 問題2：現在の日時を表示しよう

**解答例**：[01-02-today.php](01-02-today.php)

```php
<?php
// 現在の日時を表示するプログラム

// 今日の日付を取得
$today = date('Y年m月d日');

// 現在の時刻を取得
$currentTime = date('H:i:s');

// 表示
echo "今日は" . $today . "です。<br>";
echo "現在の時刻は" . $currentTime . "です。";
?>
```

**解説**：

- `date()` 関数で現在の日時を取得
- 'Y年m月d日' は日付のフォーマット指定
- 'H:i:s' は時刻のフォーマット指定
- `<br>` で改行（HTMLタグ）

**date()関数のフォーマット**：

| フォーマット | 意味 | 例 |
|------------|------|-----|
| Y | 4桁の年 | 2025 |
| m | 2桁の月 | 11 |
| d | 2桁の日 | 17 |
| H | 24時間形式の時 | 14 |
| i | 分 | 30 |
| s | 秒 | 00 |

**ポイント**：
✨ **変数の活用**：日付と時刻を変数に入れて、見やすくしている
✨ **HTML混在**：PHPとHTMLを一緒に使える
✨ **わかりやすい変数名**：`$today`、`$currentTime` など、意味がわかる名前

---

### 問題3：PHPのバージョンを確認しよう

**解答例**：[01-03-version.php](01-03-version.php)

```php
<?php
// PHPのバージョンを表示するプログラム

// PHPバージョンを取得
$phpVersion = phpversion();

// 表示
echo "PHPのバージョン：" . $phpVersion;
?>
```

**解説**：

- `phpversion()` 関数でPHPのバージョンを取得
- 文字列連結（`.`）で "PHPのバージョン：" とバージョン番号をつなげる

**ポイント**：
✨ **組み込み関数**：PHPには便利な関数がたくさんある
✨ **デバッグに便利**：環境情報を確認するときに使える

**別の書き方**：

```php
<?php
// より詳細な情報を表示
echo "PHPのバージョン：" . PHP_VERSION;
echo "<br>";
echo "OSの種類：" . PHP_OS;
?>
```

`PHP_VERSION` という定数（あらかじめ定義されている値）も使える！

---

## 応用編の解答

### 問題4：複数の情報を表示しよう

**解答例**：[01-04-info.php](01-04-info.php)

```php
<?php
// システム情報を表示するプログラム

// 各種情報を取得
$name = "山田太郎";
$today = date('Y年m月d日');
$phpVersion = phpversion();
$serverInfo = $_SERVER['SERVER_SOFTWARE'];

// 表示（HTMLで見やすく）
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>システム情報</title>
</head>
<body>
    <h1>=== システム情報 ===</h1>
    <p>名前：<?php echo $name; ?></p>
    <p>日付：<?php echo $today; ?></p>
    <p>PHPバージョン：<?php echo $phpVersion; ?></p>
    <p>Webサーバー：<?php echo $serverInfo; ?></p>
</body>
</html>
```

**解説**：

- `$_SERVER['SERVER_SOFTWARE']` でWebサーバー情報を取得
- HTMLとPHPを混在させる書き方（`<?php echo ... ?>`）
- HTMLの構造を使って見やすくする

**ポイント**：
✨ **PHPとHTMLの混在**：PHPコードをHTMLの中に埋め込める
✨ **$_SERVER**：サーバー情報を持つ特別な変数（スーパーグローバル変数）
✨ **見やすさ**：HTMLタグで構造化

**$_SERVERの他の情報**：

```php
$_SERVER['SERVER_NAME']     // サーバー名
$_SERVER['REQUEST_METHOD']  // リクエストメソッド（GET/POST）
$_SERVER['REMOTE_ADDR']     // アクセス元のIPアドレス
```

---

### 問題5：エラーを修正しよう

**間違ったコード**：

```php
<?php
echo "こんにちは"  // ← セミコロンがない！
echo "世界！";
```

**修正後のコード**：[01-05-fixed.php](01-05-fixed.php)

```php
<?php
// 修正：セミコロンを追加
echo "こんにちは";  // ← セミコロンを追加！
echo "世界！";
?>
```

**解説**：

- 1行目の `echo "こんにちは"` の後にセミコロン `;` がない
- PHPでは、各命令の最後にセミコロンが必須
- セミコロンがないと、次の行と混ざってしまい、Parse Errorになる

**エラーメッセージ**：

```text
Parse error: syntax error, unexpected 'echo' (T_ECHO), expecting ',' or ';'
```

**読み方**：

- `Parse error`：構文エラー
- `unexpected 'echo'`：echoが予期しない場所にある
- `expecting ','  or ';'`：カンマかセミコロンが必要

**ポイント**：
✨ **セミコロンは必須**：すべての命令の最後に必要
✨ **エラーメッセージを読む**：何が足りないか教えてくれる
✨ **AIに聞く**：エラーメッセージをAIに見せれば、原因と修正方法を教えてくれる

**よくあるエラーのパターン**：

1. セミコロンを忘れた
2. クォートを閉じ忘れた（`echo "こんにちは;` ← `"` がない）
3. 括弧の対応が間違っている（`{` と `}` の数が合わない）

---

## チャレンジの解答

### 問題6：自己紹介ページを作ろう

**解答例**：[01-06-profile.php](01-06-profile.php)

```php
<?php
// 自己紹介ページ

// 個人情報を変数に入れる
$name = "山田太郎";
$age = 25;
$hobbies = ["プログラミング", "音楽", "旅行"];
$currentDateTime = date('Y年m月d日 H:i:s');
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>自己紹介</title>
    <style>
        body {
            font-family: 'Hiragino Sans', 'メイリオ', sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f0f8ff;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
        }
        .info {
            margin: 20px 0;
        }
        .label {
            font-weight: bold;
            color: #34495e;
        }
        ul {
            list-style-type: none;
            padding-left: 20px;
        }
        ul li:before {
            content: "・";
            color: #3498db;
            font-weight: bold;
            margin-right: 5px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ecf0f1;
            color: #7f8c8d;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>自己紹介</h1>

        <div class="info">
            <p><span class="label">名前：</span><?php echo $name; ?></p>
            <p><span class="label">年齢：</span><?php echo $age; ?>歳</p>
        </div>

        <div class="info">
            <p class="label">好きなもの：</p>
            <ul>
                <?php foreach ($hobbies as $hobby): ?>
                    <li><?php echo $hobby; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="footer">
            <p>今日は<?php echo $currentDateTime; ?>です。</p>
            <p>私はPHPを学んでいます！</p>
        </div>
    </div>
</body>
</html>
```

**解説**：

- 変数を使って個人情報を管理
- 配列（`$hobbies`）で複数の趣味を保存
- HTMLとCSSで見た目を整える
- `foreach` ループで配列の各要素を表示（Lesson 02で詳しく学ぶ）

**ポイント**：
✨ **変数の活用**：情報を変数に入れて管理しやすく
✨ **HTMLとPHPの組み合わせ**：`<?php echo ... ?>` で値を埋め込む
✨ **CSSでスタイリング**：見た目を美しく
✨ **配列の使用**：複数のデータをまとめて管理（Lesson 03で詳しく学ぶ）

**初心者向けシンプル版**：

```php
<?php
$name = "山田太郎";
$age = 25;
?>
<h1>自己紹介</h1>
<p>名前：<?php echo $name; ?></p>
<p>年齢：<?php echo $age; ?>歳</p>
<p>私はPHPを学んでいます！</p>
```

まずはシンプル版から始めて、徐々に装飾を追加していくのもOK！

---

### 問題7：AIに指示を出してコードを生成しよう

**AIへの指示**：

```text
「MAMPで動く、季節の挨拶を表示するPHPファイルを作ってください。
現在の月によって、以下のメッセージを表示してください：
- 3月〜5月：春ですね！
- 6月〜8月：夏ですね！
- 9月〜11月：秋ですね！
- 12月〜2月：冬ですね！

ファイル名はseason.phpで、日本語でコメントも入れてください。」
```

**AIが生成するコードの例**：[01-07-season.php](01-07-season.php)

```php
<?php
// 季節の挨拶を表示するプログラム

// 現在の月を取得（1〜12）
$currentMonth = date('n');

// 季節を判定
if ($currentMonth >= 3 && $currentMonth <= 5) {
    // 3月〜5月は春
    $season = "春";
} elseif ($currentMonth >= 6 && $currentMonth <= 8) {
    // 6月〜8月は夏
    $season = "夏";
} elseif ($currentMonth >= 9 && $currentMonth <= 11) {
    // 9月〜11月は秋
    $season = "秋";
} else {
    // 12月〜2月は冬
    $season = "冬";
}

// メッセージを表示
echo $season . "ですね！";
?>
```

**解説**：

- `date('n')` で現在の月を数値で取得（1〜12）
- `if ... elseif ... else` で条件分岐（Lesson 02で詳しく学ぶ）
- 月の範囲で季節を判定

**チェックポイント**：
✅ `<?php ?>` でコードが囲まれている
✅ 条件分岐（if/elseif/else）が使われている
✅ 日本語コメントがある
✅ `date('n')` で現在の月が取得されている
✅ セミコロンがすべての命令の最後にある

**ポイント**：
✨ **AIの出力を必ずチェック**：動くだけでなく、理解することが大事
✨ **コメントで理解を深める**：どの部分が何をしているか確認
✨ **条件分岐の理解**：Lesson 02で詳しく学ぶから、今は「こういうことができるんだ」と知っておくだけでOK

**もしAIの生成コードに問題があったら**：

```text
「このコードだと、11月に『秋ですね！』ではなく『冬ですね！』と表示されます。
条件を修正してください。」
```

こんな風に、具体的に問題点を伝えると、AIが修正してくれる！

---

## 💡 学習のまとめ

### この演習で身についたこと

✅ **PHPの基本**

- PHPファイルの作成と実行
- `echo` での出力
- 基本的な関数（`date()`、`phpversion()`）

✅ **環境操作**

- htdocsフォルダへのファイル保存
- localhostでのアクセス

✅ **エラー対処**

- エラーメッセージの読み方
- セミコロンの重要性

✅ **バイブコーディング**

- AIへの具体的な指示の出し方
- 生成されたコードのチェック方法

---

## 🚀 次のステップ

演習お疲れ様！たくさん手を動かして、MAMP環境に慣れてきたね！✨

次は**Lesson 02: PHP基礎文法**で：

- 変数の詳しい使い方
- 条件分岐（if/else）
- ループ（for、while）
- 関数の作り方

を学んでいくよ！

Lesson 01で出てきた `if` や `foreach` の正体がわかるようになる！楽しみにしててね！

---

**Let's vibe and code! 🎉**
