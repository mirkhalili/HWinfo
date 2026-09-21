<?php
declare(strict_types=1);
final class ImportService {
    private static function names(string $value): array { return array_values(array_filter(array_map('trim',preg_split('/\s*,\s*/',$value)?:[]))); }
    public static function import(array $rows,string $sha,string $name='hardware-info.csv'): int {
        $pdo=db();$pdo->beginTransaction();
        try {
            $q=$pdo->prepare("SELECT id FROM import_batches WHERE sha256=? LIMIT 1");$q->execute([$sha]);if($q->fetch()){throw new RuntimeException('This CSV was already imported.');}
            $s=$pdo->prepare("INSERT INTO import_batches(sha256,original_name,row_count,status,created_by) VALUES(?,?,?,?,?)");$s->execute([$sha,$name,count($rows),'processing',Auth::user()['id']]);$batch=(int)$pdo->lastInsertId();
            foreach($rows as $r){
                $s=$pdo->prepare("INSERT INTO computers(import_batch_id,computer_name,user_name,manufacturer,model,system_serial,system_uuid,os_name,os_version,os_build,raw_json,status) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
                $s->execute([$batch,$r['ComputerName'],$r['UserName'],$r['Manufacturer'],$r['Model'],$r['SystemSerial'],$r['SystemUUID'],$r['OSName'],$r['OSVersion'],$r['OSBuild'],json_encode($r,JSON_UNESCAPED_UNICODE),'pending']);$cid=(int)$pdo->lastInsertId();
                foreach(self::names($r['PrinterNames']) as $name){$x=$pdo->prepare("INSERT INTO printers(computer_id,name,raw_json) VALUES(?,?,?)");$x->execute([$cid,$name,json_encode(['details'=>$r['PrinterDetails']],JSON_UNESCAPED_UNICODE)]);}
                foreach(self::names($r['ScannerNames']) as $name){$x=$pdo->prepare("INSERT INTO scanners(computer_id,name,raw_json) VALUES(?,?,?)");$x->execute([$cid,$name,json_encode(['details'=>$r['ScannerDetails']],JSON_UNESCAPED_UNICODE)]);}
                foreach(self::names($r['MonitorDetails']) as $name){$x=$pdo->prepare("INSERT INTO monitors(computer_id,name,raw_json) VALUES(?,?,?)");$x->execute([$cid,$name,json_encode(['details'=>$r['MonitorDetails']],JSON_UNESCAPED_UNICODE)]);}
                AuditService::log((int)Auth::user()['id'],'IMPORT','computer',$cid,null,['batch'=>$batch,'status'=>'pending']);
            }
            $u=$pdo->prepare("UPDATE import_batches SET status='completed' WHERE id=?");$u->execute([$batch]);$pdo->commit();return $batch;
        }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();throw $e;}
    }
}