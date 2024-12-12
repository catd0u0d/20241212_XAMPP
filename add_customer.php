<?php
// 引入資料庫連線
require_once 'db_connect.php';

// 初始化變數
$name = $contact_info = $company_name = $notes = "";
$errors = [];
$successMessage = "";

// 檢查是否為 POST 請求
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 取得 POST 資料
    $name = trim($_POST["name"]);
    $contact_info = trim($_POST["contact_info"]);
    $company_name = trim($_POST["company_name"]);
    $notes = trim($_POST["notes"]);

    // 資料驗證
    if (empty($name)) {
        $errors[] = "客戶姓名為必填。";
    } elseif (!preg_match("/^[a-zA-Z0-9\\x{4e00}-\\x{9fa5}]*$/u", $name)) {
        $errors[] = "客戶姓名只能包含英文大小寫、數字及中文或為空。";
    } elseif (in_array(strtolower($name), ['root', 'admin'])) {
        $errors[] = "客戶姓名不能為 'root' 或 'admin'。";
    }

    // 驗證聯絡資訊
    if (!empty($contact_info) && !preg_match("/^[a-zA-Z0-9@\-\,\/\s.]*$/", $contact_info) && !preg_match("/^TEL\/[0-9\-]+$/", $contact_info)) {
        $errors[] = "聯絡資訊只能包含英文大小寫、數字、@、-、,、/、空格、. 和 TEL/ 或為空。";
    }   

    // 驗證公司名稱
    if (!empty($company_name) && !preg_match("/^[a-zA-Z0-9\\x{4e00}-\\x{9fa5} @!?*:]*$/u", $company_name)) {
        $errors[] = "公司名稱只能包含英文大小寫、數字、中文、空格及 @、!、?、*、: 或為空。";
    }

    // 驗證備註
    if (!empty($notes) && preg_match("/<\/?\w+(\s+\w+(=\".*?\")?)*\s*>/", $notes)) {
        $errors[] = "備註中禁止包含程式碼。";
    }

    // 若無錯誤，則寫入資料庫
    if (empty($errors)) {
        $conn = getConnection();
        $sql = "INSERT INTO customers (name, contact_info, company_name, notes) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $contact_info, $company_name, $notes);

        if ($stmt->execute()) {
            $successMessage = "客戶資料新增成功！";
            // 清空表單
            $name = $contact_info = $company_name = $notes = "";
        } else {
            $errors[] = "資料庫寫入失敗: " . $conn->error;
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>新增客戶資料</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-container {
            width: 60%;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .form-container label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .form-container input[type="text"],
        .form-container textarea {
            width: calc(100% - 12px);
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .form-container textarea {
            height: 100px;
        }

        .form-container input[type="submit"] {
            background-color: #5cb85c;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-container input[type="submit"]:hover {
            background-color: #4cae4c;
        }

        .error-message {
            color: red;
        }
    </style>
</head>
<body>
    <h1>新增客戶資料</h1>

    <div class="form-container">
        <!-- 顯示錯誤訊息 -->
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- 顯示成功訊息 -->
        <?php if (!empty($successMessage)): ?>
            <div style="color: green;">
                <?php echo $successMessage; ?>
            </div>
        <?php endif; ?>

        <!-- 表單輸入欄位 -->
        <form method="post">
            <label for="name">客戶姓名:</label>
            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>" maxlength="50">

            <label for="contact_info">聯絡資訊:</label>
            <textarea name="contact_info" id="contact_info" maxlength="100"><?php echo htmlspecialchars($contact_info); ?></textarea>

            <label for="company_name">公司名稱:</label>
            <input type="text" name="company_name" id="company_name" value="<?php echo htmlspecialchars($company_name); ?>" maxlength="100">

            <label for="notes">備註:</label>
            <textarea name="notes" id="notes" maxlength="200"><?php echo htmlspecialchars($notes); ?></textarea>

            <input type="submit" value="新增">
        </form>
    </div>

    <a href="index.php">返回首頁</a>
</body>
</html>
