<?php

use modula\framework\acl;

class Acp
{

    public function createAcp($acoid, $name, $description){
        return Factory::getDatabase()->dbExecute('INSERT INTO acl_acp VALUES (null, :name, :description)', array(':name' => $name, ':description' => $description));
    }


}

?>
