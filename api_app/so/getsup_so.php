<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
$sql = "SELECT a.socode,a.sodate,s.cuscode,s.cusname FROM `somaster` as a LEFT JOIN customer as s on a.cuscode = s.cuscode ";
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