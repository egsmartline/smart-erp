@echo off
chcp 65001 >nul
echo ========================================
echo تجهيز المشروع للرفع إلى Hostinger
echo ========================================
echo.

echo 1. نسخ .env.production إلى .env للإنتاج...
copy /Y .env.production .env >nul

echo 2. مسح الكاش...
C:\php\php.exe artisan optimize:clear >nul

echo 3. توليد مفتاح التطبيق...
C:\php\php.exe artisan key:generate --force >nul

echo 4. تخزين الإعدادات...
C:\php\php.exe artisan config:cache >nul

echo 5. تخزين المسارات...
C:\php\php.exe artisan route:cache >nul

echo 6. تخزين القوالب...
C:\php\php.exe artisan view:cache >nul

echo 7. تصدير قاعدة البيانات...
C:\php\php.exe\..\mysqldump -h ::1 -P 3306 -u erp_user -perp_password_2026 smart_erp > database.sql 2>nul
if exist database.sql (
    echo    تم تصدير قاعدة البيانات إلى database.sql
) else (
    echo    ! فشل تصدير قاعدة البيانات، يرجى التصدير يدويًا
)

echo 8. إنشاء ملف ZIP...
if exist deploy.zip del deploy.zip
C:\php\php.exe -r "
\$zip = new ZipArchive();
if (\$zip->open('deploy.zip', ZipArchive::CREATE) === TRUE) {
    \$files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator('C:\\Users\\Admin\\Desktop\\SMART ERP', RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    \$ignore = ['vendor', 'node_modules', '.git', '.env', '.env.backup', '.env.production', 'deploy.zip', 'database.sql', 'prep-deploy.bat'];
    foreach (\$files as \$file) {
        \$path = \$file->getRealPath();
        \$local = substr(\$path, strlen('C:\\Users\\Admin\\Desktop\\SMART ERP\\'));
        \$skip = false;
        foreach (\$ignore as \$i) if (strpos(\$local, \$i) === 0) { \$skip = true; break; }
        if (!\$skip) \$zip->addFile(\$path, \$local);
    }
    \$zip->close();
    echo '   تم إنشاء deploy.zip';
} else {
    echo '   ! فشل إنشاء الملف المضغوط';
}
" 2>nul

echo.
echo ========================================
echo تم التجهيز!
echo الملفات الجاهزة:
echo   deploy.zip  -  ملف المشروع كامل
echo   database.sql - تصدير قاعدة البيانات
echo ========================================
pause
