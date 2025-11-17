# Lesson 04: データ型とテーブル設計 🔢

**学習目標**：MySQLの主要なデータ型を理解し、適切なデータ型を選択してテーブルを設計できるようになる！

---

## 📖 このレッスンで学ぶこと

- データ型とは何か
- **数値型**：INT、DECIMAL、FLOAT
- **文字列型**：VARCHAR、TEXT、CHAR
- **日付・時刻型**：DATE、DATETIME、TIMESTAMP
- **真偽値型**：BOOLEAN（TINYINT）
- **適切なデータ型の選択**：パフォーマンスとデータ整合性
- **データ型の落とし穴**：よくある間違いと対策

---

## 🎯 なぜデータ型を学ぶの？（Why）

### データ型は「入れ物のサイズ」

**アナロジー：引越しの荷造り**

想像してみて：引越しで荷物を箱に詰める時...

- **小さい箱（INT）**：本や食器を入れる
- **中くらいの箱（VARCHAR）**：服や靴を入れる
- **大きい箱（TEXT）**：布団や毛布を入れる
- **専用ケース（DATE）**：ワインや美術品を入れる

**間違った箱を選ぶと...**

- ❌ 大きすぎる箱：無駄なスペース、運ぶのが大変
- ❌ 小さすぎる箱：入らない、壊れる

**データ型も同じ！**

- ✅ **適切なサイズ**：無駄なメモリを使わない、高速
- ❌ **大きすぎる**：メモリとディスクスペースの無駄
- ❌ **小さすぎる**：データが入らない、エラーが発生

### バックエンド開発における重要性

データ型の選択は、**パフォーマンスとデータ整合性**に直結する！

- 📊 **メモリ効率**：適切なサイズで無駄を削減
- 🚀 **パフォーマンス**：検索やソートが高速化
- 🔒 **データ整合性**：不正なデータの挿入を防ぐ
- 💾 **ストレージ節約**：大規模データベースで重要

**例**：

```text
悪い例：年齢をVARCHAR(255)で保存
→ 255文字も必要ない！メモリの無駄、数値として扱えない

良い例：年齢をTINYINTで保存
→ 0-255の範囲で十分、メモリ効率良い、数値計算できる
```

---

## 🔢 数値型（Numeric Types）

### INT（整数型）

**用途**：整数（0, 1, 2, 3, ..., -1, -2, -3, ...）

**種類**：

| 型        | バイト数 | 範囲（符号付き）                  | 範囲（符号なし）        | 用途例                  |
| --------- | -------- | --------------------------------- | ----------------------- | ----------------------- |
| TINYINT   | 1        | -128 〜 127                       | 0 〜 255                | 年齢、フラグ            |
| SMALLINT  | 2        | -32,768 〜 32,767                 | 0 〜 65,535             | 小さい数値              |
| MEDIUMINT | 3        | -8,388,608 〜 8,388,607           | 0 〜 16,777,215         | 中程度の数値            |
| INT       | 4        | -2,147,483,648 〜 2,147,483,647   | 0 〜 4,294,967,295      | 一般的な整数（推奨）    |
| BIGINT    | 8        | とても大きい                      | とても大きい            | 超大きい数値            |

**例**：

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,  -- ユーザーID
    age TINYINT UNSIGNED,  -- 年齢（0-255）
    points INT UNSIGNED DEFAULT 0,  -- ポイント
    total_sales BIGINT UNSIGNED DEFAULT 0  -- 総売上（大きい数値）
);
```

**ポイント**：

- `UNSIGNED`：符号なし（0以上の正の数だけ）
- 年齢、個数など、負の数が不要な場合は `UNSIGNED` を付ける
- 一般的には `INT` が推奨（汎用性が高い）

### DECIMAL（固定小数点型）

**用途**：正確な小数（金額、パーセンテージなど）

**構文**：`DECIMAL(全体の桁数, 小数点以下の桁数)`

**例**：

```sql
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    price DECIMAL(10, 2),  -- 価格（例：12345678.90）
    tax_rate DECIMAL(5, 3),  -- 税率（例：0.100 = 10%）
    discount DECIMAL(5, 2)  -- 割引率（例：15.50%）
);
```

**DECIMAL(10, 2) の意味**：

- 全体で10桁
- 小数点以下は2桁
- 例：`12345678.90`（整数部分8桁 + 小数部分2桁）

**なぜDECIMALを使う？**

```sql
-- 悪い例：FLOATで金額を保存
-- FLOATは近似値なので、計算誤差が発生する可能性がある
-- 例：100.00 + 200.00 = 299.999999... のような誤差

