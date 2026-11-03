<?php
require_once __DIR__.'/includes/bootstrap.php';
if (empty($_SESSION['customer_id'])) { header('Location: customer_login.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Method not allowed.'); }
$reference = trim((string)($_POST['reference'] ?? ''));
if ($reference === '') { header('Location: my_bookings.php'); exit; }
$q = $conn->prepare("UPDATE appointments SET cancellation_requested=1 WHERE booking_reference=? AND customer_id=? AND status IN ('Pending','Confirmed') AND cancellation_requested=0");
$q->execute([$reference, (int)$_SESSION['customer_id']]);
header('Location: my_bookings.php');
exit;
