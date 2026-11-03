<?php
declare(strict_types=1);
if(session_status()!==PHP_SESSION_ACTIVE) session_start();
require_once __DIR__.'/../config/database.php';
function json_response(array $data,int $status=200): never {
 http_response_code($status); header('Content-Type: application/json; charset=utf-8');
 echo json_encode($data,JSON_UNESCAPED_UNICODE); exit;
}
function require_admin(): void {
 if(empty($_SESSION['admin_id'])) json_response(['success'=>false,'message'=>'Please log in as administrator.'],401);
}
function h(string $v): string { return htmlspecialchars($v,ENT_QUOTES,'UTF-8'); }
