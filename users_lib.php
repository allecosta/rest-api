<?php 

class Users 
{
    private $pdo = null;
    private $stmt = null;
    public $error = "";

    function __construct()
    {
        try {
            $this->pdo = new PDO(
                "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET, DB_USER, DB_PASSWORD, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (Exception $ex) {
            exit($ex->getMessage());
        }
    }

    function __destruct()
    {
        if ($this->stmt !== null) {
            $this->stmt = null;
        }

        if ($this->pdo !== null) {
            $this->pdo = null;
        }
    }

    function query($sql, $data) 
    {
        try {
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute($data);

            return true;
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();

            return false;
        }
    }

    function saveUser($email, $pass, $id = null) 
    {
        if ($id === null) {
            $sql = "INSERT INTO users ('email', 'pass') VALUES (?,?)";
            $data = [$email, password_hash($pass, PASSWORD_BCRYPT)];
        } else {
            $sql = "UPDATE users SET 'email'= ?, 'pass'= ? WHERE 'id'= ?";

        }

        return $this->query($sql, $data);
    }

    function deleteUser($id) 
    {
        return $this->query("DELETE FROM 'users' WHERE 'id'= ?", [$id]);
    }

    function getUser($id) 
    {
        $this->query("SELECT * FROM 'users' WHERE '".(is_numeric($id)?"id":"email")."'=?", [$id]);
        
        return $this->stmt->fetch();
    }

    function verifyUser($email, $pass) 
    {
        $user = $this->getUser($email);

        if (!is_array($user)) {
            return false;
        }

        if (password_verify($pass, $user['pass'])) {
            $_SESSION['user'] = [
                "id" => $user['id'],
                "email" => $user['email']
            ];

            return true;
        } else {
            return false;
        }
    }
}

require 'config.php';

session_start();

$usr = new Users();
