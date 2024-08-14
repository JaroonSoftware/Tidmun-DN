<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Bangkok');

include '../conn.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $grcode = !empty($grcode) ? "and a.grcode like '%$grcode%'" : "";
    $supcode = !empty($supcode) ? "and c.supcode like '%$supcode%'" : "";
    $supname = !empty($supname) ? "and c.supname like '%$supname%'" : "";
    $created_by = !empty($created_by) ? "and ( u.firstname like '%$created_by%' or u.lastname like '%$created_by%' )" : "";
    $grdate = "";
    $sql = " 
        select 
        a.*,
        c.*,
        concat(u.firstname, ' ', u.lastname) created_name
        from grmaster a        
        left join supplier c on (a.supcode = c.supcode)
        left join `user` u on (a.created_by = u.user_code)
        where a.active_status = 'Y' and doc_status = 'รอชั่งสินค้า'
        $grcode
        $supcode
        $supname
        $created_by
        $grdate
        order by a.created_date desc ;";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    echo json_encode($data);
} else {
    http_response_code(400);
    echo json_encode(array('status' => '0', 'message' => 'request method fail.'));
}
