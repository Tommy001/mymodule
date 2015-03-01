<?php
namespace Tommy001\Mymodule;
 
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
    public $pdo;     
/**
 * @codeCoverageIgnore
 */    
    public function __construct(){
        
        //for test purpose, replace with wanted db
        $this->pdo = new \PDO("sqlite:test_upload.sqlite");
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING); // Display errors

    }

/**
 * @codeCoverageIgnore
 */
    public function add($upload) {
        $stmt = $this->pdo->prepare("INSERT INTO test_upload (san_filename, path) VALUES(?, ?)");
        $stmt->execute($upload);
    }
/**
 * @codeCoverageIgnore
 */
    public function findLast()
    {
      $id = $this->pdo->lastInsertId();
      $stmt = $this->pdo->prepare('SELECT * FROM test_upload WHERE rowid = ?;');
      $stmt->execute(array($id));
      $lastimg = $stmt->fetchAll(\PDO::FETCH_ASSOC); 
      return $lastimg;
    }  
}


