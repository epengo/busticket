<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">Routes Price List</h3>
        <div class="card-tools align-middle">
            <button class="btn btn-dark btn-sm py-1 rounded-0" type="button" id="create_new">Add New</button>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-hover table-striped table-bordered">
            <colgroup>
                <col width="5%">
                <col width="20%">
                <col width="20%">
                <col width="35%">
                <col width="10%">
                <col width="10%">
            </colgroup>
            <thead>
                <tr>
                    <th class="text-center p-0">#</th>
                    <th class="text-center p-0">Location From</th>
                    <th class="text-center p-0">Location To</th>
                    <th class="text-center p-0">Price</th>
                    <th class="text-center p-0">Status</th>
                    <th class="text-center p-0">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $location = $conn->query("SELECT * FROM location_list where location_id in (SELECT from_location_id from `route_prices`) or location_id in (SELECT to_location_id from `route_prices`)");
                $location_arr = array();
                while($row = $location->fetchArray()){
                    $location_arr[$row['location_id']] = $row['location'];
                }
                $sql = "SELECT * FROM `route_prices` order by `rp_id` asc";
                $qry = $conn->query($sql);
                $i = 1;
                    while($row = $qry->fetchArray()):
                ?>
                <tr>
                    <td class="text-center p-1"><?php echo $i++; ?></td>
                    <td class="py-1 px-2"><?php echo $location_arr[$row['from_location_id']] ?></td>
                    <td class="py-1 px-2"><?php echo $location_arr[$row['to_location_id']] ?></td>
                    <td class="py-1 px-2">
                        <div class="lh-1">
                            <small><span class="text-muted">Normal:</span> <span><?php echo number_format($row['price'],2) ?></span></small><br>
                            <small><span class="text-muted">Student:</span> <span><?php echo number_format($row['student_price'],2) ?></span></small><br>
                            <small><span class="text-muted">Senior:</span> <span><?php echo number_format($row['senior_price'],2) ?></span></small><br>
                        </div>
                    </td>
                    <td class="py-1 px-2 text-center">
                        <?php 
                        if($row['status'] == 1){
                            echo  '<span class="py-1 px-3 badge rounded-pill bg-success"><small>Active</small></span>';
                        }else{
                            echo  '<span class="py-1 px-3 badge rounded-pill bg-danger"><small>Inactive</small></span>';

                        }
                        ?>
                    </td>
                    <td class="text-center py-1 px-2">
                        <div class="btn-group" role="group">
                            <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle btn-sm rounded-0 py-0" data-bs-toggle="dropdown" aria-expanded="false">
                            Action
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                            <li><a class="dropdown-item view_data" data-id = '<?php echo $row['rp_id'] ?>' href="javascript:void(0)">View Details</a></li>
                            <li><a class="dropdown-item edit_data" data-id = '<?php echo $row['rp_id'] ?>' href="javascript:void(0)">Edit</a></li>
                            <li><a class="dropdown-item delete_data" data-id = '<?php echo $row['rp_id'] ?>' data-name = '<?php echo $location_arr[$row['from_location_id']]." - ".$location_arr[$row['to_location_id']] ?>' href="javascript:void(0)">Delete</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
               
            </tbody>
        </table>
    </div>
</div>
<script>
    $(function(){
        $('#create_new').click(function(){
            uni_modal('Add New Route Price',"manage_route_price.php",'mid-large')
        })
        $('.edit_data').click(function(){
            uni_modal('Edit Route Price Details',"manage_route_price.php?id="+$(this).attr('data-id'),'mid-large')
        })
        $('.view_data').click(function(){
            uni_modal('Route Price Details',"view_route_price.php?id="+$(this).attr('data-id'),'')
        })
        $('.delete_data').click(function(){
            _conf("Are you sure to delete <b>"+$(this).attr('data-name')+"</b> from Route Price List?",'delete_data',[$(this).attr('data-id')])
        })
        $('table td,table th').addClass('align-middle')
        $('table').dataTable({
            columnDefs: [
                { orderable: false, targets:3 }
            ]
        })
    })
    function delete_data($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'./Actions.php?a=delete_route_price',
            method:'POST',
            data:{id:$id},
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