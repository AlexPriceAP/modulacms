<?php

/**
 * @ingroup application
 */
class User extends Object {

    public $groups = array();

    function __construct($username) {
        $db = Factory::getDatabase();

        if (!is_null($username)) {
            $userprefs = $db->dbSelect("SELECT * FROM users WHERE username = :username", array(':username' => $username))->fetch(PDO::FETCH_ASSOC);
            if ($userprefs) {
                foreach ($userprefs as $key => $value) {
                    $this->$key = $value;
                }
                $db->dbExecute("UPDATE users SET timestamp = :timestamp WHERE id = :userid", array(':timestamp' => time(), ':userid' => $this->id));
                $groups = Factory::getDatabase()->dbSelect('SELECT lower(g.name) as name FROM groups g INNER JOIN user_groups ug ON ug.groupid = g.id WHERE ug.userid = :userid', array(':userid' => $this->id))->fetchAll(PDO::FETCH_ASSOC);
                foreach ($groups as $group) {
                    $this->groups[] = $group['name'];
                }
            } else {
                throw new \Exception('Failed to load user preferences');
            }
        } else {
            $this->id = 0;
            $this->username = 'Guest';
        }
    }

    function inGroup($group) {
        return in_array(strtolower($group), $this->groups);
    }

    function userExists($user) {
        return Factory::getDatabase()->dbSelect("SELECT 1 FROM users WHERE username = :user", array(':user' => $user))->fetchColumn();
    }

    function checkPassword($user, $pass) {
        $password = Factory::getDatabase()->dbSelect("SELECT password FROM users WHERE username = :user LIMIT 1", array(':user' => $user))->fetchColumn();
        return (substr($password, 0, 12) . hash('sha512', substr($password, 0, 12) . $pass) == $password) ? true : false;
    }

    function getImageUrl($username = null) {
        if (!is_null($username)) {
            $user = new User($username);
            if (!is_null($user->image) && $user->image <> '') {
                return url_ROOT . $user->image;
            } else {
                return url_ROOT . 'images/user.png';
            }
        } else {
            if (!is_null($this->image) && $this->image <> '') {
                return url_ROOT . $this->image;
            } else {
                return url_ROOT . 'images/user.png';
            }
        }
    }

}

?>