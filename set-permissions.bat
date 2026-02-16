@echo off
REM Huuto - File Permissions Setup for Windows FTP Clients
REM This file provides instructions for setting permissions via FTP clients on Windows
REM
REM You cannot run this directly on Windows, but you can use these instructions
REM with your FTP client (FileZilla, WinSCP, etc.)

echo ===============================================
echo Huuto - File Permissions Setup Instructions
echo ===============================================
echo.
echo IMPORTANT: You cannot set Unix file permissions directly from Windows.
echo Instead, use your FTP client to set permissions:
echo.
echo ============== USING FILEZILLA ==============
echo 1. Connect to your FTP server
echo 2. Navigate to /home/dajnpsku/public_html/
echo 3. For each directory/file below, right-click and select "File Permissions"
echo 4. Enter the numeric value shown
echo.
echo DIRECTORIES NEEDING WRITE ACCESS (777):
echo   - config/
echo   - uploads/
echo   - storage/
echo   - storage/logs/
echo   - logs/
echo.
echo SECURITY FILES (644):
echo   - .htaccess
echo   - uploads/.htaccess
echo   - logs/.htaccess
echo.
echo ============== USING WINSCP ==============
echo 1. Connect to your FTP server
echo 2. Navigate to public_html
echo 3. Right-click directory/file ^> Properties ^> Permissions
echo 4. Set octal value (777 for directories, 644 for files)
echo.
echo ============== USING CPANEL ==============
echo 1. Log into cPanel
echo 2. Open File Manager
echo 3. Navigate to public_html/
echo 4. Select file/directory ^> Permissions (top menu)
echo 5. Enter numeric value (777 or 644)
echo.
echo For complete instructions, see DEPLOYMENT.md
echo.
pause
