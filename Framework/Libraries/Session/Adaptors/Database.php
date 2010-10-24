<?php

namespace Modula\Framework\Sessions\Storage;

/**
 * @ingroup sessionstorage
 */
class DatabaseSessionStorage extends SessionStorage {

    function read($id) {
        return (string) Factory::getDatabase()->dbSelect('SELECT data FROM sessions WHERE token = :token', array(':token' => $id))->fetchColumn();
    }

    function write($id, $session_data) {
        Factory::getDatabase()->dbExecute('REPLACE INTO sessions VALUES(:id, :userid, :data, :timestamp)', array(':id' => $id, ':userid' => Registry::getInstance()->user->id, ':data' => $session_data, ':timestamp' => time()));
        return true;
    }

    function destroy($id) {
        Factory::getDatabase()->dbExecute('DELETE FROM sessions WHERE token = :token', array(':token' => $id));
        return true;
    }

    function gc($maxlifetime) {
        Factory::getDatabase()->dbExecute('DELETE FROM sessions WHERE timestamp < :timeout', array(':timeout' => time() - $maxlifetime));
        return true;
    }

}

?>