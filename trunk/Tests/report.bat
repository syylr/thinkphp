@echo off
php -d safe_mode=Off -d memory_limit=64M "../ThinkPHP/Tools/phpunit.php" --configuration config.xml AllTests.php
pause