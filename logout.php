<?php
session_start();
unset($_SESSION['user_id']);
unset($_SESSION['name']);
unset($_SESSION['user_type']);
unset($_SESSION['user_status']);
unset($_SESSION['login_time']);

// $_SESSION['msg'] = "<b>USER LOGGED OUT!</b>";
// $_SESSION['msg_type'] = "warning";

// header("Location:login");
$_SESSION['msg_heading'] = "LOGOUT!";
$_SESSION['msg'] = "USER LOGGED OUT!";
$_SESSION['msg_type'] = "warning";


header("Location: login");