-- 良い例：DECIMALで金額を保存
-- DECIMALは正確な値を保存するので、誤差がない
-- 例：100.00 + 200.00 = 300.00（正確）
```

**教訓**：**金額は必ずDECIMAL！**

### FLOAT / DOUBLE（浮動小数点型）

**用途**：近似値でOKな小数（科学計算、座標など）

**例**：

```sql
CREATE TABLE locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    latitude DOUBLE,  -- 緯度（例：35.6895）
    longitude DOUBLE,  -- 経度（例：139.6917）
    temperature FLOAT  -- 温度（例：23.5℃）
);
```

**FLOATとDOUBLEの違い**：

| 型     | バイト数 | 精度     | 用途                    |
| ------ | -------- | -------- | ----------------------- |
| FLOAT  | 4        | 低い     | 大まかな小数            |
| DOUBLE | 8        | 高い     | より正確な小数          |

**⚠️ 注意**：

- 浮動小数点型は近似値を保存する
- 金額など、正確な値が必要な場合は使わない
- DECIMALを使うべき

---

## 📝 文字列型（String Types）

### VARCHAR（可変長文字列）

**用途**：短〜中程度の文字列（名前、メールアドレス、URL など）

**構文**：`VARCHAR(最大文字数)`

**例**：

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),  -- 名前（最大100文字）
    email VARCHAR(255),  -- メールアドレス（最大255文字）
    url VARCHAR(500)  -- URL（最大500文字）
);
```

**特徴**：

- **可変長**：実際のデータ長に応じてメモリを使う
  - 例：`VARCHAR(100)` で「太郎」を保存 → 2文字分だけ使用
- **最大65,535バイトまで**：ただし、一般的には数百文字までの文字列に使用し、長文には`TEXT`型を検討します。
- **インデックスが貼れる**：検索が高速

**使い分け**：

- 名前：`VARCHAR(100)`
- メールアドレス：`VARCHAR(255)`
- URL：`VARCHAR(500)` または `TEXT`

### TEXT（長い文字列）

**用途**：長い文字列（記事本文、コメント、説明文など）

**種類**：

| 型         | 最大サイズ      | 用途                    |
| ---------- | --------------- | ----------------------- |
| TINYTEXT   | 255バイト       | 短い文章                |
| TEXT       | 65,535バイト    | 一般的な長文（推奨）    |
| MEDIUMTEXT | 16MB            | 超長文                  |
| LONGTEXT   | 4GB             | 超超長文（滅多に使わない）|

**例**：

```sql
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200),  -- タイトル（短い）
    content TEXT,  -- 本文（長い）
    description TEXT  -- 説明文（長い）
);
```

**VARCHARとTEXTの違い**：

| 特徴           | VARCHAR           | TEXT              |
| -------------- | ----------------- | ----------------- |
| 最大サイズ     | 255文字（一般的） | 65,535バイト      |
| インデックス   | ✅ 可能           | ✅ 可能（プレフィックス指定が必要）|
| デフォルト値   | ✅ 設定可能       | ❌ 設定不可       |
| 用途           | 短い文字列        | 長い文字列        |

**使い分けの目安**：

- 255文字以内 → `VARCHAR`
- 255文字を超える → `TEXT`

