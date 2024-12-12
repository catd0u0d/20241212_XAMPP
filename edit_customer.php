<?php
require_once 'db_connect.php';
require_once 'log_message.php';

$id = $name = $contact_info = $company_name = $notes = "";
$errors = [];
$successMessage = "";

// 確認是否為 GET 請求並載入資料
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $id = intval($_GET["id"]);
    $conn = getConnection();
    $sql = "SELECT * FROM customers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $name = $row["name"];
        $contact_info = $row["contact_info"];
        $company_name = $row["company_name"];
        $notes = $row["notes"];
    } else {
        $errors[] = "找不到該客戶資料";
    }

    $stmt->close();
    $conn->close();
}

// 確認是否為 POST 請求並更新資料
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST["id"]);
    $name = trim($_POST["name"]);
    $contact_info = trim($_POST["contact_info"]);
    $company_name = trim($_POST["company_name"]);
    $notes = trim($_POST["notes"]);

    // 驗證必填欄位
    if (empty($name)) $errors[] = "客戶姓名為必填";
    if (empty($contact_info)) $errors[] = "聯絡資訊為必填";
    if (empty($company_name)) $errors[] = "公司名稱為必填";

    if (empty($errors)) {
        $conn = getConnection();

        // 取得舊資料
        $sql = "SELECT * FROM customers WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $oldData = $result->fetch_assoc();

        // 更新資料
        $sql = "UPDATE customers SET name = ?, contact_info = ?, company_name = ?, notes = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $contact_info, $company_name, $notes, $id);

        if ($stmt->execute()) {
            $successMessage = "客戶資料更新成功！";

            // 紀錄日誌
            $logMessage = "客戶 ID: {$id} 資料已更新。\n";
            $logMessage .= "更新前: " . json_encode($oldData, JSON_UNESCAPED_UNICODE) . "\n";
            $logMessage .= "更新後: " . json_encode([
                'name' => $name,
                'contact_info' => $contact_info,
                'company_name' => $company_name,
                'notes' => $notes
            ], JSON_UNESCAPED_UNICODE) . "\n";

            logMessage($logMessage);
        } else {
            $errors[] = "資料庫更新失敗: " . $conn->error;
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>編輯客戶資料</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
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
        form {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
        }
        input[type="submit"] {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1.2rem;
        }
        input[type="submit"]:hover {
            background-color: #2980b9;
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

    <h1>編輯客戶資料</h1>

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

    <!-- 編輯表單 -->
    <form method="post">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">
        
        <label for="name">客戶姓名:</label>
        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>">
        
        <label for="contact_info">聯絡資訊:</label>
        <textarea name="contact_info" id="contact_info"><?php echo htmlspecialchars($contact_info, ENT_QUOTES, 'UTF-8'); ?></textarea>
        
        <label for="company_name">公司名稱:</label>
        <input type="text" name="company_name" id="company_name" value="<?php echo htmlspecialchars($company_name, ENT_QUOTES, 'UTF-8'); ?>">
        
        <label for="notes">備註:</label>
        <textarea name="notes" id="notes"><?php echo htmlspecialchars($notes, ENT_QUOTES, 'UTF-8'); ?></textarea>
        
        <input type="submit" value="更新">
    </form>

    <p style="text-align: center;">
        <a href="view_customers.php">返回客戶列表</a>
    </p>

</body>
</html>
