<?php

namespace Modula\Framework;

final class ModLoader extends Object {

    private static $_loadedModules = array();

    /**
     * Takes an array of module indentfiers and fetches the related modules
     * from storage or cache.
     *
     * @param array $modids
     * @param bool $required
     */
    public static function load(array $modids, $required = false) {
        if (is_array($modids)) {
            foreach ($modids as $modid) {
                if (!self::isLoaded($modid)) {
                    $modinfo = self::getModuleInfo($modid);
                    foreach ($modinfo->include as $include) {
                        $file = self::getModulePath($modid) . '/' . $include['file'];
                        $class = (string) $include['class'];
                        require($file);
                        return self::$_loadedModules[$modid] = new $class();
                    }
                } else {
                    return self::$_loadedModules[$modid];
                }
            }
        }
    }

    /**
     * Takes a module id and returns the absolute path to it's module.xml file.
     *
     * @param string $modid
     * @return string
     */
    public static function getModulePath($modid) {
        return dir_MODULES . '/' . $modid;
    }

	/**
	 * Returns true if a requested module is already loaded
	 *
	 * @param string $modid
	 * @return bool
	 */
    public static function isLoaded($modid) {
        return array_key_exists($modid, self::$_loadedModules) ? true : false;
    }

    /**
     * Takes a module id and returns an array of module information.
     *
     * @param string $file
     * @return array
     */
    public static function getModuleInfo($modid) {
        $file = self::getModulePath($modid) . '/module.xml';
        if (is_readable($file)) {
            $data = self::parseModuleXML($file);
            if ($data) {
                return $data;
            } else {
                throw new Exception("Error parsing module XML data from $file");
            }
        }
        return false;
    }

    /**
     * Returns an array of module information based on a list of filenames.
     * 
     * @param array $files
     * @return array
     */
    public static function parseModuleXML($file) {
        if (file_exists($file)) {
            if (is_readable($file)) {
                $data = simplexml_load_file($file);
                return $data;
            } else {
                throw new Exception("Could not read module file: $file");
            }
        } else {
            throw new Exception("Module file does not exists: $file");
        }
    }

}

?>