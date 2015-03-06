<?php
/**
 * Clase que controla la conexión con la base de datos
 */
class Connection
{
    /**
     * @var null|Pdo
     */
    private $conexion = false;
    private $host = "localhost";
    private $username = "cni";
    private $password = "inc";
    private $dbname = "centro";
    private $port = '3306';
    /**
     * Constructor de conexion a la base de datos
     */
    public function __construct()
    {
        try {
            $this->conexion = new Zend_Db_Adapter_Pdo_Mysql(
                array(
                    'host' => $this->host,
                    'username' => $this->username,
                    'password' => $this->password,
                    'dbname' => $this->dbname,
                    'port' => $this->port,
                    'driver_options' => array(MYSQLI_INIT_COMMAND => 'SET NAMES UTF8;')
                )
            );
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
            $this->conexion = false;
        }
    }

    /**
     * Pasamos la consulta y los parametros y la ejecuta
     * @param  string $sql    Consulta sql
     * @param  array $params  parametros de la consulta
     * @return array          resultado
     */
    public function consulta($sql, $params = null)
    {
        $result = false;
        if ($this->conexion) {
            $result = $this->conexion->fetchAll($sql, $params);
        }
        return $result;
    }
}
