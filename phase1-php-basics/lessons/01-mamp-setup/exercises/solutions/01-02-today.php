<?php
// 現在の日時を表示するプログラム

// タイムゾーンを日本に設定
date_default_timezone_set('Asia/Tokyo');

// 今日の日付を取得
$today = date('Y年m月d日');

// 現在の時刻を取得
$currentTime = date('H:i:s');

// 表示
echo "今日は" . $today . "です。<br>";
echo "現在の時刻は" . $currentTime . "です。";
