<?php

    function lcname($tbl_field,$tbl_name,$tbl_key,$tbl_value){

        $rows_tbl = "";
        $sql_tbl = " SELECT $tbl_field FROM tbllu WHERE title='".$tbl_name."' and $tbl_key = '".$tbl_value."' ";
        $result_tbl = dbQuery($sql_tbl);
        $dbFet = dbFetchAssoc($result_tbl);

        if(dbNumRows($result_tbl) > 0) {
            $rows_tbl = trim($dbFet[$tbl_field]);
            return $rows_tbl;

        }else{
            return $rows_tbl;
        }
    }

    function ayu($born) {
        $ayu = 0;
        if ($born != '0000-00-00' && strlen($born) =='10') {
            date_default_timezone_set("Asia/Bangkok");
            $year1 = date("Y", strtotime($born));
            $year2 = date("Y");
            $ayu = $year2 - $year1;
            return $ayu;
        } else {
            return $ayu ;
        }
    }

    function escapetodb($field){
        global $con;
        $result = mysqli_real_escape_string($con,$field);
        return $result;
    }

    function isucx($_type){
        $_isuc = ".F.";
        $_isuc_upper = strtoupper(substr($_type,0,2));
        if(
            $_isuc_upper=='AA' || $_isuc_upper=='AB' || $_isuc_upper=='AC' || $_isuc_upper=='AD' || 
            $_isuc_upper=='AE' || $_isuc_upper=='AF' || $_isuc_upper=='AG' || $_isuc_upper=='AH' || 
            $_isuc_upper=='AI' || $_isuc_upper=='AJ' || $_isuc_upper=='AK' || $_isuc_upper=='AL' || 
            $_isuc_upper=='UC' 
        ){
            $_isuc = ".T.";
        }
        return  $_isuc;
    }

    function CheckDataType($data,$datatype){

        if($data === $datatype){
            $data_type = ".T.";
        }else{
            $data_type = ".F.";
        }
        
        return $data_type;
        
    }


?>