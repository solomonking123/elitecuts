<?php
require_once __DIR__.'/includes/bootstrap.php';
unset($_SESSION['customer_id'],$_SESSION['customer_name'],$_SESSION['customer_phone'],$_SESSION['customer_email']);
session_regenerate_id(true);header('Location: index.php');exit;
