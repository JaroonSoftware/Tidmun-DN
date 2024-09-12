<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
$sql = "SELECT d.dncode,a.socode,a.sodate,c.cuscode,c.cusname,c.taxnumber,c.idno, c.road, c.subdistrict, c.district, c.province, c.zipcode 
FROM dnmaster as d LEFT JOIN `somaster` as a on d.socode = a.socode LEFT JOIN customer as c on a.cuscode = c.cuscode ";
$sql .= " where d.dncode = '" . $_POST['dncode'] . "' ";
// echo $sql;
$stmt = $conn->prepare($sql);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);

http_response_code(200);
echo json_encode($data);
} else {
    http_response_code(400);
    echo json_encode(array('status' => '0', 'message' => 'request method fail.'));
}