<?php
session_start();

/* Optional: log logout time (if you want tracking later)
if(isset($_SESSION['user_id'])){
    include("config/database.php");

    mysqli_query($conn, "
        UPDATE users
        SET last_logout = NOW()
        WHERE user_id = {$_SESSION['user_id']}
    ");
}
*/

/* Destroy all session data */
$_SESSION = [];

session_unset();
session_destroy();

/* Redirect to login page */
header("Location: ../login.php");
exit();
?>