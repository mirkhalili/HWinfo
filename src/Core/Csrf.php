<?php
declare(strict_types=1);
final class Csrf {
    public static function token(): string { Auth::start(); return $_SESSION['_csrf'] ??= bin2hex(random_bytes(32)); }
    public static function check(?string $token): void {
        Auth::start();
        if (!$token || !hash_equals($_SESSION['_csrf'] ?? '',$token)) { http_response_code(419); exit('CSRF validation failed'); }
    }
}
