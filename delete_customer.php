<?php
// 引入資料庫連線和記錄日誌的功能
require_once 'db_connect.php';
require_once 'log_message.php';

// 初始化變數
$id = "";
$errors = [];
$successMessage = "";

// 確認是否有有效的 ID 參數
if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $id = $_GET["id"];
    
    // 建立資料庫連線
    $conn = getConnection();

    // 在刪除之前，取得客戶資料並記錄到日誌
    $sqlSelect = "SELECT * FROM customers WHERE id = ?";
    $stmtSelect = $conn->prepare($sqlSelect);
    $stmtSelect->bind_param("i", $id);
    $stmtSelect->execute();
    $result = $stmtSelect->get_result();

    if ($result->num_rows === 1) {
        $customer = $result->fetch_assoc();
        // 將客戶資料記錄到日誌
        $logMessage = "刪除客戶資料: ID={$customer['id']}, 姓名={$customer['name']}, 聯絡資訊={$customer['contact_info']}, 公司名稱={$customer['company_name']}, 備註={$customer['notes']}";
        logMessage($logMessage);
    } else {
        $errors[] = "找不到該客戶資料，無法刪除。";
        $stmtSelect->close();
        $conn->close();
        // 顯示錯誤後終止
        include 'error_page.php';
        exit;
    }
    $stmtSelect->close();

    // 刪除資料
    $sqlDelete = "DELETE FROM customers WHERE id = ?";
    $stmtDelete = $conn->prepare($sqlDelete);
    $stmtDelete->bind_param("i", $id);

    if ($stmtDelete->execute()) {
        $successMessage = "客戶資料刪除成功！";
        $stmtDelete->close();
        $conn->close();
        header("Location: view_customers.php");
        exit;
    } else {
        $errors[] = "刪除客戶資料失敗: " . $conn->error;
    }

    $stmtDelete->close();
    $conn->close();
} else {
    $errors[] = "無效的客戶編號";
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>刪除客戶資料</title>
    <style>
        /* 設定頁面樣式 */
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
            margin-top: 50px;
            font-size: 2.5rem;
        }

        .message-container {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .errors {
            color: red;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
        }

        .success {
            color: green;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }

        a {
            text-decoration: none;
            color: #3498db;
            font-size: 1.2rem;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <h1>刪除客戶資料</h1>

    <!-- 顯示錯誤訊息 -->
    <?php if (!empty($errors)): ?>
        <div class="message-container errors">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- 顯示成功訊息 -->
    <?php if (!empty($successMessage)): ?>
        <div class="message-container success">
            <?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <!-- 返回客戶列表的連結 -->
    <p style="text-align: center;">
        <a href="view_customers.php">返回客戶列表</a>
    </p>

</body>
</html>
