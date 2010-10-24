<?php

namespace Modula\Framework;

class ModSelector {
    const SINGLE = 0;
    const ALL = 1;
    const REGEX = 2;
    const TOP = 3;
    const RANDOM = 4;

    /**
     * Returns an array of module objects based on the given criteria
     *
     * @param int $loadtype
     * @param string $identifier
     */
    public static function select($loadtype, $identifier) {
        switch ($identifier) {
            // Selects every module for the given module group identifier.
            case self::ALL:
                return Factory::getDatabase()->dbSelect('SELECT id FROM modules WHERE type = :identifier', array(':identifier' => $identifier))->fetch(PDO::FETCH_ASSOC);
                break;
            // Selects modules based on the given regular expression pattern.
            case self::REGEX:
                return Factory::getDatabase()->dbSelect('SELECT id FROM modules WHERE name REGEX :identifier', array(':identifier' => $identifier))->fetch(PDO::FETCH_ASSOC);
                break;
            // Selects the highest ranking module based on the given module
            // group identifier.
            case self::TOP:
                return Factory::getDatabase()->dbSelect('SELECT id FROM modules WHERE type = :identifier AND order = (SELECT MAX(order) FROM modules WHERE type = :identifier', array(':identifier' => $identifier))->fetch(PDO::FETCH_ASSOC);
                break;
            // Randomly selects a module based on the given module group
            // identifier.
            case self::RANDOM:
                break;
        }
    }

}

?>
