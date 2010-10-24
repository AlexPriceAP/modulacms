<?php

namespace Modula\Framework;

/**
 * @ingroup cache
 */
class FileCacheAdaptor extends CacheAdaptor {

    public function get($group, $id) {
        $filename = $this->getFile($group, $id);
        if (file_exists($filename) && is_readable($filename)) {
            $data = unserialize(preg_replace('/^.*\n/', '', file_get_contents($filename)));
            // If cache didn't unserialize, or it's expired - delete
            if (!$data || time() > $data['expire']) {
                $this->remove($group, $id);
                return false;
            } else {
                return $data['data'];
            }
        } else {
            return false;
        }
    }

    public function set($group, $id, $data, $expire = 3600) {
        $filename = $this->getFile($group, $id);
        if (file_put_contents($filename,
                        // Add a die statement to the top of the cache file
                        "<?php die('Access Denied'); ?>\n"
                        . serialize(array('data' => $data, 'expire' => time() + $expire)), LOCK_EX)) {
            return true;
        } else {
            return false;
        }
    }

    public function remove($group, $id) {
        FileSystem::deleteFile($this->getFile($group, $id));
    }

    public function clear($group) {
        FileSystem::deleteDir($this->getDir($group), true);
    }

    public function gc() {
        
    }

    private function getDir($group) {
        $folder = dir_CACHE . '/' . md5($group);
        FileSystem::createDir($folder);
        return $folder;
    }

    private function getFile($group, $id) {
        return $this->getDir($group) . '/' . md5($id) . '.php';
    }

}

?>