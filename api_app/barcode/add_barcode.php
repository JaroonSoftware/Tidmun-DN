<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Bangkok');

include '../conn.php';
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // var_dump($_POST);     

        $sql = "INSERT INTO tidmunzb_db.items_barcode
        (stcode,unit_weight, grcode, barcode_status, socode, dncode, created_date)
        VALUES(:stcode,:unit_weight, :grcode, 'อยู่ในสต๊อก', NULL, NULL, current_timestamp())";



        $stmt = $conn->prepare($sql);
        if (!$stmt) throw new PDOException("Insert data error => {$conn->errorInfo()}");

        $stmt->bindParam(":stcode", $_POST['stcode'], PDO::PARAM_STR);
        $stmt->bindValue(":unit_weight", number_format($_POST['unit_weight'], 2), PDO::PARAM_STR);
        $stmt->bindValue(":grcode", $_POST['grcode'], PDO::PARAM_STR);
        
        

        if ($stmt->execute()) {
            http_response_code(200);
            $response = ['status' => 1, 'message' => 'เพิ่มข้อมูลสำเร็จ', 'id' => $conn->lastInsertId()];
        } else {
            $response = ['status' => 0, 'message' => 'Error! ติดต่อโปรแกรมเมอร์'];
        }
    } else {
        http_response_code(400);
        echo json_encode(array('status' => 2, 'message' => 'ข้อมูลซ้ำ'));
    }

echo json_encode($response);
