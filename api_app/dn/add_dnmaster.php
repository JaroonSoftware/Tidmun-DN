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


    $sql = "update options set dncode = dncode + 1 where year = :y and month = :m";
    $stmt = $conn->prepare($sql);

    if (!$stmt->execute(['y' => $year, 'm' => $m])) {
        $error = $conn->errorInfo();
        http_response_code(401);
        throw new PDOException("Update code error => $error");
    }

    $sql = "INSERT INTO dnmaster
    (dncode,cuscode, dndate, active_status, created_date)
    VALUES (:dncode,:cuscode, :dndate, 'รอออกใบแจ้งหนี้', current_timestamp())";

    $stmt = $conn->prepare($sql);
    if (!$stmt) throw new PDOException("Insert data error => {$conn->errorInfo()}");

    $stmt->bindParam(":dncode", $dncode, PDO::PARAM_STR);
    $stmt->bindParam(":cuscode", $_POST['cuscode'], PDO::PARAM_STR);
    $stmt->bindParam(":dndate", $today, PDO::PARAM_STR);

    // if (!$stmt->execute()) {
    //     $error = $conn->errorInfo();
    //     throw new PDOException("Insert data error => $error");
    //     die;
    // }





    

    // $sql = "
    // update grbarcode 
    // set
    // unit_weight = :unit_weight,
    // barcode_status = 'ออก barcode แล้ว',
    // barcode_date = CURRENT_TIMESTAMP()
    // where grcode = :grcode and stcode = :stcode and no = :no";

    // $stmt = $conn->prepare($sql);
    // if (!$stmt) throw new PDOException("Insert data error => {$conn->errorInfo()}");

    // $stmt->bindValue(":unit_weight", number_format($_POST['unit_weight'], 2), PDO::PARAM_STR);
    // $stmt->bindParam(":no", $_POST['no'], PDO::PARAM_STR);
    // $stmt->bindParam(":stcode", $_POST['stcode'], PDO::PARAM_STR);
    // $stmt->bindValue(":grcode", $_POST['grcode'], PDO::PARAM_STR);

    // if (!$stmt->execute()) {
    //     $error = $conn->errorInfo();
    //     throw new PDOException("Insert data error => $error");
    //     die;
    // }

    // $strSQL = "SELECT count(id) as count FROM `grbarcode` where barcode_status!='ออก barcode แล้ว' and grcode = '".$_POST['grcode']."' and stcode = '".$_POST['stcode']."' ";
    // $stmt = $conn->prepare($strSQL);
    // $stmt->execute();
    // $res = $stmt->fetch(PDO::FETCH_ASSOC);
    // extract($res, EXTR_OVERWRITE, "_");                
    //     if($count==0)
    //     {

    //         $sql = "
    //         update grmaster 
    //         set
    //         doc_status = 'ชั่งสินค้าครบแล้ว',
    //         updated_date = CURRENT_TIMESTAMP()
    //         where grcode = :grcode ";

    //         $stmt = $conn->prepare($sql);
    //         if (!$stmt) throw new PDOException("Insert data error => {$conn->errorInfo()}");

    //         $stmt->bindValue(":grcode", $_POST['grcode'], PDO::PARAM_STR);

    //         if (!$stmt->execute()) {
    //             $error = $conn->errorInfo();
    //             throw new PDOException("Insert data error => $error");
    //             die;
    //         }
    //     }

    // $conn->commit();
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
