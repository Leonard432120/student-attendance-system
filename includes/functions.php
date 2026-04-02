<?php

/* =========================================================
   LOG SYSTEM ACTIVITY
   ========================================================= */
if (!function_exists('logAction')) {

    function logAction($conn, $user_id, $action){

        $user_id = mysqli_real_escape_string($conn, $user_id);
        $action = mysqli_real_escape_string($conn, $action);

        $sql = "INSERT INTO activity_log (user_id, action)
                VALUES ('$user_id', '$action')";

        mysqli_query($conn, $sql);
    }
}


/* =========================================================
   CHECK LOGIN ROLE
   ========================================================= */
if (!function_exists('checkRole')) {

    function checkRole($role){
        if(!isset($_SESSION['role']) || $_SESSION['role'] != $role){
            echo "Access Denied!";
            exit();
        }
    }
}


/* =========================================================
   REDIRECT HELPER
   ========================================================= */
if (!function_exists('redirect')) {

    function redirect($url){
        header("Location: $url");
        exit();
    }
}


/* =========================================================
   SAFE OUTPUT (PREVENT XSS)
   ========================================================= */
if (!function_exists('e')) {

    function e($string){
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}


/* =========================================================
   COUNT ROWS
   ========================================================= */
if (!function_exists('countRows')) {

    function countRows($conn, $table){
        $sql = "SELECT COUNT(*) as total FROM $table";
        $result = mysqli_query($conn, $sql);
        return mysqli_fetch_assoc($result)['total'];
    }
}

?>