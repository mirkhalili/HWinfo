# HWinfo collector
# Compatible with Windows PowerShell 5.1+ and PowerShell 7+
# Produces hardware-info.csv matching the 75-column HWinfo import contract.
$ErrorActionPreference="SilentlyContinue"
$dir=Split-Path -Parent $MyInvocation.MyCommand.Definition
$out=Join-Path $dir "hardware-info.csv"
function V($o,$p){if($null -eq $o){return ""};try{$v=$o.$p;if($null -eq $v){return ""};return [string]$v}catch{return ""}}
function J($a,$sep=", "){return (@($a)|?{$_ -ne ""}) -join $sep}
function GB($n){if(!$n){return 0};return [math]::Round(([double]$n/1GB),2)}
function W($s){try{[Management.ManagementDateTimeConverter]::ToDateTime($s)}catch{""}}
$c=Get-CimInstance Win32_ComputerSystem;$os=Get-CimInstance Win32_OperatingSystem;$bios=Get-CimInstance Win32_BIOS;$prod=Get-CimInstance Win32_ComputerSystemProduct
$cpus=Get-CimInstance Win32_Processor;$mem=Get-CimInstance Win32_PhysicalMemory;$board=Get-CimInstance Win32_BaseBoard
$disks=Get-CimInstance Win32_DiskDrive;$drives=Get-CimInstance Win32_LogicalDisk -Filter "DriveType=3"
$nics=Get-CimInstance Win32_NetworkAdapter;$ncfg=Get-CimInstance Win32_NetworkAdapterConfiguration
$g=Get-CimInstance Win32_VideoController;$sound=Get-CimInstance Win32_SoundDevice;$mon=Get-CimInstance Win32_DesktopMonitor
$pf=Get-CimInstance Win32_PageFileUsage
$printers=Get-CimInstance Win32_Printer;$scanners=Get-CimInstance Win32_PnPEntity -Filter "ClassGuid='{6bdd1fc6-810f-11d0-bec7-08002be2092f}'"
$ramTypes=@();foreach($m in $mem){$ramTypes+=if($m.SMBIOSMemoryType){$m.SMBIOSMemoryType}else{$m.MemoryType}}
$diskText=@();foreach($d in $disks){$diskText+=(V $d Model)+" / "+(GB $d.Size)+" GB / Serial:"+(V $d SerialNumber)+" / Interface:"+(V $d InterfaceType)+" / Media:"+(V $d MediaType)}
$driveText=@();foreach($d in $drives){$driveText+=(V $d DeviceID)+": Total "+(GB $d.Size)+" GB / Free "+(GB $d.FreeSpace)+" GB"}
$mac=@();$ips=@();$masks=@();$gw=@();$dns=@();$net=@()
foreach($n in $nics){$x=$ncfg|? Index -eq $n.Index|select -First 1;$i=@($x.IPAddress);$m=@($x.IPSubnet);$gg=@($x.DefaultIPGateway);$dd=@($x.DNSServerSearchOrder);$mac+=V $n MACAddress;$ips+=$i;$masks+=$m;$gw+=$gg;$dns+=$dd;$net+=("Name:"+(V $n Name)+" / MAC:"+(V $n MACAddress)+" / IP:"+($i -join "; ")+" / Mask:"+($m -join "; ")+" / Gateway:"+($gg -join "; ")+" / DNS:"+($dd -join "; ")+" / Status:"+(V $n NetConnectionStatus)+" / Physical:"+(V $n PhysicalAdapter)+" / Speed:"+(V $n Speed))}
$pnames=@();$pports=@();$pdrivers=@();$pdetails=@();$def="";foreach($p in $printers){if((V $p Name)-like "Adobe*"){continue};$pnames+=V $p Name;$pports+=V $p PortName;$pdrivers+=V $p DriverName;if($p.Default){$def=V $p Name};$pdetails+=(V $p Name)+" / Port:"+(V $p PortName)+" / Driver:"+(V $p DriverName)+" / Default:"+(V $p Default)+" / Shared:"+(V $p Shared)+" / Status:"+(V $p PrinterStatus)}
$sname=@();$sman=@();$sdet=@();foreach($s in $scanners){$name=V $s Name;if(!$name -or $name -like "Microsoft*" -or $name -like "WSD Scan*"){continue};$sname+=$name;$sman+=V $s Manufacturer;$sdet+=($name+" / Manufacturer:"+(V $s Manufacturer)+" / Class:"+(V $s PNPClass)+" / Status:"+(V $s Status)+" / DeviceID:"+(V $s DeviceID))}
$boot=$os.LastBootUpTime;$row=[ordered]@{
ComputerName=$env:COMPUTERNAME;UserName=$env:USERNAME;Manufacturer=(V $c Manufacturer);Model=(V $c Model);SystemType=(V $c SystemType);SystemSerial=(V $bios SerialNumber);SystemUUID=(V $prod UUID);Domain=(V $c Domain);Workgroup=(if(!$c.PartOfDomain){V $c Workgroup}else{""})
OSName=(V $os Caption);OSVersion=(V $os Version);OSBuild=(V $os BuildNumber);OSArchitecture=(V $os OSArchitecture)
CPUName=(J ($cpus|% Name));CPUManufacturer=(J ($cpus|% Manufacturer));CPUCores=(J ($cpus|% NumberOfCores));CPULogicalProcessors=(J ($cpus|% NumberOfLogicalProcessors));CPUMaxClockMHz=(J ($cpus|% MaxClockSpeed))
RAMTotalGB=(GB $c.TotalPhysicalMemory);RAMSlotCount=$mem.Count;RAMInstalledSlots=$mem.Count;RAMManufacturers=(J ($mem|% Manufacturer));RAMPartNumbers=(J ($mem|% PartNumber));RAMSerialNumbers=(J ($mem|% SerialNumber));RAMSpeedsMHz=(J ($mem|% Speed));RAMType=(J $ramTypes);RAMSlotDetails=(J ($mem|%{(V $_ DeviceLocator)+": "+(GB $_.Capacity)+" GB / Speed:"+(V $_ Speed)+" MHz / Manufacturer:"+(V $_ Manufacturer)+" / PartNumber:"+(V $_ PartNumber)+" / Serial:"+(V $_ SerialNumber)}))
MotherboardManufacturer=(V $board Manufacturer);MotherboardProduct=(V $board Product);MotherboardVersion=(V $board Version);MotherboardSerial=(V $board SerialNumber)
BIOSManufacturer=(V $bios Manufacturer);BIOSVersion=(V $bios SMBIOSBIOSVersion);BIOSSerial=(V $bios SerialNumber);BIOSReleaseDate=(W $bios ReleaseDate)
DiskDetails=(J $diskText);LogicalDriveDetails=(J $driveText);NetworkDetails=(J $net " || ");MACAddresses=(J $mac);IPAddresses=(J $ips);SubnetMasks=(J $masks);Gateways=(J $gw);DNSServers=(J $dns)
GPUDetails=(J ($g|%{(V $_ Name)+" / Driver:"+(V $_ DriverVersion)}));SoundDetails=(J ($sound|% Name));MonitorDetails=(J ($mon|%{(V $_ Name)+" / Manufacturer:"+(V $_ MonitorManufacturer)}));PageFileDetails=(J ($pf|%{(V $_ Name)+" / Allocated:"+(V $_ AllocatedBaseSize)+" MB / Usage:"+(V $_ CurrentUsage)+" MB"}))
AntivirusDetails="";PrinterCount=$pnames.Count;DefaultPrinterName=$def;PrinterNames=(J $pnames);PrinterPorts=(J $pports);PrinterDrivers=(J $pdrivers);DuplexPrinters="";PrinterDetails=(J $pdetails " || ");ScannerCount=$sname.Count;ScannerNames=(J $sname);ScannerManufacturers=(J $sman);ScannerDetails=(J $sdet " || ")
BootCount="";NormalShutdownCount="";UnexpectedShutdownCount="";UserShutdownCount="";LastBootTime=(W $boot);LastShutdownTime="";CompletedSessionCount="";CurrentSessionHours="";CurrentSessionDuration="";TotalUsageHours="";TotalUsageDuration="";AverageSessionHours="";AverageSessionDuration="";LongestSessionHours="";LongestSessionDuration="";CollectedAt=Get-Date
}
[pscustomobject]$row|Export-Csv -NoTypeInformation -Encoding UTF8 -Path $out
Write-Host "HWinfo inventory written to $out"
