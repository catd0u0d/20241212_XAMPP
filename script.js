$(document).ready(function() {

    // ----- 即時搜尋高亮功能 (search_customers.php) -----
    $("#search-form input[name='keyword']").on("keyup", function() {
        var keyword = $(this).val().trim().toLowerCase();
        var table = $("#search-result-table");

        if (keyword === "") {
            table.find("td").each(function() {
                $(this).html($(this).text()); // 還原高亮
            });
           return;
        }
      

        table.find("td").each(function() {
            var text = $(this).text().toLowerCase();
            var highlightedText = text.replace(new RegExp(keyword, 'gi'), function(match) {
                return '<span class="highlight">' + match + '</span>';
            });
            $(this).html(highlightedText);
        });
    });

    // ----- 表格操作功能 (view_customers.php) -----

     // 表格奇偶數行顏色
     $("#customer-table tbody tr:odd").css("background-color", "#f9f9f9");

     // 表格滑鼠移入移出效果
    $("#customer-table tbody tr").hover(function(){
       $(this).css("background-color","#e0e0e0");
    }, function(){
        $(this).css("background-color","");
        $("#customer-table tbody tr:odd").css("background-color", "#f9f9f9"); //保持奇數行顏色
    });

    // ----- 表單驗證提示功能 (add_customer.php, edit_customer.php) -----
    $("form").submit(function(event) {
        var form = $(this);
        var valid = true;

        // 清除之前的錯誤提示
        form.find(".error-message").remove();

        // 檢查必填欄位 (例如客戶姓名)
        var nameField = form.find("input[name='name']");
        if (nameField.val().trim() === "") {
            nameField.after('<span class="error-message" style="color:red;">客戶姓名為必填</span>');
            valid = false;
        }

         // 其他驗證規則可以繼續加入

        if (!valid) {
            event.preventDefault(); // 阻止表單提交
        }

    });
});