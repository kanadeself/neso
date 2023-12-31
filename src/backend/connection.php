<?php      
    $host = "";  
    $user = "";  
    $password = '';  
    $db_name = "nesos";  
      
    $con = mysqli_connect($host, $user, $password, $db_name); 
    if(mysqli_connect_errno()) {  
        die("Connection to MySQL failed. ". mysqli_connect_error());  
    }
?>  