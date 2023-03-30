<?php 
session_start();
require_once('DBConnection.php');

Class Actions extends DBConnection{
    function __construct(){
        parent::__construct();
    }
    function __destruct(){
        parent::__destruct();
    }
    function login(){
        extract($_POST);
        $sql = "SELECT * FROM user_list where username = '{$username}' and `password` = '".md5($password)."' ";
        @$qry = $this->query($sql)->fetchArray();
        if(!$qry){
            $resp['status'] = "failed";
            $resp['msg'] = "Invalid username or password.";
        }else{
            $resp['status'] = "success";
            $resp['msg'] = "Login successfully.";
            foreach($qry as $k => $v){
                if(!is_numeric($k))
                $_SESSION[$k] = $v;
            }
        }
        return json_encode($resp);
    }
    function logout(){
        session_destroy();
        header("location:./");
    }
    function save_user(){
        $_POST['location_id'] = $_POST['type'] == 1 ? null: $_POST['location_id'];
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
        if(!in_array($k,array('id'))){
            if(!empty($id)){
                if(!empty($data)) $data .= ",";
                $data .= " `{$k}` = '{$v}' ";
                }else{
                    $cols[] = $k;
                    $values[] = "'{$v}'";
                }
            }
        }
        if(empty($id)){
            $cols[] = 'password';
            $values[] = "'".md5($username)."'";
        }
        if(isset($cols) && isset($values)){
            $data = "(".implode(',',$cols).") VALUES (".implode(',',$values).")";
        }
        

       
        @$check= $this->query("SELECT count(user_id) as `count` FROM user_list where `username` = '{$username}' ".($id > 0 ? " and user_id != '{$id}' " : ""))->fetchArray()['count'];
        if(@$check> 0){
            $resp['status'] = 'failed';
            $resp['msg'] = "Username already exists.";
        }else{
            if(empty($id)){
                $sql = "INSERT INTO `user_list` {$data}";
            }else{
                $sql = "UPDATE `user_list` set {$data} where user_id = '{$id}'";
            }
            @$save = $this->query($sql);
            if($save){
                $resp['status'] = 'success';
                if(empty($id))
                $resp['msg'] = 'New User successfully saved.';
                else
                $resp['msg'] = 'User Details successfully updated.';
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Saving User Details Failed. Error: '.$this->lastErrorMsg();
                $resp['sql'] =$sql;
            }
        }
        return json_encode($resp);
    }
    function delete_user(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `user_list` where rowid = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'User successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function update_credentials(){
        extract($_GET);
        $data = "";
        foreach($_GET as $k => $v){
            if(!in_array($k,array('id','conf_password','a')) && !empty($v)){
                if(!empty($data)) $data .= ",";
                if($k == 'password') $v = md5($v);
                $data .= " `{$k}` = '{$v}' ";
            }
        }
        if(!empty($password) && $conf_password != $password){
            $resp['status'] = 'failed';
            $resp['msg'] = "Confirmation password is incorrect.";
        }else{
            $sql = "UPDATE `user_list` set {$data} where user_id = '{$_SESSION['user_id']}'";
            @$save = $this->query($sql);
            if($save){
                $resp['status'] = 'success';
                $_SESSION['flashdata']['type'] = 'success';
                $_SESSION['flashdata']['msg'] = 'Credential successfully updated.';
                foreach($_POST as $k => $v){
                    if(!in_array($k,array('id','conf_password')) && !empty($v)){
                        if(!empty($data)) $data .= ",";
                        if($k == 'password') $v = md5($v);
                        $_SESSION[$k] = $v;
                    }
                }
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Updating Credentials Failed. Error: '.$this->lastErrorMsg();
                $resp['sql'] =$sql;
            }
        }
        return json_encode($resp);
    }
    function save_bus(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id'))){
            if(empty($id)){
                $cols[] = "`{$k}`";
                $vals[] = "'{$v}'";
            }else{
                if(!empty($data)) $data .= ", ";
                $data .= " `{$k}` = '{$v}' ";
            }
            }
        }
        if(isset($cols) && isset($vals)){
            $cols_join = implode(",",$cols);
            $vals_join = implode(",",$vals);
        }
        if(empty($id)){
            $sql = "INSERT INTO `bus_list` ({$cols_join}) VALUES ($vals_join)";
        }else{
            $sql = "UPDATE `bus_list` set {$data} where bus_id = '{$id}'";
        }
        @$check= $this->query("SELECT COUNT(bus_id) as count from `bus_list` where `name` = '{$name}' ".($id > 0 ? " and bus_id != '{$id}'" : ""))->fetchArray()['count'];
        if(@$check> 0){
            $resp['status'] ='failed';
            $resp['msg'] = 'Bus already exists.';
        }else{
            @$save = $this->query($sql);
            if($save){
                $resp['status']="success";
                if(empty($id))
                    $resp['msg'] = "Bus successfully saved.";
                else
                    $resp['msg'] = "Bus successfully updated.";
            }else{
                $resp['status']="failed";
                if(empty($id))
                    $resp['msg'] = "Saving New Bus Failed.";
                else
                    $resp['msg'] = "Updating Bus Failed.";
                $resp['error']=$this->lastErrorMsg();
            }
        }
        return json_encode($resp);
    }
    function delete_bus(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `bus_list` where bus_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Bus successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_location(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id'))){
            if(empty($id)){
                $cols[] = "`{$k}`";
                $vals[] = "'{$v}'";
            }else{
                if(!empty($data)) $data .= ", ";
                $data .= " `{$k}` = '{$v}' ";
            }
            }
        }
        if(isset($cols) && isset($vals)){
            $cols_join = implode(",",$cols);
            $vals_join = implode(",",$vals);
        }
        if(empty($id)){
            $sql = "INSERT INTO `location_list` ({$cols_join}) VALUES ($vals_join)";
        }else{
            $sql = "UPDATE `location_list` set {$data} where location_id = '{$id}'";
        }
        @$check= $this->query("SELECT COUNT(location_id) as count from `location_list` where `location` = '{$location}' ".($id > 0 ? " and location_id != '{$id}'" : ""))->fetchArray()['count'];
        if(@$check> 0){
            $resp['status'] ='failed';
            $resp['msg'] = 'Location already exists.';
        }else{
            @$save = $this->query($sql);
            if($save){
                $resp['status']="success";
                if(empty($id))
                    $resp['msg'] = "Location successfully saved.";
                else
                    $resp['msg'] = "Location successfully updated.";
            }else{
                $resp['status']="failed";
                if(empty($id))
                    $resp['msg'] = "Saving New Location Failed.";
                else
                    $resp['msg'] = "Updating Location Failed.";
                $resp['error']=$this->lastErrorMsg();
            }
        }
        return json_encode($resp);
    }
    function delete_location(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `location_list` where location_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Location successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_route_price(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id'))){
            if(empty($id)){
                $cols[] = "`{$k}`";
                $vals[] = "'{$v}'";
            }else{
                if(!empty($data)) $data .= ", ";
                $data .= " `{$k}` = '{$v}' ";
            }
            }
        }
        if(isset($cols) && isset($vals)){
            $cols_join = implode(",",$cols);
            $vals_join = implode(",",$vals);
        }
        if(empty($id)){
            $sql = "INSERT INTO `route_prices` ({$cols_join}) VALUES ($vals_join)";
        }else{
            $sql = "UPDATE `route_prices` set {$data} where rp_id = '{$id}'";
        }
        @$check= $this->query("SELECT COUNT(rp_id) as count from `route_prices` where `from_location_id` = '{$from_location_id}' and `to_location_id` = '{$to_location_id}' ".($id > 0 ? " and rp_id != '{$id}'" : ""))->fetchArray()['count'];
        if(@$check> 0){
            $resp['status'] ='failed';
            $resp['msg'] = 'Route Price already exists.';
        }else{
            @$save = $this->query($sql);
            if($save){
                $resp['status']="success";
                if(empty($id))
                    $resp['msg'] = "Route Price successfully saved.";
                else
                    $resp['msg'] = "Route Price successfully updated.";
            }else{
                $resp['status']="failed";
                if(empty($id))
                    $resp['msg'] = "Saving New Route Price Failed.";
                else
                    $resp['msg'] = "Updating Route Price Failed.";
                $resp['error']=$this->lastErrorMsg();
                $resp['sql']=$sql;
            }
        }
        return json_encode($resp);
    }
    function delete_route_price(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `route_prices` where rp_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Route Price successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_transaction(){
        $_POST['user_id'] = $_SESSION['user_id'];
        extract($_POST);
        $ids = array();
        foreach($price as $k => $v){
            if($v <= 0){
                continue;
            }
            for($s=0;$s < $pax[$k];$s++){
                $prefix = date('Ym');
                $code = sprintf("%'.04d",1);
                $i = 0;
                while(true){
                    $i++;
                    $chk = $this->query("SELECT count(ticket_id) `count` FROM `ticket_list` where ticket_no = '".$prefix.'-'.$code."' ")->fetchArray()['count'];
                    if($chk > 0){
                        $code = sprintf("%'.04d",abs($code) + 1);
                    }else{
                        break;
                    }
                }
                $data = "('".$prefix.'-'.$code."','{$rp_id}','{$type[$k]}','{$v}','{$user_id}')";
                $save = $this->query("INSERT INTO `ticket_list` (`ticket_no`,`rp_id`,`passenger_type`,`price`,`user_id`) VALUES {$data}");
                if($save){
                    $ids[]=$this->query("SELECT last_insert_rowid()")->fetchArray()[0];
                }else{
                    $error = true;
                    break;
                }
            }
        }
        if(!isset($error)){
            $resp['status']="success";
            $resp['ids']=implode(',',$ids);
            $_SESSION['flashdata']['type']="success";
            if(empty($id))
                $_SESSION['flashdata']['msg'] = "Transaction successfully saved.";
            else
                $_SESSION['flashdata']['msg'] = "Transaction successfully updated.";
        }else{
            $resp['status']="failed";
            if(empty($id))
                $resp['msg'] = "Saving New Transaction Failed.";
            else
                $resp['msg'] = "Updating Transaction Failed.";
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function delete_transaction(){
        extract($_POST);
        @$delete = $this->query("DELETE FROM `ticket_list` where ticket_id in ({$ids})");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Transaction successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
}
$a = isset($_GET['a']) ?$_GET['a'] : '';
$action = new Actions();
switch($a){
    case 'login':
        echo $action->login();
    break;
    case 'customer_login':
        echo $action->customer_login();
    break;
    case 'logout':
        echo $action->logout();
    break;
    case 'customer_logout':
        echo $action->customer_logout();
    break;
    case 'save_user':
        echo $action->save_user();
    break;
    case 'delete_user':
        echo $action->delete_user();
    break;
    case 'update_credentials':
        echo $action->update_credentials();
    break;
    case 'save_bus':
        echo $action->save_bus();
    break;
    case 'delete_bus':
        echo $action->delete_bus();
    break;
    case 'save_location':
        echo $action->save_location();
    break;
    case 'delete_location':
        echo $action->delete_location();
    break;
    case 'save_route_price':
        echo $action->save_route_price();
    break;
    case 'delete_route_price':
        echo $action->delete_route_price();
    break;
    case 'save_customer':
        echo $action->save_customer();
    break;
    case 'delete_customer':
        echo $action->delete_customer();
    break;
    case 'save_transaction':
        echo $action->save_transaction();
    break;
    case 'delete_transaction':
        echo $action->delete_transaction();
    break;
    case 'save_payment':
        echo $action->save_payment();
    break;
    default:
    // default action here
    break;
}
