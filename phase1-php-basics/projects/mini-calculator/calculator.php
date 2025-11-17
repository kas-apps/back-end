<?php
/**
 * Mini Calculator - 計算ロジック
 * Phase 1の知識を活用した電卓機能
 */

/**
 * 入力値のバリデーション
 *
 * @param string $num1 数値1
 * @param string $num2 数値2
 * @param string $operator 演算子
 * @return string|null エラーメッセージ（エラーがなければnull）
 */
function validateInput($num1, $num2, $operator) {
    // 必須チェック
    if (empty($num1) || empty($num2) || empty($operator)) {
        return "すべての項目を入力してください";
    }

    // 数値チェック
    if (!is_numeric($num1) || !is_numeric($num2)) {
        return "数値を入力してください";
    }

    // 演算子チェック
    $allowedOperators = ['+', '-', '*', '/'];
    if (!in_array($operator, $allowedOperators)) {
        return "無効な演算子です";
    }

    // ゼロ除算チェック
    if ($operator === '/' && (float)$num2 == 0) {
        return "ゼロで割ることはできません";
    }

    // エラーなし
    return null;
}

/**
 * 計算を実行
 *
 * @param float $num1 数値1
 * @param float $num2 数値2
 * @param string $operator 演算子
 * @return float 計算結果
 */
function calculate($num1, $num2, $operator) {
    switch ($operator) {
        case '+':
            return $num1 + $num2;
        case '-':
            return $num1 - $num2;
        case '*':
            return $num1 * $num2;
        case '/':
            return $num1 / $num2;
        default:
            return 0;
    }
}
?>
