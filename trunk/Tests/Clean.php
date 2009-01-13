<?php
class Clean
{
    public static function run($path)
    {
        $path = realpath($path);
        $dir = dir($path);
        while (FALSE !== ($entry = $dir->read())) {
            if ($entry != '.' and $entry != '..' and $entry != '.svn') {
                $tmppath = $path . DIRECTORY_SEPARATOR . $entry;
                if (is_dir($tmppath)) {
                    Clean::run($tmppath);
                    @rmdir($tmppath);
                } else {
                    unlink($tmppath);
                }
            }
        }
        $dir->close();
    }
}

Clean::run('Docs');
?>