<?php 

class User {

    private $id;
    public $login;
    public $email;
    public $firstname;
    public $lastname;
    
    public function __construct($id, $login, $email, $firstname, $lastname) {
        $this->id = $id;
        $this->login = $login;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
    }

    public function register($login, $password, $email, $firstname, $lastname) {
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=mydatabase', 'username', 'password');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt = $pdo->prepare("INSERT INTO users (login, password, email, firstname, lastname) VALUES (:login, :password, :email, :firstname, :lastname)");
            $stmt->bindParam(':login', $login);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':firstname', $firstname);
            $stmt->bindParam(':lastname', $lastname);
            
            $stmt->execute();
            echo "User registered successfully.";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return array($login, $password, $email, $firstname, $lastname);
    }
    public function connect($login, $password) {
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=classes', 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
        return true;
    }

    public function disconnect() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
    }

    public function delete() {
        if (empty($this->id)) {
            return false;
        }
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=classes', 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            $stmt->execute();

            // déconnecte l'utilisateur (session)
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }
            $_SESSION = [];
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params['path'], $params['domain'],
                    $params['secure'], $params['httponly']
                );
            }
            session_destroy();

            // nettoie l'objet
            $this->id = null;
            $this->login = null;
            $this->email = null;
            $this->firstname = null;
            $this->lastname = null;

            return true;
        } catch (PDOException $e) {
            // gérer/log l'erreur si nécessaire
            return false;
        }
    }

    public function update($login, $email, $firstname, $lastname) {
        if (empty($this->id)) {
            return false;
        }
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=classes', 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare("UPDATE users SET login = :login, email = :email, firstname = :firstname, lastname = :lastname WHERE id = :id");
            $stmt->bindParam(':login', $login);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':firstname', $firstname);
            $stmt->bindParam(':lastname', $lastname);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            $stmt->execute();

            $this->login = $login;
            $this->email = $email;
            $this->firstname = $firstname;
            $this->lastname = $lastname;

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function isConnected() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        // return true only if a user id is stored in session
        return isset($_SESSION['id']) && !empty($_SESSION['id']);
    }

    public function getAllInfos() {
        return array(
            'id' => $this->id,
            'login' => $this->login,
            'email' => $this->email,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname
        );
    }
    public function getLogin() {
        return $this->login;
    }
    public function getEmail() {
        return $this->email;
    }
    public function getFirstname() {
        return $this->firstname;
    }
    public function getLastname() {
        return $this->lastname;
    }
}

?>