<?php
session_start();
session_destroy();
header("Location: ../public/landing.php");
exit();
?>
