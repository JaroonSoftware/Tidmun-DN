<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Bangkok');

include '../conn.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // var_dump($_POST);     

    $year = date("Y");
    $y = substr(date("Y") + 543, -2);
    $m = date("m");
    $prefix = "DN$y$m";
    $today = date("Y-m-d");

    $sql = "SELECT dncode code FROM options where year = :y and month = :m ";
    $stmt = $conn->prepare($sql);
    if (!$stmt->execute(['y' => $year, 'm' => $m])) {
        $error = $conn->errorInfo();
        http_response_code(401);
        throw new PDOException("Geting code error => $error");
    }
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $number = intval($result["code"]);

    $dncode = $prefix . sprintf("%04s", ($number));
    if ($stmt->execute()) {
        http_response_code(200);
        $response = ['status' => 1, 'message' => 'เพิ่มข้อมูลสำเร็จ', 'dncode' => $dncode];
    } else {
        $response = ['status' => 0, 'message' => 'Error! ติดต่อโปรแกรมเมอร์'];
    }
} else {
    http_response_code(400);
    echo json_encode(array('status' => 2, 'message' => 'ข้อมูลซ้ำ'));
}

echo json_encode($response);
