<?php
namespace Anax\Mymodule;
 
/**
 * Model for Upload.
 *
 */
class Upload implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;

    /**
     * Save to database.
     *
     */
    private $pdo;     
    
    public function __construct(){
        
        $dsn      = 'mysql:host=localhost;dbname=toja14;';
        $login    = 'root';
        $password = '';
        $options  = array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'");
        $this->pdo = new \PDO($dsn, $login, $password, $options);

    }


    public function add($upload) {
        $stmt = $this->pdo->prepare("INSERT INTO test_upload (san_filename, path) VALUES(?, ?)");
        $stmt->execute($upload);
    }

    public function findLast()
    {
      $id = $this->pdo->lastInsertId();
      $stmt = $this->pdo->prepare('SELECT * FROM test_upload WHERE id = ?;');
      $stmt->execute(array($id));
      $lastimg = $stmt->fetchAll(\PDO::FETCH_ASSOC); 
      return $lastimg;
    }  
}


