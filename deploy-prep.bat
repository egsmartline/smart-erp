@echo off
chcp 65001 >nul
echo ===== تجهيز المشروع للإنتاج =====
echo.

:: 1. نسخ env
echo [1/6] نسخ .env.production إلى .env...
copy /Y .env.production .env >nul

:: 2. توليد مفتاح
echo [2/6] توليد APP_KEY...
C:\php\php.exe artisan key:generate --force

:: 3. كاش الإعدادات
echo [3/6] تخزين الإعدادات...
C:\php\php.exe artisan config:cache

:: 4. كاش المسارات
echo [4/6] تخزين المسارات...
C:\php\php.exe artisan route:cache

:: 5. كاش القوالب
echo [5/6] تخزين القوالب...
C:\php\php.exe artisan view:cache

:: 6. تفعيل الـ soft links
echo [6/6] إنشاء الروابط...
C:\php\php.exe artisan storage:link --force >nul 2>&1

echo.
echo ===== تم التجهيز! =====
echo الآن شغّل ملف export-db.bat لتصدير قاعدة البيانات
pause
