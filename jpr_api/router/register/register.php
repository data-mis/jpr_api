<?php

    router::set('register/register_patient',function(){
        require_once('./controllers/register.php');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $data = json_decode(file_get_contents("php://input",true));
            $_id = $data->id;              /* id card */
            $_ttl = trim($data->ttl);
            $_name = trim($data->name);
            $_lname = trim($data->lname);
            $_sex = $data->sex;
            $_born = $data->born;
            $_room = trim($data->room);

            if (!empty($_id) && !empty($_sex) && !empty($_born) && !empty($_room) && !empty($_name) && !empty($_lname)) {
                /* เช็ค format ของ id ว่ามี 13 หลักหรือไม่ */
                $_len_id = strlen($_id); 
                if($_len_id <> 13){
                    echo json_encode(array('MessageCode' => '200','message' => 'id card ต้องเท่ากับ 13 หลักเท่านั้น','status' => FALSE));
                }else{
                    /* เช็ค format ของ born */
                    $_len_born = strlen($_born);
                    if($_len_born <> 10){
                        echo json_encode(array('MessageCode' => '200','message' => 'born ต้องเท่ากับ 10 หลักเท่านั้น ต้องส่งตาม format ปีเดือนวัน เช่น 2025-01-01','status' => FALSE));
                    }else{
                        /* คำนวณอายุ */
                        if($_born=='0000-00-00'){
                            $_age = 0;
                        }else{
                            $_age=ayu($_born);
                            
                        }

                        register_patient($_id,$_sex,$_born,$_room,$_age,$_ttl,$_name,$_lname);
                    }
   
                }
            }else{
                
                echo json_encode(array('MessageCode' => '200','message' => 'id,sex,born,name,lname และ room ห้ามเป็นค่าว่าง','status' => FALSE)); 
                
            }

        }else{
            echo json_encode(array('message' => 'Invalid Method'.' '.$_SERVER['REQUEST_METHOD'],'status' => FALSE)); 
        }
    });

?>