<?php
session_start();
require_once("DBConnection.php");
$location_arr=array();
$location_qry = $conn->query("SELECT * FROM `location_list` where location_id in (SELECT from_location_id FROM `route_prices` where rp_id in (SELECT rp_id from ticket_list where ticket_id in ({$_POST['ids']}))) or  location_id in (SELECT to_location_id FROM `route_prices` where rp_id in (SELECT rp_id from ticket_list where ticket_id in ({$_POST['ids']}))) order by `location` asc");
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
    <div id="outprint_receipt">
        <div class="row row-cols-sm-1 row-cols-md-2 row-cols-xl-2 gx-2 gy-3 justify-content-center">
            <?php 
            $qry = $conn->query("SELECT t.*,r.from_location_id, r.to_location_id FROM `ticket_list` t inner join `route_prices` r on t.rp_id = r.rp_id where t.ticket_id in ({$_POST['ids']})");
            while($row= $qry->fetchArray()):
            ?>
            <div class="col-12">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column justify-conten-between">
                        <div class="text-info"><?php echo $row['ticket_no'] ?></div>
                        <span class="text-muted">Route:</span>
                        <div><?php echo $location_arr[$row['from_location_id']] . ' - ' .$location_arr[$row['to_location_id']] ?></div>
                        <span class="text-muted"><?php echo  $row['passenger_type'] != "Normal" ? $row['passenger_type'] : "" ?></span>
                        <div class="w-100 text-end fw-bold"><?php echo number_format($row['price'],2) ?></div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <div class="w-100 d-flex justify-content-end mt-2">
        <?php if(isset($_GET['view_only']) && $_GET['view_only']): ?>
        <button class="btn btn-sm btn-danger me-2 rounded-0" type="button" id="delete_tickets"><i class="fa fa-trash"></i> Delete</button>
        <?php endif; ?>
        <button class="btn btn-sm btn-success me-2 rounded-0" type="button" id="print_receipt"><i class="fa fa-print"></i> Print</button>
        <button class="btn btn-sm btn-dark rounded-0" type="button" data-bs-dismiss="modal">Close</button>
    </div>
</div>
<script>
    $(function(){
        $("#print_receipt").click(function(){
            var h = $('head').clone()
            var p = $('#outprint_receipt').clone()
            var el = $('<div>')
            el.append(h)
            el.append(p)
            var nw = window.open("","","width=1200,height=900,left=150")
                nw.document.write(el.html())
                nw.document.close()
                setTimeout(() => {
                    nw.print()
                    setTimeout(() => {
                        nw.close()

                        $('#uni_modal').on('hide.bs.modal',function(){
                            if($(this).find('#outprint_receipt').length > 0 && '<?php echo !isset($_GET['view_only']) ?>' == 1){
                                location.reload()
                            }
                        })
                        if('<?php echo !isset($_GET['view_only']) ?>' == 1)
                        $('#uni_modal').modal('hide')
                    }, 150);
                }, 250);
        })
        $('#uni_modal').on('hide.bs.modal',function(){
            if($(this).find('#outprint_receipt').length > 0){
                location.reload()
            }
        })
        $('#uni_modal').modal('hide')
        $('#delete_tickets').click(function(){
            _conf("Are you sure to delete this ticket?",'delete_data',['<?php echo $_POST['ids'] ?>'])
        })
       
    })
    function delete_data($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'./Actions.php?a=delete_transaction',
            method:'POST',
            data:{ids:$id},
            dataType:'JSON',
            error:err=>{
                console.log(err)
                alert("An error occurred.")
                $('#confirm_modal button').attr('disabled',false)
            },
            success:function(resp){
                if(resp.status == 'success'){
                    location.reload()
                }else{
                    alert("An error occurred.")
                    $('#confirm_modal button').attr('disabled',false)
                }
            }
        })
    }
</script>