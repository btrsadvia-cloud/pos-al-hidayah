@echo off
title POS Toko Al-Hidayah - Launcher
color 0A

echo ============================================
echo   POS Toko Al-Hidayah - Sistem Kasir
echo ============================================
echo.

:: Cek apakah MySQL sudah jalan
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I "mysqld.exe" >NUL
if %ERRORLEVEL% NEQ 0 (
    echo [*] Menjalankan MySQL...
    if exist "C:\xampp\mysql\bin\mysqld.exe" (
        start "" /B "C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini"
        timeout /t 3 /nobreak >NUL
        echo [OK] MySQL berhasil dijalankan.
    ) else (
        echo [!] MySQL tidak ditemukan di C:\xampp\mysql\bin\
        echo     Pastikan XAMPP sudah terinstal dan MySQL aktif.
        echo     Coba jalankan XAMPP Control Panel secara manual.
        pause
    )
) else (
    echo [OK] MySQL sudah berjalan.
)

echo.

:: Cek apakah port 8000 sudah dipakai
netstat -ano | findstr ":8000 " | findstr "LISTENING" >NUL 2>&1
if %ERRORLEVEL% EQU 0 (
    echo [OK] Server Laravel sudah berjalan di port 8000.
) else (
    echo [*] Menjalankan server Laravel...
    start "" /B cmd /c "cd /d c:\POS_Toko_AlHidayah && C:\xampp\php\php.exe artisan serve --host=127.0.0.1 --port=8000"
    timeout /t 2 /nobreak >NUL
    echo [OK] Server Laravel aktif di http://127.0.0.1:8000
)

echo.
echo ============================================
echo   Membuka browser...
echo   URL: http://127.0.0.1:8000
echo ============================================
echo.

:: Buka browser
start "" "http://127.0.0.1:8000"

echo Sistem POS siap digunakan!
echo Jangan tutup jendela ini selama menggunakan POS.
echo Tekan Ctrl+C atau tutup jendela ini untuk menghentikan server.
echo.
pause
