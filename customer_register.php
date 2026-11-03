<?php
require_once __DIR__.'/includes/bootstrap.php';
$next = (($_GET['next'] ?? $_POST['next'] ?? '') === 'booking.php') ? 'booking.php' : 'my_bookings.php';
if(!empty($_SESSION['customer_id'])) { header('Location: '.$next); exit; }
$error='';
if($_SERVER['REQUEST_METHOD']==='POST') {
 $name=trim(preg_replace('/\s+/',' ',(string)($_POST['name']??'')));
 $phone=trim((string)($_POST['phone']??'')); $email=strtolower(trim((string)($_POST['email']??'')));
 $password=(string)($_POST['password']??''); $confirm=(string)($_POST['confirm_password']??'');
 if(mb_strlen($name)<2 || mb_strlen($name)>80 || strlen(preg_replace('/\D/','',$phone))<7 || !filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($password)<8 || $password!==$confirm) {
  $error='Please enter valid details. Your password must be at least 8 characters and both passwords must match.';
 } else {
  try {
   $q=$conn->prepare('INSERT INTO customers(full_name,phone,email,password_hash) VALUES(?,?,?,?)');
   $q->execute([$name,$phone,$email,password_hash($password,PASSWORD_DEFAULT)]);
   session_regenerate_id(true); $_SESSION['customer_id']=(int)$conn->lastInsertId(); $_SESSION['customer_name']=$name; $_SESSION['customer_phone']=$phone; $_SESSION['customer_email']=$email;
   header('Location: '.$next); exit;
  } catch(PDOException $e) { if($e->getCode()==='23000') $error='An account with that email already exists. Please log in instead.'; else {error_log($e->getMessage());$error='Account could not be created. Please try again.';} }
 }
}
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Create account | ELITE CUTS</title><link rel="stylesheet" href="css/style.css"><style>.account-wrap{width:min(560px,92%);margin:70px auto 90px}.account-card{padding:32px;background:#fff;border:1px solid #eeece6}.account-card h1{font:500 38px Georgia,serif;margin:8px 0 22px}.account-grid{display:grid;gap:17px}.account-grid label{display:block;font-size:12px;font-weight:700;margin-bottom:7px}.account-grid input{width:100%;height:45px;padding:0 12px;border:1px solid #e6e4de}.account-error{padding:12px;background:#fbf0ed;color:#8d3d32;margin-bottom:18px;font-size:13px}.account-foot{margin-top:20px;font-size:13px}.account-foot a{color:#977747;text-decoration:underline}</style></head><body><header class="site-header"><a class="brand" href="index.php"><span class="brand-mark">EC</span><span>ELITE <b>CUTS</b></span></a><nav class="site-nav"><a href="index.php">Home</a><a href="services.php">Services</a><a href="booking.php">Book a visit</a><a class="active" href="customer_register.php">Create account</a></nav><a class="header-cta" href="customer_login.php">Customer login ↗</a></header><main class="account-wrap"><section class="account-card"><p class="eyebrow">JOIN ELITE CUTS</p><h1>Create your <em>account.</em></h1><?php if($error): ?><div class="account-error"><?=h($error)?></div><?php endif; ?><form method="post" class="account-grid"><input type="hidden" name="next" value="<?=h($next)?>"><div><label for="name">Full name</label><input id="name" name="name" required maxlength="80" autocomplete="name" value="<?=h($_POST['name']??'')?>"></div><div><label for="phone">Phone number</label><input id="phone" name="phone" type="tel" required autocomplete="tel" value="<?=h($_POST['phone']??'')?>"></div><div><label for="email">Email address</label><input id="email" name="email" type="email" required autocomplete="email" value="<?=h($_POST['email']??'')?>"></div><div><label for="password">Password (at least 8 characters)</label><input id="password" name="password" type="password" required minlength="8" autocomplete="new-password"></div><div><label for="confirm_password">Confirm password</label><input id="confirm_password" name="confirm_password" type="password" required minlength="8" autocomplete="new-password"></div><button class="button button-gold" type="submit">Create account ↗</button></form><p class="account-foot">Already have an account? <a href="customer_login.php">Log in</a></p></section></main></body></html>
