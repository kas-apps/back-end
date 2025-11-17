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
