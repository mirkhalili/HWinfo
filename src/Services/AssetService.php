<?php
declare(strict_types=1);
final class AssetService {
    public static function setStatus(int $id,string $status): void {
        if(!in_array($status,['approved','rejected','deleted'],true)) throw new InvalidArgumentException();
        $pdo=db();$s=$pdo->prepare("SELECT * FROM computers WHERE id=?");$s->execute([$id]);$before=$s->fetch();if(!$before)throw new RuntimeException('Asset not found');
        $u=$pdo->prepare("UPDATE computers SET status=? WHERE id=?");$u->execute([$status,$id]);
        AuditService::log((int)Auth::user()['id'],'STATUS','computer',$id,$before,['status'=>$status]);
    }
    public static function assign(int $id,?int $personnel,?string $location,?string $assetNo): void {
        $pdo=db();if($assetNo!==null&&$assetNo!==''){ $q=$pdo->prepare("SELECT id FROM computers WHERE asset_number=? AND id<>?");$q->execute([$assetNo,$id]);if($q->fetch())throw new RuntimeException('Asset number already exists.');}
        $s=$pdo->prepare("SELECT * FROM computers WHERE id=?");$s->execute([$id]);$before=$s->fetch();if(!$before)throw new RuntimeException('Asset not found');
        $u=$pdo->prepare("UPDATE computers SET personnel_id=?,location=?,asset_number=? WHERE id=?");$u->execute([$personnel,$location,$assetNo,$id]);
        AuditService::log((int)Auth::user()['id'],'ASSIGN','computer',$id,$before,['personnel_id'=>$personnel,'location'=>$location,'asset_number'=>$assetNo]);
    }
}