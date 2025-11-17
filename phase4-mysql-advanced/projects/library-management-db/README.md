# 総合プロジェクト2: 図書館管理システムのデータベース設計 📚

**プロジェクト概要**：図書館の本の貸し出し管理システムのデータベースを設計・実装し、Phase 4の技術を実践する！

---

## 🎯 プロジェクトの目標

Phase 4で学んだ技術を使って、図書館管理システムのデータベースを構築しよう！

- JOIN（本と著者の多対多）
- 集計関数（人気の本ランキング）
- LEFT JOIN（貸し出し中の本を取得）
- トランザクション（貸し出し・返却処理）

---

## 📊 データベース設計

### テーブル一覧

1. **members**：会員情報
2. **books**：本の情報
3. **authors**：著者情報
4. **book_authors**：本と著者の多対多（中間テーブル）
5. **lendings**：貸し出し履歴

---

## 💻 実装内容

### 実践クエリ例

**本と著者を取得**：

```sql
SELECT
    b.title,
    GROUP_CONCAT(a.name) AS authors
FROM
    books AS b
LEFT JOIN
    book_authors AS ba ON b.id = ba.book_id
LEFT JOIN
    authors AS a ON ba.author_id = a.id
GROUP BY
    b.id, b.title;
```

**貸し出し中の本を取得**：

```sql
SELECT
    b.title,
    m.name AS member_name,
    l.lent_at
FROM
    books AS b
INNER JOIN
    lendings AS l ON b.id = l.book_id
INNER JOIN
    members AS m ON l.member_id = m.id
WHERE
    l.returned_at IS NULL;
```

---

## ✅ 学べること

- ✅ 多対多の関係（本と著者）
- ✅ LEFT JOINで貸し出し状況を取得
- ✅ 集計関数で人気ランキング
- ✅ トランザクションで貸し出し処理

---

**Let's vibe and code! 🎉**

図書館管理システムのデータベースを構築しよう！
