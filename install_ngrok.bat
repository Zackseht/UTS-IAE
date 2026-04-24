@echo off
echo ===================================================
echo   Mengunduh dan Menginstal Ngrok secara otomatis...
echo ===================================================
echo.

if exist "ngrok.exe" (
    echo [1/3] Ngrok sudah ada, melewati proses download...
) else (
    :: Download Ngrok zip file using curl
    echo [1/3] Mengunduh Ngrok...
    curl -L -# -o ngrok.zip https://bin.equinox.io/c/bNyj1mQVY4c/ngrok-v3-stable-windows-amd64.zip

    :: Extract the zip using tar
    echo [2/3] Mengekstrak file...
    tar -xf ngrok.zip

    :: Clean up zip file
    del ngrok.zip
)

echo [3/3] Instalasi selesai!
echo.
echo Mengatur Authtoken Ngrok...
ngrok.exe config add-authtoken 36H5vzlJxFkuhBx2X6pollHn0yj_VbcG5ZGChBXp1TfqgsTw
echo Token berhasil ditambahkan!

echo.
echo Membuka Ngrok di port 8000...
echo PERHATIAN: Silakan Copy URL yang berawalan "https://" 
echo dan masukkan ke Dashboard Midtrans.
echo ===================================================
echo.

:: Run ngrok
ngrok.exe http 8000

pause
