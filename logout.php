<?php
require_once 'controller/AuthController.php';
(new AuthController())->logout();
header('Location: login.php');
exit;
?>
