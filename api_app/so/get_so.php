<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Bangkok');

include '../conn.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    

    extract($_POST, EXTR_OVERWRITE, "_"); 
    
    $socode = !empty($socode) ? "and a.socode like '%$socode%'" : "";
    $cuscode = !empty($cuscode) ? "and c.cuscode like '%$cuscode%'" : "";
    $cusname = !empty($cusname) ? "and c.cusname like '%$cusname%'" : "";
    
    $sodate = "";
    $sql = " 
        select 
        a.*,
        c.*
        from somaster a        
        left join customer c on (a.cuscode = c.cuscode)
        where a.active_status = 'Y' and doc_status = 'รอออกใบส่งของ'
        $socode
        $cuscode
        $cusname
        $sodate
        order by a.created_date desc ;";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    echo json_encode($data);
    // echo json_encode(array('status' => '1', 'data' => $data, 'sql' => str_replace(' ', '', $sql)));
} else {
    http_response_code(400);
    echo json_encode(array('status' => '0', 'message' => 'request method fail.'));
}
