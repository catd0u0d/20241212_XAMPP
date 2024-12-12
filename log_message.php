<?php
function logMessage($message) {
    $logFile = 'debug.log'; // 日誌文件名
    $maxLines = 20000; // 最大行數

    // 如果日誌文件存在，檢查其行數
    if (file_exists($logFile)) {
        // 讀取整個文件
        $lines = file($logFile);

        // 如果行數超過最大限制，則刪除最舊的行
        if (count($lines) >= $maxLines) {
            // 保留最後 $maxLines 行
            $lines = array_slice($lines, -($maxLines));
            // 寫回文件
            file_put_contents($logFile, implode('', $lines));
        }
    }

    // 添加新的日誌訊息
    $timestamp = date('Y-m-d H:i:s'); // 紀錄當前時間
    $formattedMessage = "[{$timestamp}] {$message}\n"; // 格式化訊息

    // 寫入日誌
    file_put_contents($logFile, $formattedMessage, FILE_APPEND);
}
?>
