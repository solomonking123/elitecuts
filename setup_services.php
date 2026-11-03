<?php
require_once __DIR__.'/config/database.php';
$items=[
 ['Regular Haircut',2000,'A tailored, clean everyday haircut.'],
 ['Skin Fade',3000,'A modern fade with a crisp finish.'],
 ['Haircut and Beard Trim',4000,'A haircut with a carefully shaped beard.'],
 ['Children Haircut',2000,'A friendly haircut for children.'],
 ['Hair Washing',1500,'A refreshing professional hair wash.']
];
$find=$conn->prepare('SELECT id FROM services WHERE service_name=?');
$add=$conn->prepare('INSERT INTO services(service_name,price,description) VALUES(?,?,?)');
$upd=$conn->prepare('UPDATE services SET price=?,description=? WHERE service_name=?');
foreach($items as [$n,$p,$d]) { $find->execute([$n]); if($find->fetch()) $upd->execute([$p,$d,$n]); else $add->execute([$n,$p,$d]); }
echo 'Services are ready. Delete setup_services.php now.';
