<?php
declare(strict_types=1);

final class Auth {
    public static function start(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            $sessionPath = getenv('IHW_SESSION_PATH') ?: '/tmp/hwinfo-sessions';
            if (!is_dir($sessionPath)) { @mkdir($sessionPath, 0700, true); }
            if (is_dir($sessionPath) && is_writable($sessionPath)) {
                session_save_path($sessionPath);
            }

            $forwardedProto = strtolower((string)($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''));
            $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $forwardedProto === 'https';

            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => $secure,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

            ini_set('session.use_strict_mode', '1');
            ini_set('session.use_only_cookies', '1');
            ini_set('session.cookie_httponly', '1');
            ini_set('session.cookie_samesite', 'Lax');
            session_start();
        }
    }

    public static function login(string $username, string $password): bool {
        self::start();
        $s=db()->prepare("SELECT id,username,password_hash,role,full_name,active FROM users WHERE username=? LIMIT 1");
        $s->execute([$username]); $u=$s->fetch();
        if (!$u || !$u['active'] || !password_verify($password,$u['password_hash'])) return false;

        session_regenerate_id(true);
        $_SESSION['user']=$u;
        unset($_SESSION['user']['password_hash']);
        unset($_SESSION['_csrf']);

        return true;
    }

    public static function logout(): void {
        self::start();
        $_SESSION=[];
        if (ini_get('session.use_cookies')) {
            $p=session_get_cookie_params();
            setcookie(session_name(),'',time()-42000,$p['path']??'/', $p['domain']??'', (bool)($p['secure']??false), (bool)($p['httponly']??true));
        }
        session_destroy();
    }

    public static function user(): ?array { self::start(); return $_SESSION['user'] ?? null; }
    public static function requireLogin(): void { if (!self::user()) { header('Location:?r=login'); exit; } }
    public static function requireRole(array $roles): void {
        self::requireLogin();
        if (!in_array(self::user()['role'],$roles,true)) { http_response_code(403); exit('403'); }
    }
}
