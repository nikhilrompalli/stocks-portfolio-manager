<?php
// Initialize the session
require_once "config.php";
try{
    $user_id_sql = "SELECT id FROM users WHERE username='nikhil';";
    $user_id = mysqli_query($link, $user_id_sql);
    $row = mysqli_fetch_array($user_id);
    echo  $row[0];
    }catch(Exception $e) {
        echo 'Message: ' .$e->getMessage();
    }

?>

