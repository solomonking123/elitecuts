<?php
require_once __DIR__.'/includes/bootstrap.php';
require_once __DIR__.'/includes/mailer.php';
require_admin();
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT a.id,a.booking_reference reference,c.full_name name,c.phone,c.email,s.service_name service,s.price,a.appointment_date date,TIME_FORMAT(a.appointment_time,'%H:%i') time,a.status,a.cancellation_requested FROM appointments a JOIN customers c ON c.id=a.customer_id JOIN services s ON s.id=a.service_id ORDER BY a.appointment_date DESC,a.appointment_time DESC";
    json_response(['success'=>true,'appointments'=>$conn->query($sql)->fetchAll()]);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $x = json_decode(file_get_contents('php://input'), true) ?? [];
    $id = filter_var($x['id'] ?? null, FILTER_VALIDATE_INT);
    $action = (string)($x['action'] ?? 'status');
    if (!$id) json_response(['success'=>false,'message'=>'Invalid appointment.'],422);
    $q = $conn->prepare("SELECT a.status,a.cancellation_requested,a.booking_reference,a.appointment_date,TIME_FORMAT(a.appointment_time,'%H:%i') time,c.full_name,c.email,s.service_name FROM appointments a JOIN customers c ON c.id=a.customer_id JOIN services s ON s.id=a.service_id WHERE a.id=?");
    $q->execute([$id]); $a = $q->fetch();
    if (!$a) json_response(['success'=>false,'message'=>'Appointment not found.'],404);
    if ($action === 'reject_cancellation') {
        if (!(int)$a['cancellation_requested']) json_response(['success'=>false,'message'=>'There is no cancellation request to reject.'],409);
        $conn->prepare('UPDATE appointments SET cancellation_requested=0 WHERE id=?')->execute([$id]);
        $sent = elite_send_email((string)$a['email'],'Your ELITE CUTS cancellation request was declined','<h2>Cancellation request declined</h2><p>Hello '.htmlspecialchars($a['full_name'],ENT_QUOTES,'UTF-8').', your request to cancel appointment <strong>'.htmlspecialchars($a['booking_reference']).'</strong> was not approved. Your appointment remains <strong>'.htmlspecialchars($a['status']).'</strong>.</p><p>Date: '.htmlspecialchars($a['appointment_date']).'<br>Time: '.htmlspecialchars($a['time']).'<br>Service: '.htmlspecialchars($a['service']).'</p>');
        json_response(['success'=>true,'email_sent'=>$sent]);
    }
    $status = (string)($x['status'] ?? '');
    if (!in_array($status,['Pending','Confirmed','Completed','Cancelled'],true)) json_response(['success'=>false,'message'=>'Invalid status.'],422);
    $wasCancellationRequest = (int)$a['cancellation_requested'] === 1;
    $conn->prepare('UPDATE appointments SET status=?, cancellation_requested=0 WHERE id=?')->execute([$status,$id]);
    $subject = ''; $heading = ''; $message = '';
    if ($status === 'Confirmed' && $a['status'] !== 'Confirmed') {
        $subject='Your ELITE CUTS appointment is confirmed'; $heading='Appointment confirmed'; $message='Your appointment has been confirmed. We look forward to seeing you.';
    } elseif ($status === 'Cancelled' && $wasCancellationRequest) {
        $subject='Your ELITE CUTS cancellation request was approved'; $heading='Cancellation approved'; $message='Your request to cancel this appointment has been approved.';
    } elseif ($status === 'Cancelled' && $a['status'] !== 'Cancelled') {
        $subject='Your ELITE CUTS appointment was cancelled'; $heading='Appointment cancelled'; $message='Your appointment has been cancelled by the barbershop.';
    } elseif ($status === 'Completed' && $a['status'] !== 'Completed') {
        $subject='Your ELITE CUTS appointment is complete'; $heading='Appointment completed'; $message='Your appointment has been marked as completed. Thank you for choosing ELITE CUTS.';
    }
    $sent = false;
    if ($subject !== '') $sent = elite_send_email((string)$a['email'],$subject,'<h2>'.htmlspecialchars($heading).'</h2><p>Hello '.htmlspecialchars($a['full_name'],ENT_QUOTES,'UTF-8').', '.htmlspecialchars($message,ENT_QUOTES,'UTF-8').'</p><p><strong>Reference:</strong> '.htmlspecialchars($a['booking_reference']).'<br><strong>Service:</strong> '.htmlspecialchars($a['service']).'<br><strong>Date:</strong> '.htmlspecialchars($a['appointment_date']).'<br><strong>Time:</strong> '.htmlspecialchars($a['time']).'<br><strong>Status:</strong> '.htmlspecialchars($status).'</p>');
    json_response(['success'=>true,'email_sent'=>$sent]);
}
json_response(['success'=>false,'message'=>'Method not allowed.'],405);
