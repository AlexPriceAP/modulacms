<?php

namespace Modula\Framework;

/**
 * @defgroup modules
 * @ingroup application
 */
class ModuleHandler {

    private $_loadedModule = array();

    private function listModules() {
        return FileSystem::dirList(dir_MODULES);
    }

    /**
     * Loops though every installed module and fetches all associated includes
     */
    /* public function loadModules()
      {
      if (!$modules = Factory::getCache()->get('modules', 'installed')) {
      $modules = Factory::getDatabase()->dbSelect('SELECT * FROM modules')->fetchAll(PDO::FETCH_ASSOC);
      Factory::getCache()->set('modules', 'installed', $modules);
      }
      foreach ($modules as $module) {
      $file = dir_MODULES . '/' . $module['name'] . '/module.xml';
      $data = self::parseModuleXML($file);
      if (is_readable($file)) {
      $name = (string) $data['name'];
      if (!array_key_exists($name, self::$_modules)) {
      foreach ($data->include as $include) {
      $file = str_replace('{dir_MODULES}', dir_MODULES, (string) $include['file']);
      $class = (string) $include['class'];
      // if(file_exists($file)) {
      //include_once($file);
      if (class_exists($class)) {
      self::$_modules[$name] = new $class;
      // @todo Finish event registering
      Factory::getEventDispatcher()->registerEventCallback(self::$_modules[$name]);
      } else {
      throw new Exception("Module class not found: $class");
      }
      //                        }else {
      //                            throw new Exception("Module include not found: $file");
      //                        }
      }
      } else {
      throw new Exception("Attemped to load multiple modules with the same name {$name}");
      }
      }
      }
      } */

    /**
     * Tries to return a previously loaded module or pass on to the loadModule
     * method to handle the loading.
     */
    public function getModule($id) {
        if ($this->isLoaded($id)) {
            return $this->_loadedModules[$id];
        } else {
            try {
                return $this->loadModule($id);
            } catch (Exception $e) {
                die("Problem loading module: {$e->getMessage()}");
            }
        }
    }

    private function loadModule($id) {
        $path = dir_MODULES . '/' . $id;
        $classfile = $path . '/module.php';
        $classname = ucfirst(strtolower($id)) . 'Module';

        if (is_readable($classfile)) {
            $this->_loadedModule[$id] = new $classname;
        } else {
            throw new Exception("Class file not readable: $classfile");
        }

        return $this->_loadedModule[$id];
    }

    private function isLoaded($id) {
        return array_key_exists($id, $this->_loadedModules) && is_object($this->_loadedModules[$id]) ? true : false;
    }

    private function getDominantModule($type, $id = null) {
        $module = Factory::getDb()->dbSelect("SELECT *
                                            FROM modules
                                            WHERE id = (SELECT MAX(order)
                                            FROM modules
                                            WHERE" . (is_null($id) ? "type = '{:type}'" : "id = :id") . ")",
                        array(':type' => $type, ':id' => $id));

        return $this->getModule[$id];
    }

    /**
     * Takes a module xml file and installs it into the database
     * @param string $path
     */
    public function installModule($name) {
        return Factory::getDatabase()->dbExecute('REPLACE INTO modules VALUES(null, :name, null, now())', array(':name' => $name));
    }

    /**
     * Removes the specific module from the database table
     * @param int $id
     * @return bool
     */
    public function uninstallModule($name) {
        return Factory::getDatabase()->dbExecute('DELETE FROM modules WHERE name = :name', array(':name' => $name));
    }

    /**
     * Returns an array of installed modules
     * @return array
     */
    public function getInstalledModules() {
        return Factory::getDatabase()->dbExecute('SELECT name FROM modules')->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * isInstalled
     * Returns true if requested module id is installed
     * @param int $id
     * @return bool
     */
    public function isInstalled($id) {
        return (bool) Factory::getDatabase()->dbSelect('SELECT 1 FROM modules WHERE id = :id', array(':id' => $id))->fetchColumn();
    }

    /**
     * Lists every module found in the /modules directory
     * @return array
     */
    public function listModuleFiles() {
        $modules = array();
        $folders = FileSystem::dirList(dir_MODULES);
        foreach ($folders as $folder) {
            $xmlpath = dir_MODULES . '/' . $folder . '/module.xml';

            if (file_exists($xmlpath)) {
                $modules[] = $xmlpath;
            }
        }

        return $modules;
    }

    /**
     * Returns an array of module information based on a list of filenames
     * @param array $files
     * @return array
     */
    public function parseModuleXML($path) {
        if (file_exists($path)) {
            if (is_readable($path)) {
                $data = simplexml_load_file($path);
                return $data;
            } else {
                throw new Exception("Could not read module file: {$path}");
            }
        } else {
            throw new Exception("Module file does not exists: {$path}");
        }
    }

}

?>