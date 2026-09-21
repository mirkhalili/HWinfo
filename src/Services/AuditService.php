<?php
declare(strict_types=1);
final class AuditService {
    public static function log(int $userId,string $action,string $entity,int $entityId,?array $before=null,?array $after=null): void {
        $s=db()->prepare("INSERT INTO audit_logs(user_id,action,entity,entity_id,before_json,after_json,ip_address,user_agent) VALUES(?,?,?,?,?,?,?,?)");
        $s->execute([$userId,$action,$entity,$entityId,$before?json_encode($before,JSON_UNESCAPED_UNICODE):null,$after?json_encode($after,JSON_UNESCAPED_UNICODE):null,$_SERVER['REMOTE_ADDR']??null,substr($_SERVER['HTTP_USER_AGENT']??'',0,500)]);
    }
}
