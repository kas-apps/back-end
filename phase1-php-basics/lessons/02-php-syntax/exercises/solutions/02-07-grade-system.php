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
