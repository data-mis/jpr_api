<?php
    /* Main */
    $servername = "11.0.0.1"; // localhost
    $username = "root";  //root
    $password = "istyle8885"; //istyle8885
    $dbname = "jpr";
    $port = "3306";
    
    // $servername = "163.44.197.219"; // localhost
    // $username = "mis";  //root
    // $password = "datasoft"; //istyle8885
    // $dbname = "dsd";
    // $port = "3306";

    // Create connection
    /*---------- server main------------*/    
    global $con;
    $con = mysqli_connect($servername, $username, $password,$dbname,$port);
    mysqli_query($con,"set names tis620");
    mysqli_query($con,"set names utf8");
    mysqli_query($con,"set character_set_results=utf8");
    mysqli_query($con,"set character_set_client=utf8");
    mysqli_query($con,"set character_set_connection=utf8");

    if (!$con) {
        
        // die("Connection failed: " . mysqli_connect_error());
        echo json_encode(array('connection'=>'Connection failed','status'=>FALSE));

    }else{
        /* connection successfully to server main*/
        function dbQuery($sql) {
            global $con;
            $result = mysqli_query($con,$sql) or die(mysqli_error($con));
	        // $result = mysqli_query($con,$sql);
            return $result;
        }

        function dbFetchAssoc($result) {
            return mysqli_fetch_assoc($result);
        }
        
        function dbNumRows($result) {
            return mysqli_num_rows($result);
        }
        
        function closeConn() {
           mysqli_close($con);
        }

    }

?>