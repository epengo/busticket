<?php
require_once("DBConnection.php");
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM `route_prices` where rp_id = '{$_GET['id']}'");
    foreach($qry->fetchArray() as $k => $v){
        $$k = $v;
    }
}
$location_arr=array();
$location_qry = $conn->query("SELECT * FROM `location_list` where `status` = 1".(isset($rp_id) && $rp_id > 0 ? " or location_id = '{$from_location_id}' or location_id = '{$to_location_id}' " : "" ). " order by `location` asc");
while($row = $location_qry->fetchArray()){
    $location_arr[$row['location_id']] = $row;
}
?>
<div class="container-fluid">
    <form action="" id="route_price-form">
        <input type="hidden" name="id" value="<?php echo isset($rp_id) ? $rp_id : '' ?>">
        <div class="col-12">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="from_location_id" class="control-label">Depart Location</label>
                        <select name="from_location_id" id="from_location_id" class="form-select form-select-sm select2" data-placeholder="Please Select Location Here">
                            <option value="" disabled <?php echo !isset($id) ? 'selected' : '' ?>></option>
                            <?php
                            foreach($location_arr as $k  => $v):
                            ?>
                            <option value="<?php echo $k ?>" <?php echo isset($from_location_id) && $from_location_id == $k ? "selected" : '' ?>><?php echo $v['location'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="to_location_id" class="control-label">Destination</label>
                        <select name="to_location_id" id="to_location_id" class="form-select form-select-sm select2" data-placeholder="Please Select Location Here">
                            <option value="" disabled <?php echo !isset($id) ? 'selected' : '' ?>></option>
                            <?php
                            foreach($location_arr as $k  => $v):
                                if(isset($from_location_id) && $from_location_id == $k)
                                continue;
                            ?>
                            <option value="<?php echo $k ?>" <?php echo isset($to_location_id) && $to_location_id == $k ? "selected" : '' ?>><?php echo $v['location'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="price" class="control-label">Normal Price</label>
                        <input type="number" step="any" name="price"  id="price" required class="form-control form-control-sm rounded-0 text-end" value="<?php echo isset($price) ? $price : 0 ?>">
                    </div>
                    <div class="form-group">
                        <label for="student_price" class="control-label">Student Price</label>
                        <input type="number" step="any" name="student_price"  id="student_price" required class="form-control form-control-sm rounded-0 text-end" value="<?php echo isset($student_price) ? $student_price : 0 ?>">
                    </div>
                    <div class="form-group">
                        <label for="senior_price" class="control-label">Senior Price</label>
                        <input type="number" step="any" name="senior_price"  id="senior_price" required class="form-control form-control-sm rounded-0 text-end" value="<?php echo isset($senior_price) ? $senior_price : 0 ?>">
                    </div>
                    <div class="form-group">
                        <label for="status" class="control-label">Status</label>
                        <select name="status" id="status" class="form-select form-select-sm rounded-0" required>
                            <option value="1" <?php echo isset($status) && $status == 1 ? 'selected' : '' ?>>Active</option>
                            <option value="0" <?php echo isset($status) && $status == 0 ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    var locations = $.parseJSON('<?php echo json_encode($location_arr) ?>') || {};
    $(function(){
        $('.select2').select2({
            width:'100%',
            dropdownParent:$('#uni_modal')
        })
        $('#from_location_id').change(function(){
            var  fid = $(this).val()
            $('#to_location_id').select2('destroy')
            $('#to_location_id').html('')
            var opt = $('<option>')
            opt.text('')
            opt.attr({disable:true,selected:true})
            $('#to_location_id').append(opt)
            Object.keys(locations).map(k=>{
                if(locations[k].location_id != fid){
                var opt = $('<option>')
                opt.text(locations[k].location)
                opt.val(locations[k].location_id)
                $('#to_location_id').append(opt)
                }
            })
            $('#to_location_id').select2({
                width:'100%',
                dropdownParent:$('#uni_modal')
            })
        })
        $('#route_price-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            $('#uni_modal button').attr('disabled',true)
            $('#uni_modal button[type="submit"]').text('submitting form...')
            $.ajax({
                url:'./Actions.php?a=save_route_price',
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
                error:err=>{
                    console.log(err)
                    _el.addClass('alert alert-danger')
                    _el.text("An error occurred.")
                    _this.prepend(_el)
                    _el.show('slow')
                     $('#uni_modal button').attr('disabled',false)
                     $('#uni_modal button[type="submit"]').text('Save')
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        _el.addClass('alert alert-success')
                        $('#uni_modal').on('hide.bs.modal',function(){
                            location.reload()
                        })
                        if("<?php echo isset($rp_id) ?>" != 1)
                        _this.get(0).reset();
                        $('.select2').trigger('change')
                    }else{
                        _el.addClass('alert alert-danger')
                    }
                    _el.text(resp.msg)

                    _el.hide()
                    _this.prepend(_el)
                    _el.show('slow')
                     $('#uni_modal button').attr('disabled',false)
                     $('#uni_modal button[type="submit"]').text('Save')
                }
            })
        })
    })
</script>