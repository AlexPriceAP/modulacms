<?php

namespace Modula\Framework;

class PdoDatabaseAdaptor extends DatabaseAdaptor {

    private $connection;
    private $stime;
    private $etime;
    public $timespent;
    public $numqueries;
    public $queries = array();

    function __constuct() {

        // Do nothing
    }

    /**
     * Creates the initial mysql connection object based on the defaults
     * in the constants definition file, more work is needed here
     *
     * @param string $db_server
     * @param string $db_user
     * @param string $db_pass
     * @return PDO Connection Object
     */
    function __construct($params) {
        if (is_array($params) && array_key_exists('server', $params) && array_key_exists('user', $params) && array_key_exists('pass', $params)) {
            try {
                $this->connection = new PDO($params['server'], $params['user'], $params['pass']);
                // $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connection->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
                //$this->connection->setAttribute(PDO::ATTR_PERSISTENT, true);
                $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
                //$this->connection->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8");
            } catch (PDOException $e) {
                $this->connection = null;
                throw new \Exception('DB Connection Error: ' . $e->getMessage());
            }
            return $this->connection;
        } else {
            throw new CustomException("Invalid paramters passed to PDO database adaptor");
        }
    }

    /**
     * Takes an SQL statement and executes it on the server returning the
     * result as a PDO result set
     *
     * @param string $sql
     * @param array $params
     * @return PDOResultSet
     */
    private function dbQuery($sql, $params = array()) {
        $this->queries[] = $sql;
        $this->numqueries++;
        $this->sTime = microtime(true);

        try {
            $this->connection->beginTransaction();
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            $this->connection->commit();
        } catch (PDOException $e) {
            $this->connection->rollBack();
            throw new CustomException('DB Query Error: ' . $e->getMessage());
        }

        $this->eTime = microtime(true);
        $this->timespent += round($this->eTime - $this->sTime, 4);

        return $stmt;
    }

    /**
     * Takes an SQL query with an array of query parameters and returns a
     * PDO result set
     *
     * @param string $sql
     * @param array $params
     * @return PDOResultSet
     */
    public function dbSelect($sql, $params = array()) {
        return $this->dbQuery($sql, $params);
    }

    public function dbCount($table) {
        return $this->dbSelect("SELECT count(1) FROM {$table}")->fetchColumn();
    }

    /**
     * Takes an SQL query and array of parameters and returns an none
     * specific variable type
     *
     * @param string $sql
     * @param array $params
     * @return mixed
     */
    public function dbValue($sql, $params = array()) {
        return $this->dbQuery($sql, $params)->fetchColumn();
    }

    /**
     * Takes an SQL statement and executes it on the servers, returning
     * the result as a bool value
     *
     * @param string $sql
     * @param array $params
     * @return bool
     */
    public function dbExecute($sql, $params = array()) {
        $stmt = $this->dbQuery($sql, $params);
        return $stmt;
    }

}

?>