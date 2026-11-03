<?php
require_once __DIR__.'/includes/bootstrap.php';
$q=$conn->query('SELECT id,service_name AS name,price,description FROM services ORDER BY id');
json_response(['success'=>true,'services'=>$q->fetchAll()]);
