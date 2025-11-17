# Lesson 04: データ型とテーブル設計 - 解答例 ✅

演習お疲れさま！ここでは各演習の解答例と詳しい解説を紹介するよ。

---

## 🎯 基礎編：データ型の選択

### 演習 1-1: 適切なデータ型を選ぶ ✅

**解答**：

1. **ユーザーの年齢**（0〜150歳）→ `TINYINT UNSIGNED`
   - 理由：0〜255の範囲で十分、省メモリ

2. **商品の価格**（例：1,234.56円）→ `DECIMAL(10, 2)`
   - 理由：金額は正確な値が必要、FLOATは誤差が発生する可能性

3. **商品名**（最大100文字）→ `VARCHAR(100)`
   - 理由：短い文字列、可変長で効率的

4. **商品の説明文**（最大1000文字）→ `TEXT`
   - 理由：長い文字列、VARCHARの上限を超える

5. **生年月日**（例：1990-05-20）→ `DATE`
   - 理由：日付のみ、時刻は不要

6. **登録日時**（例：2024-01-15 14:30:00）→ `TIMESTAMP`
   - 理由：日付と時刻、タイムゾーン対応

7. **在庫数**（0以上の整数）→ `INT UNSIGNED`
   - 理由：負の数は不要、UNSIGNED で範囲を広げる

8. **販売中かどうか**（ON/OFF）→ `BOOLEAN`
   - 理由：真偽値、内部的には TINYINT(1)

---

### 演習 1-2: テーブル作成（データ型指定） ✅

**解答**：

```sql
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    age TINYINT UNSIGNED,
    gpa DECIMAL(3, 2),  -- 例：3.75（最大4.00）
    enrolled_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**解説**：

- `TINYINT UNSIGNED`：年齢は0〜255の範囲で十分
- `DECIMAL(3, 2)`：GPA（成績平均）は0.00〜9.99の範囲（通常は0.00〜4.00）
- `DATE`：入学日は日付のみ
- `TIMESTAMP`：登録日時は自動設定

---

## 🚀 応用編：実践的なテーブル設計

### 演習 2-1: ECサイトのproductsテーブル ✅

**解答**：

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

**解説**：

- `id`：INT型、AUTO_INCREMENT で自動採番
- `name`：VARCHAR(200) で短い文字列
- `description`：TEXT で長文
- `price`：DECIMAL(10, 2) で正確な金額
- `stock`：INT UNSIGNED で0以上の整数
- `is_active`：BOOLEAN で販売中かどうか
- `created_at`：TIMESTAMP で自動設定

---

### 演習 2-2: データ型の間違いを修正 ✅

**問題点と修正**：

```sql
-- 元のテーブル（問題あり）
CREATE TABLE users (
    id VARCHAR(255),  -- ❌ 問題1：IDは数値の方が効率的
    name TEXT,  -- ❌ 問題2：名前は短いのでVARCHAR
    age VARCHAR(10),  -- ❌ 問題3：年齢は整数
    balance FLOAT,  -- ❌ 問題4：金額はDECIMAL
    birth_date VARCHAR(50),  -- ❌ 問題5：日付はDATE
    is_active VARCHAR(10)  -- ❌ 問題6：真偽値はBOOLEAN
);

-- 修正版（正しい）
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,  -- ✅ 整数、主キー
    name VARCHAR(100) NOT NULL,  -- ✅ 短い文字列
    age TINYINT UNSIGNED,  -- ✅ 小さい整数
    balance DECIMAL(12, 2) NOT NULL,  -- ✅ 正確な金額
    birth_date DATE,  -- ✅ 日付
    is_active BOOLEAN DEFAULT TRUE  -- ✅ 真偽値
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**解説**：

- **問題1**：IDはINT型が効率的、検索も高速
- **問題2**：名前は最大100文字程度なのでVARCHAR
- **問題3**：年齢はTINYINT UNSIGNEDで0〜255の範囲
- **問題4**：金額はDECIMALで正確な値を保存
- **問題5**：生年月日はDATE型
- **問題6**：ON/OFFの判定はBOOLEAN

---

## 💡 チャレンジ問題

### チャレンジ 1: ブログシステムのテーブル設計 ✅

**解答**：

```sql
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,  -- 外部キー（Lesson 05で学ぶ）
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    is_published BOOLEAN DEFAULT FALSE,
    view_count INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**解説**：

- `id`：INT型、AUTO_INCREMENT
- `user_id`：INT型（Lesson 05で外部キー制約を追加）
- `title`：VARCHAR(200) で短いタイトル
- `content`：TEXT で長文
- `is_published`：BOOLEAN で公開状態
- `view_count`：INT UNSIGNED で閲覧数
- `created_at`：作成日時（自動設定）
- `updated_at`：更新日時（自動設定、更新時に自動更新）

---

### チャレンジ 2: AIにテーブル設計を依頼 ✅

**AIが生成するSQL**：

```sql
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    status ENUM('pending', 'in_progress', 'completed') DEFAULT 'pending',
    priority TINYINT UNSIGNED DEFAULT 1,
    due_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**チェックポイント**：

- ✅ `id`：INT型、主キー、AUTO_INCREMENT
- ✅ `title`：VARCHAR(200)、NOT NULL
- ✅ `description`：TEXT（長文）
- ✅ `status`：ENUM型で選択肢を制限
- ✅ `priority`：TINYINT UNSIGNED（1〜5の範囲）
- ✅ `due_date`：DATE型
- ✅ `created_at`：TIMESTAMP、自動設定
- ✅ `completed_at`：TIMESTAMP、NULL許可
- ✅ 文字コード：utf8mb4

**解説**：

- **ENUM型**：status は 'pending', 'in_progress', 'completed' の3つの値だけを許可
- **TINYINT UNSIGNED**：priority は1〜5の範囲（0〜255）
- **NULL許可**：completed_at は完了時のみ値が入る

---

## 🎓 まとめ

演習を通じて、データ型の選択をマスターしたね！

### 重要なポイント

- ✅ **数値型**：INT（一般的）、TINYINT（小さい）、DECIMAL（金額）
- ✅ **文字列型**：VARCHAR（短い）、TEXT（長い）
- ✅ **日付・時刻型**：DATE（日付）、TIMESTAMP（日時）
- ✅ **真偽値型**：BOOLEAN（ON/OFF）
- ✅ **UNSIGNED**：負の数が不要な場合に付ける

### 次のステップ

次のLesson 05では、テーブル間の関係（1対多）、制約（外部キー、一意制約）、インデックスを学んでいくよ！

👉 **[Lesson 05: 基本的なテーブル設計へ進む](../../05-table-design/README.md)**

---

**Let's vibe and code! 🎉**
