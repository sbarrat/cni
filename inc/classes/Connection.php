<?php
/**
 * Clase que controla la conexión con la base de datos
 */
class Connection
{
    /**
     * @var null|Pdo
     */
    private $conexion = null;
    private $host = "localhost";
    private $username = "cni";
    private $password = "inc";
    private $dbname = "centro";

    /**
     * Constructor de conexion a la base de datos
     */
    public function __construct()
    {
        $dsn = 'mysql:dbname='.$this->dbname.';host='.$this->host;
        $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'");
        try {
            $this->conexion = new PDO(
                $dsn,
                $this->username,
                $this->password,
                $options
            );
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
            xdebug_var_dump($e->getTraceAsString());
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
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
