<?php

    router::set('login/get_login',function(){
        require_once('./controllers/login.php');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents("php://input",true));
            $username = $data->username;
            $password = $data->password;

            $c_username = CheckDataType(gettype($username),"string");
            $c_password = CheckDataType(gettype($password),"string");
            if (!empty($username) and !empty($password)) {
                if($c_username == ".T." && $c_password == ".T."){
                    get_login($username,$password);
                }else{
                    echo json_encode(array('MessageCode' => '200','message' => 'มีชนิดข้อมูลไม่ถูกต้อง','status' => FALSE));
                }
                
            }else{
                echo json_encode(array('MessageCode' => '200','message' => 'username และ password ห้ามเป็นค่าว่าง','status' => FALSE));
            }
            
        }else{
            echo json_encode(array('MessageCode' => '200','message' => 'Invalid Method'.' '.$_SERVER['REQUEST_METHOD'],'status' => FALSE)); 
        }
    });

?>