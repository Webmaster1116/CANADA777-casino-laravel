<?php
$host = "localhost"; 
$user = "canada777"; 
$password = "vV6iA0lW1drI5q"; 
$dbname = "canada777"; 

$con = mysqli_connect($host, $user, $password,$dbname);
// Check connection
if (!$con) {
 die("Connection failed: " . mysqli_connect_error());
}
if(isset($_POST['email'])){
    $username = mysqli_real_escape_string($con,$_POST['email']);

    $query = "select count(*) as cntUser from w_users where email='".$username."'";
    
    $result = mysqli_query($con,$query);
    $response = "";
    if(mysqli_num_rows($result)){
        $row = mysqli_fetch_array($result);
    
        $count = $row['cntUser'];
        
        if($count > 0){
            $response = "<span style='color: red;'>This email already taken</span>";
        }
       
    }
    
    echo $response;
    die;
}
?>