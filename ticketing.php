<?php 
$location_arr=array();
$routes_arr=array();
$location_qry = $conn->query("SELECT * FROM `location_list` where location_id in (SELECT from_location_id FROM `route_prices` where status = '1') or location_id in (SELECT to_location_id FROM `route_prices` where status = '1')  order by `location` asc");
while($row = $location_qry->fetchArray()){
    $location_arr[$row['location_id']] = $row['location'];
}
$routes_qry= $conn->query("SELECT * FROM `route_prices` where status = 1");
while($row = $routes_qry->fetchArray()){
    $row['name'] = isset($location_arr[$row['to_location_id']]) ? $location_arr[$row['to_location_id']] : '';
    $routes_arr[$row['from_location_id']][$row['to_location_id']] = $row;
}
?>
<div class="w-100 h-100 d-flex flex-column">
    <div class="row">
        <div class="col-8">
            <h3>Ticketing</h3>
        </div>
        <div class="col-4 d-flex justify-content-end">
            <button class="btn btn-sm btn-primary rounded-0 " id="transaction-save-btn" form="transaction-form">Save</button>
        </div>
        <div class="clear-fix mb-1"></div>
        <hr>
    </div>
    <style>
        #plist .item,#item-list tr{
            cursor:pointer
        }
        .petrol-item{
            transition: transform 10s easein;
        }
        .petrol-item:hover{
            transform:scale(.98);
        }
    </style>
    <div class="card">
        <div class="card-body">
            <form action="" class="h-100" id="transaction-form">
                <input type="hidden" name="rp_id" value="">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="from_location_id" class="control-label">Departure Location</label>
                            <select name="from_location_id" id="from_location_id" class="form-select form-select-sm select2" data-placeholder="Please Select Location Here">
                                <option value="" disabled <?php echo !isset($id) ? 'selected' : '' ?>></option>
                                <?php
                                foreach($location_arr as $k  => $v):
                                ?>
                                <option value="<?php echo $k ?>" <?php echo isset($from_location_id) && $from_location_id == $k ? "selected" : '' ?>><?php echo $v ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="to_location_id" class="control-label">Destination</label>
                            <select name="to_location_id" id="to_location_id" class="form-select form-select-sm select2" data-placeholder="Please Select Location Here">
                                <option value="" disabled <?php echo !isset($id) ? 'selected' : '' ?>></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row row-cols-3 gx-2 gy-2 aling-items-center">
                            <div class="col d-flex flex-column justify-content-center">
                                <div class="align-middle"><label for="" class="control-label">Normal</label></div>
                            </div>
                            <div class="col d-flex flex-column justify-content-center">
                                <input type="hidden" name="type[]" value="Normal">
                                <input type="hidden" name="price[]" value="0">
                                <div class="text-end"><span id="normal_price">0</span></div>
                            </div>
                            <div class="col d-flex flex-column justify-content-center">
                                <input type="number" name="pax[]" value="0" class="form-control rounded-0" data-name="normal_price">
                            </div>
                            <div class="col d-flex flex-column justify-content-center">
                                <div class="align-middle"><label for="" class="control-label">Student</label></div>
                            </div>
                            <div class="col d-flex flex-column justify-content-center">
                                <input type="hidden" name="type[]" value="Student">
                                <input type="hidden" name="price[]" value="0">
                                <div class="text-end"><span id="student_price">0</span></div>
                            </div>
                            <div class="col d-flex flex-column justify-content-center">
                                <input type="number" name="pax[]" value="0" class="form-control rounded-0" data-name="student_price">
                            </div>
                            <div class="col d-flex flex-column justify-content-center">
                                <div class="align-middle"><label for="" class="control-label">Senior</label></div>
                            </div>
                            <div class="col d-flex flex-column justify-content-center">
                                <input type="hidden" name="type[]" value="Senior">
                                <input type="hidden" name="price[]" value="0">
                                <div class="text-end"><span id="senior_price">0</span></div>
                            </div>
                            <div class="col d-flex flex-column justify-content-center">
                                <input type="number" name="pax[]" value="0" class="form-control rounded-0" data-name="senior_price">
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="my-2 row justify-content-center">
                    <div class="col-md-2">
                        <label for="total" class="control-label">Total</label>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control text-end" id="total" value="0" disabled>
                    </div>
                </div>
                <div class="my-2 row justify-content-center">
                    <div class="col-md-2">
                        <label for="tendered_amount" class="control-label">Tendered Amount</label>
                    </div>
                    <div class="col-md-3">
                        <input type="number" step="any" class="form-control text-end" id="tendered_amount" value="0">
                    </div>
                </div>
                <div class="my-2 row justify-content-center">
                    <div class="col-md-2">
                        <label for="change" class="control-label">Change</label>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control text-end" id="change" value="0">
                    </div>
                </div>
                
            </form>
        </div>
    </div>
