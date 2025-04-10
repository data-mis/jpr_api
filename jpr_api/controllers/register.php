<?php

    function register_patient($_id,$_sex,$_born,$_room,$_age,$_ttl,$_name,$_lname){
        
        $_spc = lcname('spc','clin','code',$_room);
        if(empty($_spc) || $_spc==''){
            $_spc='01';
        }

        $_room_str = substr($_room,0,2);

        $ck_date_sql = " SELECT date_format(curdate(),'%Y-%m-%d') as curdate__,time_format(curtime(),'%H:%i:%s') as curtime__ ";
        $ck_date_res = dbQuery($ck_date_sql);
        $ck_date_row = dbFetchAssoc($ck_date_res);
        $date =  $ck_date_row['curdate__'];
        $time = substr($ck_date_row['curtime__'],0,5);
        
        /* เช็คว่า id นี้ เสียชีวิตแล้วหรือไม่ */
        $sql_mis_dead = "SELECT * FROM mis WHERE id='".$_id."' and sta='9'";
        $res_dead = dbQuery($sql_mis_dead);
        if(dbNumRows($res_dead) > 0) {
            echo json_encode(array('MessageCode' => '200','message' => 'ผู้ป่วยเสียชีวิต ลงทะเบียนไม่ได้','status' => FALSE));
        }else{
            /* เช็คว่า id นี้ มีการล้มเวชระเบียนหรือไม่ */
            $sql_mis = "SELECT hn,ttl,name,lname,sex,born,addr,moo,acode,rac,nat,rel,occ,alg,blood,rh,sta,id,type,typem,typen,typei,typex,hmain,hsub,num,types,under,unders 
                        FROM mis WHERE id='".$_id."' and sta<>'D' 
                        ORDER BY hn DESC
                        LIMIT 1 
                       ";
            $res_mis = dbQuery($sql_mis);
            if(dbNumRows($res_mis) > 0) {
                $row_mis = dbFetchAssoc($res_mis);
                $hn = $row_mis['hn'];
                $ttl = $row_mis['ttl'];
                $name = $row_mis['name'];
                $lname = $row_mis['lname'];
                $sex = $row_mis['sex'];
                $born = $row_mis['born'];
                $addr = $row_mis['addr'];
                $moo = $row_mis['moo'];
                $acode = $row_mis['acode'];
                $rac = $row_mis['rac'];
                $nat = $row_mis['nat'];
                $rel = $row_mis['rel'];
                $occ = $row_mis['occ'];
                $sta = $row_mis['sta'];
                $alg = $row_mis['alg'];
                $blood = $row_mis['blood'];
                $rh = $row_mis['rh'];
                $type = $row_mis['type'];
                $typem = $row_mis['typem'];
                $typen = $row_mis['typen'];
                $typei = $row_mis['typei'];
                $typex = $row_mis['typex'];
                $hmain = $row_mis['hmain'];
                $hsub = $row_mis['hsub'];
                $num = (int)$row_mis['num']+1;
                $types = $row_mis['types'];
                $under = $row_mis['under'];
                $unders = $row_mis['unders'];
                $_bill=0;	

                $_tall = '';
                $sql_reg = "SELECT txn,tall FROM reg WHERE hn='".$hn."' ORDER BY date DESC LIMIT 1";
                $res_reg = dbQuery($sql_reg);
                if(dbNumRows($res_reg) > 0) {
                    $row_reg = dbFetchAssoc($res_reg);
                    $_tall = $row_reg['tall'];
                }

                /* เช็คว่า id นี้ เป็นผู้ป่วยในหรือไม่ */
                $sql_ipd = "SELECT * FROM ipd WHERE hn='".$hn."'";
                $res_ipd = dbQuery($sql_ipd);
                if(dbNumRows($res_ipd) > 0) {
                    echo json_encode(array('MessageCode' => '200','message' => 'เป็นผู้ป่วยใน ไม่สามารถลงทะเบียนได้','status' => FALSE));
                }else{
                    
                    $upd_mis = "UPDATE mis SET name='".escapetodb($name)."',lname='".escapetodb($lname)."',sex='".escapetodb($sex)."',typem='".escapetodb($typem)."',type='".escapetodb($type)."',
                                typen='".escapetodb($typen)."',typei='".$typei."',typex='".$typex."',id='".escapetodb($_id)."',hmain='".escapetodb($hmain)."',hsub='".escapetodb($hsub)."',
                                born='".$born."',num='".$num."',last_room='".escapetodb($_room)."',last='".$date."',last_time='".escapetodb($time)."'
                                WHERE hn='".$hn."'
                               ";
                    $res_upd_mis = dbQuery($upd_mis);

                    $ins_reg = "INSERT INTO reg SET hn='".escapetodb($hn)."',ttl='".escapetodb($ttl)."',name='".escapetodb($name)."',lname='".escapetodb($lname)."',sex='".escapetodb($sex)."',
                                age='".$_age."',born='".$born."',room='".escapetodb($_room)."',spc='".escapetodb($_spc)."',date='".$date."',time='".escapetodb($time)."',num='".$num."',
                                acode='".escapetodb($acode)."',rac='".escapetodb($rac)."',nat='".escapetodb($nat)."',rel='".escapetodb($rel)."',occ='".escapetodb($occ)."',sta='".escapetodb($sta)."',
                                id='".escapetodb($_id)."',hmain='".escapetodb($hmain)."',hsub='".escapetodb($hsub)."',type='".escapetodb($type)."',typem='".escapetodb($typem)."',
                                typen='".escapetodb($typen)."',typei='".$typei."',typex='".$typex."',alg='".escapetodb($alg)."',blood='".escapetodb($blood)."',rh='".escapetodb($rh)."',
                                bill='".$_bill."',types='".escapetodb($types)."',under='".escapetodb($under)."',unders='".escapetodb($unders)."',tall='".escapetodb($_tall)."'
                                ";
                    $res_ins_reg = dbQuery($ins_reg);

                    $sql_last_txn = "SELECT last_insert_id() as last_";
                    $res_last_txn = dbQuery($sql_last_txn);
                    $row_last_txn = dbFetchAssoc($res_last_txn);
                    $_txn = (int)$row_last_txn['last_'];

                    if((int)$_txn > 0){

                        $fname = $ttl.$name.'  '.$lname;

                        $sql_autoreg = "SELECT hn FROM autoreg WHERE hn='".$hn."' and date='".$date."'";
                        $res_autoreg = dbQuery($sql_autoreg);
                        if(dbNumRows($res_autoreg) > 0) {
                            
                            $ins_autoreg = "INSERT INTO autoreg SET hn='".$hn."',name='".escapetodb($fname)."',sex='".escapetodb($sex)."',born='".$born."',room='".escapetodb($_room)."',
                                            date='".$date."',time='".escapetodb($time)."',type='".escapetodb($type)."',txn='".$_txn."'
                                            ";
                            $res_ins_autoreg = dbQuery($ins_autoreg);
                        }else{
                            $sql_opd_trk = "SELECT site FROM opd_trk WHERE hn='".$hn."'";
                            $res_sql_opd_trk = dbQuery($sql_opd_trk);
                            if(dbNumRows($res_sql_opd_trk) > 0) {
                                
                                $ins_autoreg = "INSERT INTO autoreg SET hn='".$hn."',name='".escapetodb($fname)."',sex='".escapetodb($sex)."',born='".$born."',room='".escapetodb($_room)."',
                                                date='".$date."',time='".escapetodb($time)."',type='".escapetodb($type)."',txn='".$_txn."'
                                                ";
                                $res_ins_autoreg = dbQuery($ins_autoreg);
                            }else{
                                
                                $ins_autoreg = "INSERT INTO autoreg SET hn='".$hn."',name='".escapetodb($fname)."',sex='".escapetodb($sex)."',born='".$born."',room='".escapetodb($_room)."',
                                                date='".$date."',time='".escapetodb($time)."',type='".escapetodb($type)."',txn='".$_txn."'
                                                ";
                                $res_ins_autoreg = dbQuery($ins_autoreg);
                            }
                        }

                        if($_room_str<>'04' && $_room_str<>'02' && $_room_str<>'11' && $_room_str<>'03' && $_room_str<>'32' && $_room_str<>'33' && $_room_str<>'36' && $_room_str<>'37' && $_room_str<>'38' && $_room_str<>'42'){
                            $grp='16';
                            $pri = 50;
                            $pri1 = 50;
        
                            if(date("w", strtotime($date)) == 1 || date("w", strtotime($date)) == 7){
        
                                $namex = 'ค่าบริการ OPD นอกเวลาราชการ 55021';
                                $codex = '16-001';
        
                                $ins_etc = "INSERT INTO etc SET txn='".$_txn."',code='".escapetodb($codex)."',name='".escapetodb($namex)."',amt=1,print=0,date='".$date."',time='".escapetodb($time)."',
                                            pri='".$pri."',pri1='".$pri1."',grp='".escapetodb($grp)."',typ='".escapetodb($grp)."',room='".escapetodb($_room)."'
                                        ";
                                $res_ins_etc = dbQuery($ins_etc);
                            }else{
                                if(($time>='16:01' && $time<='24:00') || ($time>='00:00' && $time<='07:00')){
                                    
                                    if($_room_str == '01'){
                                        $namex = 'ค่าบริการ OPD ในเวลาราชการ 55020';
                                        $codex = '16-000';
        
                                        $ins_etc = "INSERT INTO etc SET txn='".$_txn."',code='".escapetodb($codex)."',name='".escapetodb($namex)."',amt=1,print=0,date='".$date."',time='".escapetodb($time)."',
                                                    pri='".$pri."',pri1='".$pri1."',grp='".escapetodb($grp)."',typ='".escapetodb($grp)."',room='".escapetodb($_room)."'
                                                   ";
                                        $res_ins_etc = dbQuery($ins_etc);
                                    }else{
                                        $namex = 'ค่าบริการ OPD นอกเวลาราชการ 55021';
                                        $codex = '16-001';
            
                                        $ins_etc = "INSERT INTO etc SET txn='".$_txn."',code='".escapetodb($codex)."',name='".escapetodb($namex)."',amt=1,print=0,date='".$date."',time='".escapetodb($time)."',
                                                    pri='".$pri."',pri1='".$pri1."',grp='".escapetodb($grp)."',typ='".escapetodb($grp)."',room='".escapetodb($_room)."'
                                                ";
                                        $res_ins_etc = dbQuery($ins_etc);
                                    }
                                    
                                }else{
                                    if($_room_str<>'11' && $_room_str<>'03'){
        
                                        $namex = 'ค่าบริการ OPD ในเวลาราชการ 55020';
                                        $codex = '16-000';
        
                                        $ins_etc = "INSERT INTO etc SET txn='".$_txn."',code='".escapetodb($codex)."',name='".escapetodb($namex)."',amt=1,print=0,date='".$date."',time='".escapetodb($time)."',
                                                    pri='".$pri."',pri1='".$pri1."',grp='".escapetodb($grp)."',typ='".escapetodb($grp)."',room='".escapetodb($_room)."'
                                                   ";
                                        $res_ins_etc = dbQuery($ins_etc);
                                    }
                                }
                            }
                            
                        }
        
                    }
                }
 
            }else{

                $type = 'A1$';
                $typem = 'A1$';
                $_bill=0;

                $sql_max_hn = "SELECT max(hn) as max_hn_ FROM mis ";
                $res_max_hn = dbQuery($sql_max_hn);
                $row_max_hn = dbFetchAssoc($res_max_hn);
                $hn = (int)$row_max_hn['max_hn_']+1;

                $substr_date = substr($date,5,5);
                if($substr_date == '01-01'){
                    $_hn_new = substr(((int)(substr($date,0,4))+543),2,2).'00001';
                    $sql_hn_new = "SELECT hn FROM mis WHERE hn='".$_hn_new."'";
                    $res_hn_new = dbQuery($sql_hn_new);
                    if(dbNumRows($res_hn_new) < 1) {
                        $hn = $_hn_new;
                    }
                }

                if((int)(substr($hn,0,2)) < 51 && $date>='2008-01-01'){
                    $hn_old=$hn;
                    $sql_max_hn_old = "SELECT max(hn) as max_hn_ FROM mis ";
                    $res_max_hn_old = dbQuery($sql_max_hn_old);
                    $row_max_hn_old = dbFetchAssoc($res_max_hn_old);
                    $hn = (int)$row_max_hn_old['max_hn_']+1;
                }else{
                    $hn_old='';
                }

                $sql_ishn = "INSERT INTO mis SET hn='".$hn."'";
                $rea_ishn = dbQuery($sql_ishn);
                if($rea_ishn=== FALSE){
                    do {
                        $sql_max_hn_ex = "SELECT max(hn) as max_hn_ FROM mis ";
                        $res_max_hn_ex = dbQuery($sql_max_hn_ex);
                        $row_max_hn_ex = dbFetchAssoc($res_max_hn_ex);
                        $hn = (int)$row_max_hn_ex['max_hn_']+1;

                        $sql_ishn_ex = "INSERT INTO mis SET hn='".$hn."'";
                        $rea_ishn_ex = dbQuery($sql_ishn_ex);

                        if ($rea_ishn_ex === FALSE) {
                            sleep(1);
                        }

                    } while($rea_ishn_ex === FALSE);
                }
                
                $upd_mis = "UPDATE mis SET ttl='".escapetodb($_ttl)."',name='".escapetodb($_name)."',lname='".escapetodb($_lname)."',sex='".escapetodb($_sex)."',born='".$_born."',num=1,
                            reg='".$date."',reg_time='".escapetodb($time)."',last_room='".escapetodb($_room)."',id='".escapetodb($_id)."',
                            type='".escapetodb($type)."',typem='".escapetodb($typem)."',last='".$date."',last_time='".escapetodb($time)."',hn_old='".escapetodb($hn_old)."'
                            WHERE hn='".$hn."'
                        ";
                $res_upd_mis = dbQuery($upd_mis);

                $ins_reg = "INSERT INTO reg SET hn='".escapetodb($hn)."',ttl='".escapetodb($_ttl)."',name='".escapetodb($_name)."',lname='".escapetodb($_lname)."',sex='".escapetodb($_sex)."',
                            age='".$_age."',born='".$_born."',room='".escapetodb($_room)."',spc='".escapetodb($_spc)."',date='".$date."',time='".escapetodb($time)."',num=1,
                            rac='99',nat='99',rel='1',occ='000',sta='1',type='".escapetodb($type)."',typem='".escapetodb($typem)."',bill='".$_bill."',
                            id='".escapetodb($_id)."',hn_old='".escapetodb($hn_old)."'
                            ";
                $res_ins_reg = dbQuery($ins_reg);

                $sql_last_txn = "SELECT last_insert_id() as last_";
                $res_last_txn = dbQuery($sql_last_txn);
                $row_last_txn = dbFetchAssoc($res_last_txn);
                $_txn = (int)$row_last_txn['last_'];

                if((int)$_txn > 0){

                    if($_room_str<>'04' && $_room_str<>'02' && $_room_str<>'11' && $_room_str<>'03' && $_room_str<>'32' && $_room_str<>'33' && $_room_str<>'36' && $_room_str<>'37' && $_room_str<>'38'){
                        $grp='16';
                        $pri = 50;
                        $pri1 = 50;
    
                        if(date("w", strtotime($date)) == 1 || date("w", strtotime($date)) == 7){
    
                            $namex = 'ค่าบริการ OPD นอกเวลาราชการ 55021';
                            $codex = '16-001';
    
                            $ins_etc = "INSERT INTO etc SET txn='".$_txn."',code='".escapetodb($codex)."',name='".escapetodb($namex)."',amt=1,print=0,date='".$date."',time='".escapetodb($time)."',
                                        pri='".$pri."',pri1='".$pri1."',grp='".escapetodb($grp)."',typ='".escapetodb($grp)."',room='".escapetodb($_room)."'
                                       ";
                            $res_ins_etc = dbQuery($ins_etc);
                        }else{
                            if(($time>='16:01' && $time<='24:00') || ($time>='00:00' && $time<='07:00')){
    
                                $namex = 'ค่าบริการ OPD นอกเวลาราชการ 55021';
                                $codex = '16-001';
    
                                $ins_etc = "INSERT INTO etc SET txn='".$_txn."',code='".escapetodb($codex)."',name='".escapetodb($namex)."',amt=1,print=0,date='".$date."',time='".escapetodb($time)."',
                                            pri='".$pri."',pri1='".$pri1."',grp='".escapetodb($grp)."',typ='".escapetodb($grp)."',room='".escapetodb($_room)."'
                                        ";
                                $res_ins_etc = dbQuery($ins_etc);
                            }else{
                                if($_room_str<>'11' && $_room_str<>'03'){
    
                                    $namex = 'ค่าบริการ OPD ในเวลาราชการ 55020';
                                    $codex = '16-000';
    
                                    $ins_etc = "INSERT INTO etc SET txn='".$_txn."',code='".escapetodb($codex)."',name='".escapetodb($namex)."',amt=1,print=0,date='".$date."',time='".escapetodb($time)."',
                                                pri='".$pri."',pri1='".$pri1."',grp='".escapetodb($grp)."',typ='".escapetodb($grp)."',room='".escapetodb($_room)."'
                                            ";
                                    $res_ins_etc = dbQuery($ins_etc);
                                }
                            }
                        }
                        
                    }
    
                }
    
            }
            
            if($type == 'A21*'){
                $ins_type_pay = "INSERT INTO type_pay SET txn='".$_txn."',type='C0001'";
                $res_ins_type_pay = dbQuery($ins_type_pay);
            }

            $_isucx = isucx($type);
            if($_isucx == '.T.'){
                $ins_type_pay_isucx = "INSERT INTO type_pay SET txn='".$_txn."',type='UCUC'";
                $res_ins_type_pay_isucx = dbQuery($ins_type_pay_isucx);
            }

            $ins_reg_room = "INSERT INTO reg_room SET txn='".$_txn."',room='".escapetodb($_room)."',date='".$date."',time='".escapetodb($time)."'";
            $res_ins_reg_room = dbQuery($ins_reg_room);

            $ins_reg_occ = "INSERT INTO reg_occ SET hn='".$hn."',name='".escapetodb($_name)."',lname='".escapetodb($_lname)."',date='".$date."',time='".escapetodb($time)."',
                            opd=1,txn='".$_txn."',stat_ca='".escapetodb($type)."'
                            ";
            $res_ins_reg_occ = dbQuery($ins_reg_occ);

            echo json_encode(array('MessageCode' => '200','message' => 'ลงทะเบียนสำเร็จ','status' => TRUE));

            /* End */
        }
        /* End */
        
    }
?>