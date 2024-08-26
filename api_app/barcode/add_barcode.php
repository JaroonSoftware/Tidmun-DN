<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Bangkok');

    include '../conn.php';
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // var_dump($_POST);     

                $sql = "INSERT INTO items_barcode
                (stcode,unit_weight, grcode, barcode_status, socode, dncode, created_date)
                VALUES(:stcode,:unit_weight, :grcode, 'อยู่ในสต๊อก', NULL, NULL, current_timestamp())";

                $stmt = $conn->prepare($sql);
                if (!$stmt) throw new PDOException("Insert data error => {$conn->errorInfo()}");

                $stmt->bindParam(":stcode", $_POST['stcode'], PDO::PARAM_STR);
                $stmt->bindValue(":unit_weight", number_format($_POST['unit_weight'], 2), PDO::PARAM_STR);
                $stmt->bindValue(":grcode", $_POST['grcode'], PDO::PARAM_STR);

                if (!$stmt->execute()) {
                    $error = $conn->errorInfo();
                    throw new PDOException("Insert data error => $error");
                    die;
                }

                $sql = "
                update grbarcode 
                set
                unit_weight = :unit_weight,
                barcode_status = 'ออก barcode แล้ว',
                barcode_date = CURRENT_TIMESTAMP()
                where grcode = :grcode and stcode = :stcode and no = :no";

                $stmt = $conn->prepare($sql);
                if (!$stmt) throw new PDOException("Insert data error => {$conn->errorInfo()}");

                $stmt->bindValue(":unit_weight", number_format($_POST['unit_weight'], 2), PDO::PARAM_STR);
                $stmt->bindParam(":no", $_POST['no'], PDO::PARAM_STR);
                $stmt->bindParam(":stcode", $_POST['stcode'], PDO::PARAM_STR);
                $stmt->bindValue(":grcode", $_POST['grcode'], PDO::PARAM_STR);

                if (!$stmt->execute()) {
                    $error = $conn->errorInfo();
                    throw new PDOException("Insert data error => $error");
                    die;
                }

                $strSQL = "SELECT count(id) as count FROM `grbarcode` where barcode_status!='ออก barcode แล้ว' and grcode = '".$_POST['grcode']."' and stcode = '".$_POST['stcode']."' ";
                $stmt = $conn->prepare($strSQL);
                $stmt->execute();
                $res = $stmt->fetch(PDO::FETCH_ASSOC);
                extract($res, EXTR_OVERWRITE, "_");                
                    if($count==0)
                    {

                        $sql = "
                        update grmaster 
                        set
                        doc_status = 'ชั่งสินค้าครบแล้ว',
                        updated_date = CURRENT_TIMESTAMP()
                        where grcode = :grcode ";

                        $stmt = $conn->prepare($sql);
                        if (!$stmt) throw new PDOException("Insert data error => {$conn->errorInfo()}");

                        $stmt->bindValue(":grcode", $_POST['grcode'], PDO::PARAM_STR);

                        if (!$stmt->execute()) {
                            $error = $conn->errorInfo();
                            throw new PDOException("Insert data error => $error");
                            die;
                        }
                    }
        
        // $conn->commit();
        if ($stmt->execute()) {
            http_response_code(200);
            $response = ['status' => 1, 'message' => 'เพิ่มข้อมูลสำเร็จ', 'barcode_id' => $conn->lastInsertId(), 'stcode' => $_POST['stcode'], 'grcode' => $_POST['grcode']];
        } else {
            $response = ['status' => 0, 'message' => 'Error! ติดต่อโปรแกรมเมอร์'];
        }
    } else {
        http_response_code(400);
        echo json_encode(array('status' => 2, 'message' => 'ข้อมูลซ้ำ'));
    }

echo json_encode($response);