</div>
<script>
    var locations = $.parseJSON('<?php echo json_encode($location_arr) ?>') || {};
    var routes = $.parseJSON('<?php echo json_encode($routes_arr) ?>') || {};
    $(function(){
        $('.select2').select2({
            width:'100%',
        })
        if('<?php echo isset($_SESSION['type']) && $_SESSION['type'] != 1 ?>' == 1){
            $('#from_location_id').select2('destroy')
            $('#from_location_id option[value="<?php echo $_SESSION['location_id'] ?>"]').attr('selected',true)
            $('#from_location_id').attr('disabled',true)
            $('#from_location_id').trigger('change')
            $('#from_location_id').attr('name','')
            $('#from_location_id').after('<input type="hidden" name="from_location_id" value="<?php echo $_SESSION['location_id'] ?>">')

            var  fid = "<?php echo $_SESSION['location_id'] ?>"
            $('#to_location_id').select2('destroy')
            $('#to_location_id').html('')
            var opt = $('<option>')
            opt.text('')
            opt.attr({disable:true,selected:true})
            $('#to_location_id').append(opt)
            if(!!routes[fid]){
                Object.keys(routes[fid]).map(k=>{
                    if(routes[fid][k].location_id != fid){
                    var opt = $('<option>')
                    opt.text(routes[fid][k].name)
                    opt.val(routes[fid][k].to_location_id)
                    $('#to_location_id').append(opt)
                    }
                })
            }
            $('#to_location_id').select2({
                width:'100%',
            })
            $('#to_location_id').trigger('change')
        }
        $('#from_location_id').change(function(){
            var  fid = $(this).val()
            $('#to_location_id').select2('destroy')
            $('#to_location_id').html('')
            var opt = $('<option>')
            opt.text('')
            opt.attr({disable:true,selected:true})
            $('#to_location_id').append(opt)
            if(!!routes[fid]){
                Object.keys(routes[fid]).map(k=>{
                    if(routes[fid][k].location_id != fid){
                    var opt = $('<option>')
                    opt.text(routes[fid][k].name)
                    opt.val(routes[fid][k].to_location_id)
                    $('#to_location_id').append(opt)
                    }
                })
            }
            $('#to_location_id').select2({
                width:'100%',
            })
            $('#to_location_id').trigger('change')
        })
        $('#to_location_id').change(function(){
            var  fid = $('#from_location_id').val()
            var  tid = $(this).val()
            if(!!routes[fid] && !!routes[fid][tid]){
                var data = routes[fid][tid];
                $("[name='rp_id']").val(data.rp_id)
                $('#normal_price').text(parseFloat(data.price).toLocaleString('en-US',{style:'decimal',minimumFractionDigits:2,maximumFractionDigits:2}))
                $('#normal_price').closest('.col').find('input[name="price[]"]').val(data.price)
                
                $('#student_price').text(parseFloat(data.student_price).toLocaleString('en-US',{style:'decimal',minimumFractionDigits:2,maximumFractionDigits:2}))
                $('#student_price').closest('.col').find('input[name="price[]"]').val(data.student_price)
                
                $('#senior_price').text(parseFloat(data.senior_price).toLocaleString('en-US',{style:'decimal',minimumFractionDigits:2,maximumFractionDigits:2}))
                $('#senior_price').closest('.col').find('input[name="price[]"]').val(data.senior_price)
            }else{
                $("[name='rp_id']").val('')
                $('#normal_price').text(0.00)
                $('#normal_price').closest('.col').find('input[name="price[]"]').val(0)
                
                $('#student_price').text(0.00)
                $('#student_price').closest('.col').find('input[name="price[]"]').val(0)
                
                $('#senior_price').text(0.00)
                $('#senior_price').closest('.col').find('input[name="price[]"]').val(0)
            }
        })
        $('input[name="pax[]"]').on('input',function(){
            var total = 0;
            $('input[name="pax[]"]').each(function(){
                var pax = $(this).val()
                    pax = pax > 0 ? pax : 0;
                var price = $("#"+$(this).attr('data-name')).closest('.col').find("input[name='price[]']").val()
                    price = price > 0 ? price : 0;

                var amount = parseFloat(pax) * parseFloat(price)
                total += parseFloat(amount)
            })
            $('#total').val(parseFloat(total).toLocaleString('en-US',{style:'decimal',minimumFractionDigits:2,maximumFractionDigits:2}))
        })
        $('#tendered_amount').on('input',function(){
            var total = $('#total').val()
                total = total.replace(/\,/gi,'')
                total = total > 0 ? total : 0;
            var tendered = $(this).val()
            tendered = tendered > 0 ? tendered : 0;
            var change = total - tendered;
            $('#change').val(change)
        })
        $('#transaction-form').submit(function(e){
            e.preventDefault()
            if($('#total').val() <= 0){
                alert("Please Enter Pax First.")
                return false;
            }
            if($('#tendered_amount').val() <= 0){
                alert("Tendered Amount is invalid.")
                $('#tendered_amount').focus()
                return false;
            }
            if($('#change').val() < 0){
                alert("Tendered Amount is invalid.")
                $('#tendered_amount').focus()
                return false;
            }
            
            $('#transaction-save-btn').attr('disabled',true)
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            $.ajax({
                url:'./Actions.php?a=save_transaction',
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
                    $('#transaction-save-btn').attr('disabled',false)
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        setTimeout(() => {
                            uni_modal("Tickets","view_tickets.php",'mid-large',{ids:resp.ids})
                        }, 1000);
                    }else{
                        _el.addClass('alert alert-danger')
                    }
                    _el.text(resp.msg)

                    _el.hide()
                    _this.prepend(_el)
                    _el.show('slow')
                    $('#transaction-save-btn').attr('disabled',false)
                }
            })
        })
       
    })
</script>