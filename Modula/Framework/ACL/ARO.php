<?php

use modula\framework\acl;

class Aro
{

    /**
     * Inserts a new ACL records.
     *
     * @param bool $parentid
     */
    public function insertNode($parentid)
    {
        $parentnode = $this->getNode($parentid);
        if ($this->shuntNodes($parentnode['rightid'])) {
            Factory::getDatabase()->dbSelect('INSERT INTO acl VALUES (:parentid, :parentright, :parentright + 1)', array(':parentid' => $parent['id'], ':parentright' => $parent['rightid']));
        }
    }

    /**
     * Returns an ACL record based on the given parent id.
     *
     * @param int $parentid
     * @return array
     */
    private function getNode($parentid)
    {
        return Factory::getDatabase()->dbSelect('SELECT id, leftid, rightid FROM acl WHERE id = :id', array(':id' => $parentid))->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Shifts all records equal or greator to the given id to the right to make
     * room for a new record.
     *
     * @param bool $parentright
     */
    private function shiftNodes($parentright)
    {
        Factory::getDatabase()->dbSelect('UPDATE acl SET right = right + 2 WHERE rightid >= :parentright', array(':parentright' => $parentright))->fetch(PDO::FETCH_ASSOC);
        Factory::getDatabase()->dbSelect('UPDATE acl SET left = left + 2 WHERE leftid > :parentright', array(':parentright' => $parentright))->fetch(PDO::FETCH_ASSOC);
    }

}

?>
