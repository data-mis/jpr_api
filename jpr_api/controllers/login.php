<?php

    function get_login($username,$password){
        $u_username = strtoupper($username);

        $sql = " SELECT id,username FROM user WHERE UPPER(username) = '".$u_username."' AND password = password('".$password."') LIMIT 1";
        $result = dbQuery($sql);
        if(dbNumRows($result) > 0) {
            echo json_encode(array('MessageCode' => '200','message' =>'พบผู้ใช้งานนี้ในระบบ','status' => TRUE));
        }else{
            echo json_encode(array('MessageCode' => '200','message' =>'ไม่พบผู้ใช้งานนี้ในระบบ','status' => FALSE));
        }
    }
?>