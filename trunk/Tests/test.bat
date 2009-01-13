@echo off
php -d safe_mode=Off -d memory_limit=64M "../ThinkPHP/Tools/phpunit.php" --log-tap Docs/log.txt --log-xml Docs/log.xml --testdox-html Docs/test.html --testdox-text Docs/test.txt AllTests.php
pause