<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Bangkok');

include '../conn.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // var_dump($_POST);     

    $sql = "SELECT count(dncode) chkdncode FROM dnmaster where dncode = '" . $_POST['dncode'] . "'  ";
    // $sql = "SELECT count(dncode) chkdncode FROM dnmaster where dncode = 'DN67100018'  ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    extract($result, EXTR_OVERWRITE, "_");

    if ($chkdncode == 0) {

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
            (dncode,cuscode, dndate,socode, doc_status, created_date)
            VALUES (:dncode,:cuscode, :dndate,:socode, 'รอออกใบแจ้งหนี้', current_timestamp())";

        $stmt = $conn->prepare($sql);
        if (!$stmt) throw new PDOException("Insert data error => {$conn->errorInfo()}");

        $stmt->bindParam(":dncode", $dncode, PDO::PARAM_STR);
        $stmt->bindParam(":cuscode", $_POST['cuscode'], PDO::PARAM_STR);
        $stmt->bindParam(":dndate", $today, PDO::PARAM_STR);
        $stmt->bindParam(":socode", $_POST['socode'], PDO::PARAM_STR);

        if (!$stmt->execute()) {
            $error = $conn->errorInfo();
            throw new PDOException("Insert data error => $error");
            die;
        }
    }



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

            $sql = "update sodetail set delamount = delamount+:unit_weight where socode = :socode and stcode = :stcode ";

            $stmt2 = $conn->prepare($sql);
            if (!$stmt2) throw new PDOException("Insert data error => {$conn->errorInfo()}");

            $stmt2->bindValue(":socode", $_POST['socode'], PDO::PARAM_STR);
            $stmt2->bindValue(":stcode", $stcode, PDO::PARAM_STR);
            $stmt2->bindParam(":unit_weight", $unit_weight, PDO::PARAM_STR);

            if (!$stmt2->execute()) {
                $error = $conn->errorInfo();
                throw new PDOException("Insert data error => $error");
                die;
            }

            $strSQL = "SELECT SUM(delamount-qty),SUM(IF(delamount-qty>=0, 0, 1)) as count FROM `sodetail` WHERE socode = '" . $_POST['socode'] . "' ";
            $stmt = $conn->prepare($strSQL);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            extract($res, EXTR_OVERWRITE, "_");
            if ($count == 0) {

                $sql = "update somaster set doc_status = 'รอชำระเงิน' where socode = :socode ";

                $stmt3 = $conn->prepare($sql);
                if (!$stmt3) throw new PDOException("Insert data error => {$conn->errorInfo()}");

                $stmt3->bindValue(":socode", $_POST['socode'], PDO::PARAM_STR);

                if (!$stmt3->execute()) {
                    $error = $conn->errorInfo();
                    throw new PDOException("Insert data error => $error");
                    die;
                }
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
    } else
        $response = ['status' => 0, 'message' => 'ไม่พบรหัส Barcode สินค้านี้'];
    // 
} else {
    http_response_code(400);
    echo json_encode(array('status' => 0, 'message' => 'ข้อมูลซ้ำ'));
}

echo json_encode($response);
