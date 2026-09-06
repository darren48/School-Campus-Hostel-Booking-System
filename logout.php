<?php
session_start();
unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_email'], $_SESSION['user_role']);
session_write_close();
header('Location: index.php');
exit;
