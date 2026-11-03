<?php
require_once __DIR__.'/includes/bootstrap.php';
$date=$_GET['date']??''; $d=DateTime::createFromFormat('Y-m-d',$date);
if(!$d||$d->format('Y-m-d')!==$date||$date<date('Y-m-d')) json_response(['success'=>false,'message'=>'Choose today or a future date.'],422);
$q=$conn->prepare("SELECT TIME_FORMAT(appointment_time,'%H:%i') slot FROM appointments WHERE appointment_date=? AND status<>'Cancelled'");
$q->execute([$date]); $taken=array_column($q->fetchAll(),'slot'); $slots=[];
for($h=9;$h<18;$h++){ $t=sprintf('%02d:00',$h); if(!in_array($t,$taken,true)) $slots[]=$t; }
json_response(['success'=>true,'slots'=>$slots]);
