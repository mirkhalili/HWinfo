<?php
declare(strict_types=1);
final class CsvImportService {
    public const HEADERS = ["ComputerName","UserName","Manufacturer","Model","SystemType","SystemSerial","SystemUUID","Domain","Workgroup","OSName","OSVersion","OSBuild","OSArchitecture","CPUName","CPUManufacturer","CPUCores","CPULogicalProcessors","CPUMaxClockMHz","RAMTotalGB","RAMSlotCount","RAMInstalledSlots","RAMManufacturers","RAMPartNumbers","RAMSerialNumbers","RAMSpeedsMHz","RAMType","RAMSlotDetails","MotherboardManufacturer","MotherboardProduct","MotherboardVersion","MotherboardSerial","BIOSManufacturer","BIOSVersion","BIOSSerial","BIOSReleaseDate","DiskDetails","LogicalDriveDetails","NetworkDetails","MACAddresses","IPAddresses","SubnetMasks","Gateways","DNSServers","GPUDetails","SoundDetails","MonitorDetails","PageFileDetails","AntivirusDetails","PrinterCount","DefaultPrinterName","PrinterNames","PrinterPorts","PrinterDrivers","DuplexPrinters","PrinterDetails","ScannerCount","ScannerNames","ScannerManufacturers","ScannerDetails","BootCount","NormalShutdownCount","UnexpectedShutdownCount","UserShutdownCount","LastBootTime","LastShutdownTime","CompletedSessionCount","CurrentSessionHours","CurrentSessionDuration","TotalUsageHours","TotalUsageDuration","AverageSessionHours","AverageSessionDuration","LongestSessionHours","LongestSessionDuration","CollectedAt"];
    public static function read(string $path): array {
        if (!is_uploaded_file($path) && !is_readable($path)) throw new RuntimeException('Invalid CSV source');
        $fh=fopen($path,'rb'); $header=fgetcsv($fh);
        if (!$header) throw new RuntimeException('Empty CSV');
        $header=array_map(fn($v)=>trim((string)$v),$header);
        if ($header[0] !== self::HEADERS[0] && str_starts_with($header[0],"\xEF\xBB\xBF")) $header[0]=substr($header[0],3);
        if ($header !== self::HEADERS) throw new RuntimeException('CSV header does not match the approved hardware contract.');
        $rows=[]; while(($r=fgetcsv($fh))!==false){ if(count($r)!==count(self::HEADERS)) throw new RuntimeException('CSV column count mismatch'); $rows[]=array_combine(self::HEADERS,$r); }
        fclose($fh); return $rows;
    }
    public static function hash(string $path): string { return hash_file('sha256',$path); }
}
