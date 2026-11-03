<?php
require_once __DIR__.'/config/database.php';
$user='admin'; $hash=password_hash('EliteCuts@123',PASSWORD_DEFAULT);
$q=$conn->prepare('SELECT id FROM admins WHERE username=?');$q->execute([$user]);
if($q->fetch()){$q=$conn->prepare('UPDATE admins SET password=? WHERE username=?');$q->execute([$hash,$user]);}
else{$q=$conn->prepare('INSERT INTO admins(username,password) VALUES(?,?)');$q->execute([$user,$hash]);}
echo 'Admin ready. Username: admin | Temporary password: EliteCuts@123. DELETE create_admin.php now.';
