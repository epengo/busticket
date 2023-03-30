<?php
require_once("DBConnection.php");
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM `user_list` where user_id = '{$_GET['id']}'");
    foreach($qry->fetchArray() as $k => $v){
        $$k = $v;
    }
}
$location_arr=array();
$location_qry = $conn->query("SELECT * FROM `location_list` where `status` = 1".(isset($location_id) && $location_id > 0 ? " or location_id = '{$location_id}' " : "" ). " order by `location` asc");
while($row = $location_qry->fetchArray()){
    $location_arr[$row['location_id']] = $row;
}
?>
<div class="container-fluid">
    <form action="" id="user-form">
        <input type="hidden" name="id" value="<?php echo isset($user_id) ? $user_id : '' ?>">
        <div class="form-group">
            <label for="fullname" class="control-label">Full Name</label>
            <input type="text" name="fullname" id="fullname" required class="form-control form-control-sm rounded-0" value="<?php echo isset($fullname) ? $fullname : '' ?>">
        </div>
        <div class="form-group">
            <label for="username" class="control-label">Username</label>
            <input type="text" name="username" id="username" required class="form-control form-control-sm rounded-0" value="<?php echo isset($username) ? $username : '' ?>">
        </div>
        <div class="form-group">
            <label for="type" class="control-label">Type</label>
            <select name="type" id="type" class="form-select form-select-sm rounded-0" required>
                <option value="1" <?php echo isset($type) && $type == 1 ? 'selected' : '' ?>>Administrator</option>
                <option value="0" <?php echo isset($type) && $type == 2 ? 'selected' : '' ?>>Cashier</option>
            </select>
        </div>
        <div class="form-group" style="display:none">
            <label for="location_id" class="control-label">Location</label>
            <select name="location_id" id="location_id" class="form-select form-select-sm select2" data-placeholder="Please Select Location Here">
                <option value="" disabled <?php echo !isset($id) ? 'selected' : '' ?>></option>
                <?php
                foreach($location_arr as $k  => $v):
                ?>
                <option value="<?php echo $k ?>" <?php echo isset($location_id) && $location_id == $k ? "selected" : '' ?>><?php echo $v['location'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
</div>

<script>
    $(function(){
        $('.select2').select2({
            width:"100%",
            dropdownParent:$('#uni_modal')
        })
        $('#type').change(function(){
            var type = $(this).val()
            if(type == 1){
                $('#location_id').parent().hide('slow')
                $('#location_id').attr('required',false)
            }else{
                $('#location_id').attr('required',true)
                $('#location_id').parent().show('slow')
            }
        })
        $('#user-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            $('#uni_modal button').attr('disabled',true)
            $('#uni_modal button[type="submit"]').text('submitting form...')
            $.ajax({
                url:'./Actions.php?a=save_user',
                method:'POST',
                data:$(this).serialize(),
                dataType:'JSON',
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
                        if("<?php echo ISSET($user_id) ?>" != 1)
                        _this.get(0).reset();
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