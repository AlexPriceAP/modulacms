<?php

/**
 * Handles all filesystem related tasks throughout the system.
 *
 * @defgroup filesystem
 * @ingroup application
 */
class FileSystem
{

    /**
     * Copy the source file to the target file, overwritting any pre existing
     * file if $overwrite is set to TRUE.
     *
     * @param string $source
     * @param string $target
     * @param bool $overwrite
     * @return bool
     */
    public static function copyFile($source, $target, $overwrite = false)
    {
        if (file_exists($source)) {
            if (file_exists($target) && $overwrite) {
                $sourcestat = stat($source);
                $targetstat = stat($target);
                if ($sourcestat['mtime'] > $targetstat['mtime']) {
                    copy($source, $target);
                } else {
                    return false;
                }
            } else {
                return copy($source, $target);
            }
        } else {
            return false;
        }
    }

    /**
     * Copy the source to target and then delete if that went ok, return false
     * if either fail.
     *
     * @param string $source
     * @param string $target
     * @return bool
     */
    public static function moveFile($source, $target)
    {
        return $this->copyFile($source, $target) ? $this->deleteFile($source) : false;
    }

    /**
     * Create the specified directory or return false if it already exists.
     *
     * @param string $path
     * @param int $mode
     * @return bool
     */
    public static function createDir($path, $mode = 0777)
    {
        if (!is_dir($path)) {
            return mkdir($path, $mode, true);
        } else {
            return false;
        }
    }

    /**
     * Delete the specified directory and also all it's content if $recursive is
     * set to TRUE;
     *
     * @param string $path
     * @param bool $recursive
     * @return bool
     */
    public static function deleteDir($path, $recursive = false)
    {
        if (is_dir($path)) {
            if ($recursive) {
                $iterator = new RecursiveDirectoryIterator($path);
                foreach (new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST) as $file) {
                    if ($file->isDir()) {
                        rmdir($file->getPathname());
                    } else {
                        unlink($file->getPathname());
                    }
                }
            }
            return rmdir($dir);
        } else
            return false;
    }

    public static function dirList($path)
    {
        $handle = opendir($path);
        $dirs = array();
        if ($handle) {
            while ($file = readdir($handle)) {
                if ($file != "." && $file != "..") {
                    $dirs[] = $file;
                }
            }
            closedir($handle);
            return $dirs;
        } else {
            return false;
        }
    }

    /**
     * Delete the given file path, return false if the file doesn't exist.
     *
     * @param string $path
     * @return bool
     */
    public static function deleteFile($path)
    {
        return file_exists($path) ? unlink($path) : false;
    }

    /**
     * Return the mimetype of the file, return false if the file doesn't exist.
     *
     * @param string $path
     * @return string
     */
    public static function mimeType($path)
    {
        return file_exists($path) ? mime_content_type($path) : false;
    }

    /**
     * Creates the specified file if it doesn't already exist.
     *
     * @param string $path
     * @return bool
     */
    public static function createFile($path)
    {
        return!file_exists($path) ? touch($path) : false;
    }

    /**
     * Change mode of the given file path, return false if path doesn't exist.
     *
     * @param string $file
     * @param int $mode
     * @param int $umask
     * @return bool
     */
    public static function chmod($path, $mode, $umask = 0000)
    {
        if (file_exists($path)) {
            // Store the current umask so we can set back after we're finished
            $umask_store = umask();
            umask($umask);
            chmod($path, $mode);
            // Reset the umask back
            umask($umask_store);
            return true;
        } else {
            return false;
        }
    }

    public static function findFile($path, $filename)
    {
        try {
            foreach (new recursiveIteratorIterator(new recursiveDirectoryIterator($path)) as $file) {
                if (basename($file) == $filename) {
                    return $file;
                }
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

}
?>