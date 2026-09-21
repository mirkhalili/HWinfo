<?php
declare(strict_types=1);
require __DIR__.'/../config/database.php';
if(PHP_SAPI!=='cli'){exit("CLI only\n");}
if($argc<3){exit("Usage: php scripts/create-admin.php username password [full name]\n");}
[$_, $username,$password]=$argv;$name=$argv[3]??$username;
$hash=password_hash($password,PASSWORD_DEFAULT);
$s=db()->prepare("INSERT INTO users(username,password_hash,role,full_name) VALUES(?,?, 'admin',?)");
$s->execute([$username,$hash,$name]);echo "Admin created.\n";