### CHAR（固定長文字列）

**用途**：固定長の文字列（郵便番号、国コードなど）

**構文**：`CHAR(文字数)`

**例**：

```sql
CREATE TABLE addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    postal_code CHAR(7),  -- 郵便番号（例：1234567）
    country_code CHAR(2),  -- 国コード（例：JP、US）
    phone_number CHAR(11)  -- 電話番号（例：09012345678）
);
```

**特徴**：

- **固定長**：常に指定した文字数分のメモリを使う
  - 例：`CHAR(10)` で「太郎」を保存 → 10文字分使用（無駄がある）
- **パディング**：短い文字列は空白で埋められる
- **高速**：固定長なので、アクセスが少し速い

**VARCHARとCHARの使い分け**：

- **固定長のデータ**（郵便番号、国コードなど）→ `CHAR`
- **可変長のデータ**（名前、メールアドレスなど）→ `VARCHAR`

---

## 📅 日付・時刻型（Date and Time Types）

### DATE（日付）

**用途**：日付のみ（年-月-日）

**形式**：`YYYY-MM-DD`（例：`2024-01-15`）

**範囲**：`1000-01-01` 〜 `9999-12-31`

**例**：

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    birth_date DATE,  -- 生年月日（例：1990-05-20）
    joined_date DATE  -- 入会日（例：2024-01-01）
);
```

### DATETIME（日付と時刻）

**用途**：日付と時刻（年-月-日 時:分:秒）

**形式**：`YYYY-MM-DD HH:MM:SS`（例：`2024-01-15 14:30:00`）

**範囲**：`1000-01-01 00:00:00` 〜 `9999-12-31 23:59:59`

**例**：

```sql
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200),
    content TEXT,
    published_at DATETIME  -- 公開日時（例：2024-01-15 14:30:00）
);
```

### TIMESTAMP（タイムスタンプ）

**用途**：日付と時刻（UNIX時間ベース）

**形式**：`YYYY-MM-DD HH:MM:SS`（表示はDATETIMEと同じ）

**範囲**：`1970-01-01 00:00:01` 〜 `2038-01-19 03:14:07`（UTCベース）

**例**：

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  -- 登録日時（自動設定）
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP  -- 更新日時（自動更新）
);
```

**DATETIMEとTIMESTAMPの違い**：

| 特徴               | DATETIME                          | TIMESTAMP                         |
| ------------------ | --------------------------------- | --------------------------------- |
| 範囲               | 1000年〜9999年                    | 1970年〜2038年                    |
| タイムゾーン       | ❌ なし                           | ✅ あり（UTCで保存）              |
| デフォルト値       | ✅ 設定可能                       | ✅ 設定可能                       |
| 自動更新           | ❌ なし                           | ✅ ON UPDATE CURRENT_TIMESTAMP    |
| ストレージ         | 8バイト                           | 4バイト（省スペース）             |

**使い分け**：

- **記録用**（作成日時、更新日時）→ `TIMESTAMP`（推奨）
- **予定日**（イベント日時）→ `DATETIME`
- **生年月日**（日付のみ）→ `DATE`

---

## ✅ 真偽値型（Boolean Type）

### BOOLEAN（TINYINT(1)）

**用途**：真偽値（ON/OFF、YES/NO、TRUE/FALSE）

**内部的にはTINYINT(1)**：

- `FALSE` = `0`
- `TRUE` = `1`

**例**：

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,  -- アクティブかどうか
    is_admin BOOLEAN DEFAULT FALSE,  -- 管理者かどうか
    email_verified BOOLEAN DEFAULT FALSE  -- メール認証済みか
);
```

**挿入例**：

```sql
INSERT INTO users (name, is_active, is_admin) VALUES
('山田太郎', TRUE, FALSE),
('佐藤花子', TRUE, TRUE),
('鈴木一郎', FALSE, FALSE);
```

**検索例**：

```sql
-- アクティブなユーザーを取得
SELECT * FROM users WHERE is_active = TRUE;

