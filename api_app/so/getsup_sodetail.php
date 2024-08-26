<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
$sql = "SELECT b.socode,b.stcode,i.stname,b.qty,b.price,b.unit,b.delamount,ROUND(b.qty*b.price,2) as totalprice FROM `somaster` as a INNER JOIN sodetail as b on a.socode= b.socode left join items as i on b.stcode = i.stcode ";
$sql .= " where a.socode = '" . $_POST['socode'] . "' ";
// echo $sql;
$stmt = $conn->prepare($sql);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

http_response_code(200);
echo json_encode($data);
} else {
    http_response_code(400);
    echo json_encode(array('status' => '0', 'message' => 'request method fail.'));
}