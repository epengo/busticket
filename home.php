<h3>Welcome to Bus Station Ticketing Booth System</h3>
<hr>
<div class="col-12">
    <div class="row gx-3 row-cols-4">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="w-100 d-flex align-items-center">
                        <div class="col-auto pe-1">
                            <span class="fa fa-map-marked-alt fs-3 text-primary"></span>
                        </div>
                        <div class="col-auto flex-grow-1">
                            <div class="fs-5"><b>Locations</b></div>
                            <div class="fs-6 text-end fw-bold">
                                <?php 
                                $location = $conn->query("SELECT count(location_id) as `count` FROM `location_list` where `status` = 1")->fetchArray()['count'];
                                echo $location > 0 ? number_format($location) : 0 ;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
    })
</script>