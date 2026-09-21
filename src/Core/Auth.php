<?php
declare(strict_types=1);
final class Auth {
    public static function start(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_set_cookie_params(['httponly'=>true,'samesite'=>'Lax','secure'=>!empty($_SERVER['HTTPS'])]);
            session_start();
        }
    }
    public static function login(string $username, string $password): bool {
        self::start();
        $s=db()->prepare("SELECT id,username,password_hash,role,full_name,active FROM users WHERE username=? LIMIT 1");
        $s->execute([$username]); $u=$s->fetch();
        if (!$u || !$u['active'] || !password_verify($password,$u['password_hash'])) return false;
        session_regenerate_id(true); $_SESSION['user']=$u; unset($_SESSION['user']['password_hash']); return true;
    }
    public static function logout(): void { self::start(); $_SESSION=[]; session_destroy(); }
    public static function user(): ?array { self::start(); return $_SESSION['user'] ?? null; }
    public static function requireLogin(): void { if (!self::user()) { header('Location:?r=login'); exit; } }
    public static function requireRole(array $roles): void {
        self::requireLogin(); if (!in_array(self::user()['role'],$roles,true)) { http_response_code(403); exit('403'); }
    }
}
