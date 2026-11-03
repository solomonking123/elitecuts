<?php
require_once __DIR__.'/includes/bootstrap.php'; $error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $u=trim($_POST['username']??'');$p=$_POST['password']??'';
 $q=$conn->prepare('SELECT id,username,password FROM admins WHERE username=?');$q->execute([$u]);$a=$q->fetch();
 if($a&&password_verify($p,$a['password'])){session_regenerate_id(true);$_SESSION['admin_id']=(int)$a['id'];$_SESSION['admin_username']=$a['username'];header('Location: admin.php');exit;}
 $error='Incorrect username or password.';
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin login | ELITE CUTS</title><link rel="stylesheet" href="css/style.css"></head>
<body><main class="booking-page"><section class="booking-intro"><p class="eyebrow">ELITE CUTS / MANAGEMENT</p><h1>Admin <em>login.</em></h1><p>Sign in to manage appointments.</p></section>
<form method="post" class="booking-form panel" style="max-width:520px;margin:auto"><div class="form-field"><label>Username</label><input name="username" required autocomplete="username"></div><div class="form-field"><label>Password</label><input name="password" type="password" required autocomplete="current-password"></div>
<?php if($error): ?><p role="alert"><?= h($error) ?></p><?php endif; ?><button class="button button-gold" type="submit">Sign in</button></form></main></body></html>