-- 管理者を取得
SELECT * FROM users WHERE is_admin = TRUE;
```

---

## 🎯 適切なデータ型の選択

### 選択の基準

1. **データの性質**：数値？文字列？日付？
2. **データの範囲**：最大値、最小値は？
3. **データの長さ**：最大文字数は？
4. **精度**：正確な値が必要？近似値でOK？
5. **検索の頻度**：よく検索するカラム？

### 実例：ECサイトのproductsテーブル

**悪い例**：

```sql
CREATE TABLE products (
    id VARCHAR(255),  -- ❌ IDは数値の方が良い
    name TEXT,  -- ❌ 商品名は長くない
    price FLOAT,  -- ❌ 金額はDECIMALで正確に
    stock VARCHAR(100),  -- ❌ 在庫数は整数
    created_at VARCHAR(255)  -- ❌ 日時はTIMESTAMP
);
```

**良い例**：

```sql
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,  -- ✅ IDは整数、AUTO_INCREMENT
    name VARCHAR(200) NOT NULL,  -- ✅ 商品名は適切な長さ
    price DECIMAL(10, 2) NOT NULL,  -- ✅ 金額は正確なDECIMAL
    stock INT UNSIGNED NOT NULL DEFAULT 0,  -- ✅ 在庫数は符号なし整数
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP  -- ✅ 日時はTIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## 🤖 バイブコーディング実践（最重要セクション！）

### AIへの指示例

#### 良い指示の例1：データ型を明示したテーブル作成

```text
「MySQLで、ECサイトのproductsテーブルを作成するCREATE TABLE文を書いてください。
以下のカラムを含めてください：
- id：INT型、主キー、AUTO_INCREMENT
- name：VARCHAR(200)型、NOT NULL（商品名）
- description：TEXT型、商品の詳細説明
- price：DECIMAL(10, 2)型、NOT NULL（価格、正確な金額）
- stock：INT型、UNSIGNED、NOT NULL、デフォルト値0（在庫数）
- is_active：BOOLEAN型、デフォルト値TRUE（販売中かどうか）
- created_at：TIMESTAMP型、デフォルトCURRENT_TIMESTAMP

文字コードはutf8mb4を使用してください。」
```

**AIが生成するSQL**：

