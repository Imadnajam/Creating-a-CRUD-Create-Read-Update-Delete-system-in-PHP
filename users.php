<?php
/*

class user
{
    public $UserName;
    public $Email;
    public $Password;
    public function __construct($Email, $Password)
    {
        $this->Email = $Email;
        $this->Password = $Password;
    }
    public function Verif_Email()
    {
        include_once('Connexion.php');
        $sql0 = "SELECT `email` FROM `users` WHERE `email` =:Email";
        $stm0 = $pdo->prepare($sql0);
        $stm0->execute([
            "Email" => $this->Email
        ]); 
        $row = $stm0->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return false;
        } else {   
            return true;
        }
    }
    
    public function Inscription($UserName)
    {
        
        $this->UserName = $UserName;

        include_once('Connexion.php');
        
        if ($this->Verif_Email()) {
            $d = date('Y-m-d');
            $sql = "INSERT INTO `users`(`username`, `email`, `password`, `create_date`, `status`) VALUES (:username, :email, :password, :d, 1)";

            $stm = $pdo->prepare($sql);
            $stm->execute([
                "username" => $this->UserName,
                "email" => $this->Email,
                "password" => $this->Password,
                "d" => $d
            ]);
        }else{
            header('Location: inscription.html');
        }
    }
    public function Authentification()
    {
        include_once('Connexion.php');

        $cod = "SELECT * FROM users WHERE email = :Email";
        $stmt = $pdo->prepare($cod);
        $stmt->execute([
            'Email' => $this->Email
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($_GET['Password'] == $row['password']) {
            header('Location: HomeP.html?user='.$row['username']);
        } else {
            header('Location: inscription.html');
        }
    }
}
if (!empty($_GET['Username']) && !empty($_GET['Email']) and isset($_GET['Password'])) {
    $Create = new user($_GET['Email'], $_GET['Password']);
    $Create->Inscription($_GET['Username']);
}
if (empty($_GET['Username']) && isset($_GET['Email']) && isset($_GET['Password'])) {
    $Create = new user($_GET['Email'], $_GET['Password']);
    $Create->Authentification();
}
*/



class User
{
    private $Email;
    private $Password;
    public $UserName;
    public function __construct($Email, $Password)
    {
        $this->Email = $Email;
        $this->Password = $Password;
    }

    public function Verif_Email($pdo)
    {
        $sql0 = "SELECT `email` FROM `users` WHERE `email` = :Email";
        $stm0 = $pdo->prepare($sql0);
        $stm0->execute([
            "Email" => $this->Email
        ]);
        $row = $stm0->fetch(PDO::FETCH_ASSOC);
        return $row ? false : true;
    }

    public function Inscription($pdo, $UserName)
    {
        $this->UserName = $UserName;

        if ($this->Verif_Email($pdo)) {
            $d = date('Y-m-d');
            $sql = "INSERT INTO `users`(`username`, `email`, `password`, `create_date`, `status`) VALUES (:username, :email, :password, :d, 1)";
            $stm = $pdo->prepare($sql);
            $stm->execute([
                "username" => $this->UserName,
                "email" => $this->Email,
                "password" => $this->Password,
                "d" => $d
            ]);
            return true;
        } else {
            return false;
        }
    }

    public function Authentification($pdo, $inputPassword)
    {
        $cod = "SELECT * FROM users WHERE email = :Email";
        $stmt = $pdo->prepare($cod);
        $stmt->execute([
            'Email' => $this->Email
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($inputPassword, $row['password'])) {
            return $row['username'];
        } else {
            return false;
        }
    }
}

if (!empty($_GET['Username']) && !empty($_GET['Email']) && isset($_GET['Password'])) {
    include_once('Connexion.php'); 
    $Create = new User($_GET['Email'], password_hash($_GET['Password'], PASSWORD_DEFAULT));
    if ($Create->Inscription($pdo, $_GET['Username'])) {
        header('Location: HomeP.html?user=' . $_GET['Username']);
    } else {
        header('Location: inscription.html');
    }
}

if (empty($_GET['Username']) && isset($_GET['Email']) && isset($_GET['Password'])) {
    include_once('Connexion.php'); 
    $Create = new User($_GET['Email'], $_GET['Password']);
    $authenticatedUser = $Create->Authentification($pdo, $_GET['Password']);
    if ($authenticatedUser) {
        header('Location: HomeP.html?user=' . $authenticatedUser);
    } else {
        header('Location: inscription.html');
    }
}
?>