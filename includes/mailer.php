<?php
/** Minimal SMTP mailer using PHP streams; no Composer package required. */
function elite_send_email(string $to, string $subject, string $html): bool {
    $cfg = require __DIR__ . '/../config/email.php';
    if (empty($cfg['enabled']) || !filter_var($to, FILTER_VALIDATE_EMAIL)) return false;
    $host = (string)$cfg['host']; $port = (int)$cfg['port'];
    $socket = @stream_socket_client("tcp://{$host}:{$port}", $errno, $errstr, 15, STREAM_CLIENT_CONNECT);
    if (!$socket) { error_log("ELITE CUTS SMTP connect failed: {$errstr} ({$errno})"); return false; }
    stream_set_timeout($socket, 15);
    $read = static function() use ($socket): string {
        $response = '';
        do { $line = fgets($socket, 515); if ($line === false) break; $response .= $line; }
        while (isset($line[3]) && $line[3] === '-');
        return $response;
    };
    $command = static function(string $cmd, array $expected) use ($socket, $read): bool {
        fwrite($socket, $cmd . "\r\n"); $reply = $read();
        $code = (int)substr($reply, 0, 3);
        if (!in_array($code, $expected, true)) { error_log('ELITE CUTS SMTP error: ' . trim($reply)); return false; }
        return true;
    };
    $greeting = $read();
    if ((int)substr($greeting, 0, 3) !== 220 || !$command('EHLO localhost', [250]) || !$command('STARTTLS', [220])) { fclose($socket); return false; }
    if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) { fclose($socket); error_log('ELITE CUTS SMTP TLS negotiation failed'); return false; }
    if (!$command('EHLO localhost', [250]) || !$command('AUTH LOGIN', [334]) || !$command(base64_encode((string)$cfg['username']), [334]) || !$command(base64_encode((string)$cfg['password']), [235]) || !$command('MAIL FROM:<' . $cfg['from_email'] . '>', [250]) || !$command('RCPT TO:<' . $to . '>', [250, 251]) || !$command('DATA', [354])) { fclose($socket); return false; }
    $fromName = '=?UTF-8?B?' . base64_encode((string)$cfg['from_name']) . '?=';
    $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    $body = "<!doctype html><html><body style=\"font-family:Arial,sans-serif;color:#222;line-height:1.6\">" . $html . "<p style=\"color:#777;font-size:12px\">ELITE CUTS — Online Barbershop Booking</p></body></html>";
    $message = "From: {$fromName} <{$cfg['from_email']}>\r\nTo: <{$to}>\r\nSubject: {$encodedSubject}\r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\n\r\n" . str_replace(["\r\n", "\r", "\n"], "\r\n", $body);
    $message = preg_replace('/(?m)^\./', '..', $message);
    fwrite($socket, $message . "\r\n.\r\n"); $sent = (int)substr($read(), 0, 3) === 250;
    $command('QUIT', [221]); fclose($socket); return $sent;
}
