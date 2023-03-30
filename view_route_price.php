<?php
require_once("DBConnection.php");
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM `route_prices` where rp_id = '{$_GET['id']}'");
    foreach($qry->fetchArray() as $k => $v){
        $$k = $v;
    }
}
$location_arr=array();
$location_qry = $conn->query("SELECT * FROM `location_list` where ".(isset($rp_id) && $rp_id > 0 ? " location_id = '{$from_location_id}' or location_id = '{$to_location_id}' " : "" ). " order by `location` asc");
while($row = $location_qry->fetchArray()){
    $location_arr[$row['location_id']] = $row['location'];
}
?>
<style>
    #uni_modal .modal-footer{
        display:none !important;
    }
</style>
<div class="container-fluid">
    <div class="col-12">
        <div class="w-100 mb-1">
            <div class="fs-6"><b>Departure:</b></div>
            <div class="fs-5 ps-4"><?php echo isset($location_arr[$from_location_id]) ? $location_arr[$from_location_id] : '' ?></div>
        </div>
        <div class="w-100 mb-1">
            <div class="fs-6"><b>Destination:</b></div>
            <div class="fs-5 ps-4"><?php echo isset($location_arr[$to_location_id]) ? $location_arr[$to_location_id] : '' ?></div>
        </div>
        <div class="w-100 mb-1">
            <div class="fs-6"><b>Normal Price:</b></div>
            <div class="fs-6 ps-4"><?php echo isset($price) ? number_format($price,2) : '' ?></div>
        </div>
        <div class="w-100 mb-1">
            <div class="fs-6"><b>Student Price:</b></div>
            <div class="fs-6 ps-4"><?php echo isset($student_price) ? number_format($student_price,2) : '' ?></div>
        </div>
        <div class="w-100 mb-1">
            <div class="fs-6"><b>Senior Price:</b></div>
            <div class="fs-6 ps-4"><?php echo isset($senior_price) ? number_format($senior_price,2) : '' ?></div>
        </div>
        <div class="w-100 mb-1">
            <div class="fs-6"><b>Status:</b></div>
            <div class="fs-5 ps-4">
                <?php 
                    if(isset($status) && $status == 1){
                        echo "<small><span class='badge rounded-pill bg-success'>Active</span></small>";
                    }else{
                        echo "<small><span class='badge rounded-pill bg-danger'>Inactive</span></small>";
                    }
                ?>
            </div>
        </div>
        <div class="w-100 d-flex justify-content-end">
            <button class="btn btn-sm btn-dark rounded-0" type="button" data-bs-dismiss="modal">Close</button>
        </div>
    </div>
</div>