<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../conn.php';

// echo $_POST['barcode_id'];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
$sql = "SELECT a.barcode_id, a.stcode,i.stname , a.grcode, a.barcode_status, a.socode, a.dncode, a.created_date, a.updated_date 
FROM tidmunzb_db.items_barcode a 
left outer join items i on (a.stcode=i.stcode)
left outer join grmaster g on (a.grcode=g.grcode)
left outer join supplier s on (g.supcode=s.supcode) 
left outer join somaster so on (a.socode = so.socode)
left outer join customer c on (so.cuscode=c.cuscode)";
$sql .= " where a.barcode_id = '" . $_POST['barcode_id'] . "' ";
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