<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../conn.php';

// echo $_POST['barcode_id'];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "SELECT a.no, a.stcode,i.stname , a.grcode,a.unit_weight, a.barcode_status
    FROM tidmunzb_db.grbarcode a 
    left outer join items i on (a.stcode=i.stcode)
    left outer join grdetail d on (a.grcode=d.grcode)
    left outer join grmaster g on (a.grcode=g.grcode)
    left outer join supplier s on (g.supcode=s.supcode) 
    where a.grcode = '" . $_POST['grcode'] . "' and a.stcode = '" . $_POST['stcode'] . "' ";
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
