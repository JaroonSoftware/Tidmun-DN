<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Bangkok');

include '../conn.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // var_dump($_POST);     

    $sql = "SELECT a.barcode_id,a.stcode,i.stname,a.unit_weight,a.dncode,a.barcode_status FROM items_barcode a left outer join items i on (a.stcode=i.stcode) where barcode_id = '" . $_POST['barcode_id'] . "'  ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!empty($result)) {
        $res = $result;
        extract($res, EXTR_OVERWRITE, "_");
        if ($barcode_status == 'อยู่ในสต๊อก') {

            $sql = "INSERT INTO dndetail
        (dncode,stcode,unit_weight, barcode_id, created_date)
        VALUES (:dncode,:stcode,:unit_weight, :barcode_id, current_timestamp())";

            $stmt = $conn->prepare($sql);
            if (!$stmt) throw new PDOException("Insert data error => {$conn->errorInfo()}");

            $stmt->bindParam(":dncode", $_POST['dncode'], PDO::PARAM_STR);
            $stmt->bindParam(":stcode", $stcode, PDO::PARAM_STR);
            $stmt->bindParam(":unit_weight", $unit_weight, PDO::PARAM_STR);
            $stmt->bindParam(":barcode_id", $barcode_id, PDO::PARAM_STR);

            if (!$stmt->execute()) {
                $error = $conn->errorInfo();
                throw new PDOException("Insert data error => $error");
                die;
            }

            $sql = "
        update items_barcode 
        set
        barcode_status = 'ขายแล้ว',
        socode = :socode,
        dncode = :dncode,
        updated_date = CURRENT_TIMESTAMP()
        where barcode_id = :barcode_id ";

            $stmt = $conn->prepare($sql);
            if (!$stmt) throw new PDOException("Insert data error => {$conn->errorInfo()}");

            $stmt->bindValue(":barcode_id", $_POST['barcode_id'], PDO::PARAM_STR);
            $stmt->bindValue(":socode", $_POST['socode'], PDO::PARAM_STR);
            $stmt->bindValue(":dncode", $_POST['dncode'], PDO::PARAM_STR);

            if ($stmt->execute()) {
                $json_result = array(
                    "stcode" => array(),
                    "stname" => array(),
                    "unit_weight" => array(),
                    "datetimelog" => array()

                );
                array_push($json_result['stcode'], $stcode);
                array_push($json_result['stname'], $stname);
                array_push($json_result['unit_weight'], $unit_weight);
                array_push($json_result['datetimelog'], date("Y-m-d"));

                http_response_code(200);
                $response = ['status' => 1, 'message' => 'เพิ่มข้อมูลสำเร็จ', 'data' => $json_result];
            } else
                $response = ['status' => 0, 'message' => 'Error! ติดต่อโปรแกรมเมอร์'];
        } else
            $response = ['status' => 0, 'message' => 'สินค้านี้ ออกใบส่งของเลขที่ ' . $dncode . ' ไปแล้ว'];
    } else {
        $response = ['status' => 0, 'message' => 'ไม่พบรหัส Barcode สินค้านี้'];
        // 
    }
} else {
    http_response_code(400);
    echo json_encode(array('status' => 0, 'message' => 'ข้อมูลซ้ำ'));
}

echo json_encode($response);
