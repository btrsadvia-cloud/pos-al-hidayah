@echo off
title POS Toko Al-Hidayah - Launcher
color 0A

echo ======================================================
echo          POS TOKO AL-HIDAYAH - SISTEM KASIR
echo ======================================================
echo.

:: 1. Cek MySQL
echo [*] Memeriksa status database MySQL...
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I "mysqld.exe" >NUL
if %ERRORLEVEL% NEQ 0 (
    echo [*] Menjalankan MySQL database server...
    if exist "C:\xampp\mysql\bin\mysqld.exe" (
        start "" /B "C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini" --standalone
        timeout /t 3 /nobreak >NUL
        echo [OK] MySQL berhasil dijalankan.
    ) else (
        echo [!] MySQL tidak ditemukan di C:\xampp\mysql\bin\
        echo     Pastikan XAMPP sudah terpasang.
    )
) else (
    echo [OK] MySQL sudah aktif.
)

echo.

:: 2. Cek Server Laravel
echo [*] Memeriksa server aplikasi POS (Port 8000)...
netstat -ano | findstr ":8000 " | findstr "LISTENING" >NUL 2>&1
if %ERRORLEVEL% EQU 0 (
    echo [OK] Server POS sudah berjalan di port 8000.
) else (
    echo [*] Memulai server Laravel POS...
    cd /d "c:\POS_Toko_AlHidayah"
    start "POS Server - Toko Al-Hidayah" /min "C:\xampp\php\php.exe" artisan serve --host=127.0.0.1 --port=8000
    timeout /t 2 /nobreak >NUL
    echo [OK] Server POS aktif di http://127.0.0.1:8000
)

echo.
echo ======================================================
echo    Membuka aplikasi di browser...
echo    URL: http://127.0.0.1:8000
echo ======================================================
echo.

:: 3. Buka browser
start "" "http://127.0.0.1:8000"

echo Aplikasi siap digunakan!
echo Jendela server Laravel berjalan di latar belakang.
echo.
timeout /t 5
