# قرارداد CSV

قرارداد پذیرفته‌شده دقیقاً 75 ستون دارد و ترتیب ستون‌ها بخشی از قرارداد است. نمونه واقعی شامل ComputerName، UserName، Manufacturer، Model، SystemSerial، SystemUUID، مشخصات OS/CPU/RAM، دیسک، شبکه، GPU، صدا، مانیتور، PageFile، Antivirus، Printer/Scanner و آمار Boot/Session است.

Collector مرجع در `powershell/hardware-info.ps1` قرار می‌گیرد. سامانه header و تعداد ستون را دقیق بررسی می‌کند و در صورت مغایرت کل import را متوقف می‌کند.
