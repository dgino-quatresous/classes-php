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
        
        $conn = new mysqli('localhost', 'root', '', 'classes');
        if ($conn->connect_error) {
            echo "Error: " . $conn->connect_error;
            return false;
        }


        $login_e = $conn->real_escape_string($login);
        $password_e = $conn->real_escape_string($password);
        $email_e = $conn->real_escape_string($email);
        $firstname_e = $conn->real_escape_string($firstname);
        $lastname_e = $conn->real_escape_string($lastname);

        $sql = "INSERT INTO users (login, password, email, firstname, lastname) VALUES ('$login_e', '$password_e', '$email_e', '$firstname_e', '$lastname_e')";

        if ($conn->query($sql) === TRUE) {
            echo "User registered successfully.";
        } else {
            echo "Error: " . $conn->error;
        }

        $conn->close();
        return array($login, $password, $email, $firstname, $lastname);
    }
    public function connect($login, $password) {

        $conn = new mysqli('localhost', 'root', '', 'classes');
        if ($conn->connect_error) {
            echo "Error: " . $conn->connect_error;
            return false;
        }
        $conn->close();
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
            $conn = new mysqli('localhost', 'root', '', 'classes');
            if ($conn->connect_error) {

                return false;
            }

            $id = (int) $this->id;
            $sql = "DELETE FROM users WHERE id = $id";
            if (!$conn->query($sql)) {
                $conn->close();
                return false;
            }
            $conn->close();


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


            $this->id = null;
            $this->login = null;
            $this->email = null;
            $this->firstname = null;
            $this->lastname = null;

            return true;
        } catch (Exception $e) {

            return false;
        }
    }

    public function update($login, $email, $firstname, $lastname) {
        if (empty($this->id)) {
            return false;
        }
        try {
            $conn = new mysqli('localhost', 'root', '', 'classes');
            if ($conn->connect_error) {
                return false;
            }

            $login_e = $conn->real_escape_string($login);
            $email_e = $conn->real_escape_string($email);
            $firstname_e = $conn->real_escape_string($firstname);
            $lastname_e = $conn->real_escape_string($lastname);
            $id = (int) $this->id;

            $sql = "UPDATE users SET login = '$login_e', email = '$email_e', firstname = '$firstname_e', lastname = '$lastname_e' WHERE id = $id";
            if (!$conn->query($sql)) {
                $conn->close();
                return false;
            }

            $conn->close();

            $this->login = $login;
            $this->email = $email;
            $this->firstname = $firstname;
            $this->lastname = $lastname;

            return true;
        } catch (Exception $e) {
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