```sql
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT UNSIGNED NOT NULL DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**なぜ良い？**

- ✅ データ型を具体的に指定
- ✅ 制約（NOT NULL、UNSIGNED、DEFAULT）も明示
- ✅ 用途（金額、在庫数など）も説明

### 生成されたテーブル設計のチェックポイント

#### データ型チェック

- [ ] **数値型**
  - IDは `INT` または `BIGINT`
  - 金額は `DECIMAL`（FLOATは使わない）
  - 整数は適切なサイズ（年齢は `TINYINT`、一般的な数値は `INT`）
  - 負の数が不要な場合は `UNSIGNED`

- [ ] **文字列型**
  - 短い文字列（名前、メールアドレス）は `VARCHAR`
  - 長い文字列（本文、説明）は `TEXT`
  - 固定長（郵便番号、国コード）は `CHAR`

- [ ] **日付・時刻型**
  - 作成日時、更新日時は `TIMESTAMP`
  - イベント日時は `DATETIME`
  - 生年月日は `DATE`

- [ ] **真偽値型**
  - ON/OFF、YES/NOは `BOOLEAN`

### よくある問題と修正方法

#### 問題1：金額にFLOATを使っている

**AIが生成しがちな危険なコード**：

```sql
CREATE TABLE products (
    price FLOAT  -- ❌ 金額にFLOATは危険
);
```

**原因**：FLOATは近似値なので、計算誤差が発生する可能性がある

**修正**：

```sql
CREATE TABLE products (
    price DECIMAL(10, 2) NOT NULL  -- ✅ 金額はDECIMALで正確に
);
```

**AIへの修正指示**：

```text
「金額を保存するpriceカラムはDECIMAL(10, 2)型に変更してください。
FLOATは近似値なので、金額には不適切です。」
```

#### 問題2：短い文字列にTEXTを使っている

**AIが生成しがちなコード**：

```sql
CREATE TABLE users (
    name TEXT,  -- ❌ 名前にTEXTは大きすぎる
    email TEXT  -- ❌ メールアドレスにTEXTは大きすぎる
);
```

**原因**：TEXT型はインデックスが貼りづらく、検索が遅い

**修正**：

```sql
CREATE TABLE users (
    name VARCHAR(100) NOT NULL,  -- ✅ 名前は適切な長さ
    email VARCHAR(255) NOT NULL UNIQUE  -- ✅ メールアドレスも適切な長さ
);
```

**AIへの修正指示**：

```text
「nameとemailカラムはTEXTではなく、VARCHARに変更してください。
nameはVARCHAR(100)、emailはVARCHAR(255)が適切です。」
```

#### 問題3：在庫数に符号付き整数を使っている

**AIが生成しがちなコード**：

```sql
CREATE TABLE products (
    stock INT  -- ❌ 在庫数は負の数にならない
);
```

**原因**：在庫数は0以上なので、符号なし整数が適切

**修正**：

```sql
CREATE TABLE products (
    stock INT UNSIGNED NOT NULL DEFAULT 0  -- ✅ 符号なし整数
);
```

**AIへの修正指示**：

```text
「stockカラムはINT UNSIGNED NOT NULL DEFAULT 0に変更してください。
在庫数は負の数にならないので、UNSIGNEDを付けてください。」
```

---

## 💪 演習問題

演習問題は別ファイルにまとめています。実際にデータ型を選択して、テーブルを設計してみよう！

👉 **[演習問題を見る](exercises/README.md)**

---

## ✅ まとめ

このレッスンで学んだことを振り返ろう！

### 数値型

- ✅ **INT**：一般的な整数（推奨）
- ✅ **TINYINT**：小さい整数（年齢、フラグ）
- ✅ **DECIMAL**：正確な小数（金額）
- ✅ **FLOAT/DOUBLE**：近似値でOKな小数（座標、温度）
- ✅ **UNSIGNED**：負の数が不要な場合に付ける

### 文字列型

- ✅ **VARCHAR**：短〜中程度の文字列（名前、メールアドレス）
- ✅ **TEXT**：長い文字列（本文、説明文）
- ✅ **CHAR**：固定長文字列（郵便番号、国コード）

### 日付・時刻型

- ✅ **DATE**：日付のみ（生年月日）
- ✅ **DATETIME**：日付と時刻（イベント日時）
- ✅ **TIMESTAMP**：日付と時刻（作成日時、更新日時）

### 真偽値型

- ✅ **BOOLEAN**：真偽値（ON/OFF、YES/NO）

### 適切なデータ型の選択

- ✅ データの性質、範囲、長さに応じて選択
- ✅ 金額は必ずDECIMAL
- ✅ 短い文字列はVARCHAR、長い文字列はTEXT
- ✅ 負の数が不要な場合はUNSIGNED

---

## 🚀 次のステップ

データ型をマスターしたね！すごい！✨

次のLesson 05では、**基本的なテーブル設計**を学ぶよ！

- 正規化の基礎（第1正規形）
- 1対多の関係
- 制約（PRIMARY KEY、FOREIGN KEY、UNIQUE、NOT NULL、DEFAULT）
- インデックスの基礎
- 命名規則

データ型の知識を活かして、効率的なテーブル設計をマスターしよう！

👉 **[Lesson 05: 基本的なテーブル設計へ進む](../05-table-design/README.md)**

---

**Let's vibe and code! 🎉**

データ型を正しく選ぶことで、パフォーマンスとデータ整合性が向上する！AIと一緒に、効率的なデータベースを設計しよう！
