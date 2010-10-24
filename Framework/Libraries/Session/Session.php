<?php

namespace Modula\Framework\Sessions;

/**
 * @defgroup sessions
 * @ingroup application
 */
class Session extends Object {

    /**
     * Our session storage adaptor object
     *
     * @var array
     */
    private $options;
    private $isValid = true;

    function __construct($options) {
        $this->options = $options;
        /**
         * Get storage adaptor
         */
        $this->_storage = SessionStorage::getInstance($this->options['storage']);

        // Set the garbage collector lifetime
        ini_set('session.gc_maxlifetime', $this->options['cookie.expire']);

        // Tell PHP we'll use our own handlers
        ini_set('session.save_handler', 'user');

        // Disable fallback to trans sids
        ini_set('session.use_trans_sid', false);

        // Only use cookies for session handling
        ini_set('session.use_only_cookies', true);

        // Get cookie name from site configuration
        session_name($this->options['cookie.name']);

        /**
         * Start the session
         */
        $this->start();

        /**
         * Perform security checks
         */
        $this->isValid = $this->validateClient();

        /**
         * Removes the clients session cookie if validation returns false, note that we don't destroy the session
         * as this might belong to an innocent party in a session hijack attempt
         */
        if (!$this->isValid) {
            $this->destroyCookie();
        }

        /**
         * Update our counters
         */
        $this->updateCounters();

        /**
         * If $this->remember is set, like from the login script etc, set the
         * session cookie to expire according to the session cookie timeout
         * setting
         */
        if ($this->remember) {
            setcookie($this->options['cookie.name'], $_COOKIE[$this->options['cookie.name']], time() + $this->options['cookie.expire'], $this->options['cookie.path']);
        }

        /**
         * Regenerate session id to prevent fixiation
         * @todo Get this to work :)
         */
        //$this->regenerate();
    }

    /**
     * Objects are destroyed before session write and close are called, thus we
     * must make sure we call these functions in the destructor
     */
    function __destruct() {
        session_write_close();
    }

    /**
     * Override default setter
     *
     * @param string $index
     * @param mixed $value
     */
    function __set($index, $value) {
        if ($this->isValid) {
            $_SESSION[$index] = $value;
        }
    }

    /**
     * Override default getter
     *
     * @param string $index
     */
    function __get($index) {
        return $this->isValid ? $_SESSION[$index] : null;
    }

    /**
     * Starts a new session
     */
    function start() {
        /**
         * Destroy any sessions that may have been auto created
         */
        if (session_id ()) {
            session_unset();
            session_destroy();
        }

        /**
         * Start a new session
         */
        session_start();
    }

    /**
     * Completely destroy the session and create a new one
     *
     * @return bool
     */
    function restart() {
        // Completely destory the session
        $this->destroy();
        // Start a new session
        $this->start();
        return true;
    }

    /**
     * Regenerated a session id, keeping the old session data
     */
    function regenerate() {
        $oldsessid = $this->getSessionID();
        session_regenerate_id(true);
        Factory::getDatabase()->dbExecute('UPDATE sessions SET token = :newsessid WHERE token = :oldsessid', array(':newsessid' => $this->getSessionID(), ':oldsessid' => $oldsessid));
    }

    /**
     * Returns the current session id string
     * @return string
     */
    function getSessionID() {
        return session_id();
    }

    /**
     * Updates the session counter, last and current timestamps
     */
    function updateCounters() {
        // Increment session counter by 1 or populate if it doesn't already exist
        $this->session_visits = $this->session_visits ? ++$this->session_visits : 1;

        if ($this->session_now) {
            // If previous 'now' timestamp exists, set that as our last
            $this->session_last = $this->session_now;
        } else {
            /**
             * If previous 'now' timestamp doesn't exists, this means it's our
             * first visit to the site using this session id, so mark the start
             * time for later use.
             */
            $this->session_start = time();
        }
        // Set current timestamp
        $this->session_now = time();
    }

    /**
     * Performs various security validation checks to make sure that the
     * session used in the request is authentic
     */
    function validateClient() {
        if (is_null($this->remoteaddr) || is_null($this->useragent)) {
            $this->remoteaddr = Utils::getRemoteAddr();
            $this->useragent = Utils::getAgent();
        }
        return ($this->remoteaddr == Utils::getRemoteAddr() && $this->useragent == Utils::getAgent()) ? true : false;
    }

    /**
     * Returns true if the session has expired
     *
     * @return bool
     */
    function hasExpired() {
        return ($this->session_now - $this->session_last > $this->options['cookie.expire']) ? true : false;
    }

    /**
     * Completely destoys the session and removes it from the database,
     * this is generally used for things like logout etc
     */
    function destroy() {
        $this->destroyCookie();
        session_unset();
        session_destroy();
    }

    /**
     * Destory our clients cookie
     */
    function destroyCookie() {
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, '/');
        }
    }

    /**
     * Returns a list of users online based on the minimum timeout value past as
     * $timeout and limited to annonymous users if $annon is set to true
     *
     * @param int $timestamp
     * @param bool $annon
     * @return array
     */
    public function sessionList($timestamp = 0, $annon = true) {
        $result = Factory::getDatabase()->dbSelect("SELECT username FROM users WHERE id IN (SELECT DISTINCT userid FROM sessions WHERE timestamp > :timestamp " . ($annon ? ' AND userid = 0' : ' AND userid > 0') . ")", array(':timestamp' => $timestamp));
        $users = array();
        foreach ($result as $row) {
            $users[] = $row['username'];
        }
        return $users;
    }

    /**
     * Returns a count of sessions based on the on the minimum timeout
     * value passed as $timestamp and limits to annonymous users if $annon is
     * set to true
     *
     * @param int $timestamp
     * @param bool $annon
     * @return int
     */
    function sessionCount($timestamp = 0, $annon = true) {
        return (int) Factory::getDatabase()->dbSelect("SELECT count(*) FROM users WHERE id IN (SELECT" . (!$annon ? ' DISTINCT' : '') . " userid FROM sessions WHERE timestamp > :timestamp " . ($annon ? ' AND userid = 0' : ' AND userid > 0') . ")", array(':timestamp' => $timestamp))->fetchColumn();
    }

}

?>