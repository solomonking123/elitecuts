<?php
require_once __DIR__.'/includes/bootstrap.php';
require_once __DIR__.'/includes/mailer.php';
// Enforce customer authentication on the server; hiding the form alone is not security.
if (empty($_SESSION['customer_id'])) json_response(['success'=>false,'message'=>'Please create an account or log in before booking.','login_url'=>'customer_login.php?next=booking.php'],401);
if($_SERVER['REQUEST_METHOD']!=='POST') json_response(['success'=>false,'message'=>'Method not allowed.'],405);
$x=json_decode(file_get_contents('php://input'),true)??[];
$name=trim(preg_replace('/\s+/',' ',(string)($x['name']??'')));
$phone=trim((string)($x['phone']??'')); $digits=preg_replace('/\D/','',$phone);
$sid=filter_var($x['service_id']??null,FILTER_VALIDATE_INT); $date=(string)($x['date']??''); $time=(string)($x['time']??'');
$d=DateTime::createFromFormat('Y-m-d',$date);
if(mb_strlen($name)<2||mb_strlen($name)>150||strlen($digits)<7||strlen($digits)>15||!$sid||
 !$d||$d->format('Y-m-d')!==$date||$date<date('Y-m-d')||!preg_match('/^(09|1[0-7]):00$/',$time))
 json_response(['success'=>false,'message'=>'Please provide valid name, phone, service, date and time.'],422);
$lockName='elitecuts_'.md5($date.'_'.$time);
$l=$conn->prepare('SELECT GET_LOCK(?,5)'); $l->execute([$lockName]);
if((int)$l->fetchColumn()!==1) json_response(['success'=>false,'message'=>'That time is being booked. Try again.'],409);
try {
 $conn->beginTransaction();
 $q=$conn->prepare("SELECT id FROM appointments WHERE appointment_date=? AND appointment_time=? AND status<>'Cancelled' LIMIT 1");
 $q->execute([$date,$time]);
 if($q->fetch()){ $conn->rollBack(); json_response(['success'=>false,'message'=>'That time has just been booked. Choose another time.'],409); }
 $q=$conn->prepare('SELECT id,service_name,price FROM services WHERE id=?'); $q->execute([$sid]); $service=$q->fetch();
 if(!$service){$conn->rollBack();json_response(['success'=>false,'message'=>'Choose a valid service.'],422);}
 if(!empty($_SESSION['customer_id'])) {
  $cid=(int)$_SESSION['customer_id'];
  $q=$conn->prepare('SELECT full_name,phone,email FROM customers WHERE id=?'); $q->execute([$cid]); $account=$q->fetch();
  if(!$account){$conn->rollBack();json_response(['success'=>false,'message'=>'Your customer account could not be found. Please log in again.'],401);}
  $name=$account['full_name']; $phone=$account['phone']; $customerEmail=$account['email'];
 } else {
  $q=$conn->prepare('INSERT INTO customers(full_name,phone) VALUES(?,?)');$q->execute([$name,$phone]);$cid=(int)$conn->lastInsertId();
 }
 $customerEmail = $customerEmail ?? (string)($_SESSION['customer_email'] ?? '');
 $ref='EC-'.strtoupper(bin2hex(random_bytes(5))); 
 $q=$conn->prepare("INSERT INTO appointments(booking_reference,customer_id,service_id,appointment_date,appointment_time,status) VALUES(?,?,?,?,?,'Pending')");
 $q->execute([$ref,$cid,$sid,$date,$time.':00']);$conn->commit();
 $mailSent = $customerEmail !== '' && elite_send_email($customerEmail, 'Your ELITE CUTS booking is received', '<h2>Booking received</h2><p>Hello '.htmlspecialchars($name,ENT_QUOTES,'UTF-8').', your appointment request has been received and is currently <strong>Pending</strong>.</p><p><strong>Reference:</strong> '.htmlspecialchars($ref).'<br><strong>Service:</strong> '.htmlspecialchars($service['service_name']).'<br><strong>Date:</strong> '.htmlspecialchars($date).'<br><strong>Time:</strong> '.htmlspecialchars($time).'<br><strong>Price:</strong> ₦'.number_format((float)$service['price'],2).'</p><p>We will notify you when the appointment is confirmed.</p>');
 json_response(['success'=>true,'email_sent'=>$mailSent,'appointment'=>['reference'=>$ref,'name'=>$name,'phone'=>$phone,'service'=>$service['service_name'],'price'=>(float)$service['price'],'date'=>$date,'time'=>$time,'status'=>'Pending']],201);
} catch(Throwable $e){if($conn->inTransaction())$conn->rollBack();error_log($e->getMessage());json_response(['success'=>false,'message'=>'Booking could not be saved. Please try again.'],500);}
finally{$q=$conn->prepare('SELECT RELEASE_LOCK(?)');$q->execute([$lockName]);}
