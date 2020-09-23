<?php

class DB_Functions
{

    private $conn;

    function __construct()
    {
        require_once 'db_connect.php';
        $db = new DB_connect();
        $this->conn = $db->connect();
    }

    function __destruct()
    {
    }

    public function UserExists($email)
    {

        $q = "SELECT `email` FROM `user` WHERE `email`= ?;";
        $stmt =  $this->conn->prepare($q);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $stmt->close();
            return true;
        }
        return false;
    }

    public function GetUser($email)
    {
        $q = "SELECT `email`, `name` FROM `user` WHERE `email`= ?;";
        $stmt =  $this->conn->prepare($q);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    public function StoreUser($data, $hash)
    {

        $email = $data["email"];
        $password = $hash[1];
        $salt = $hash[0];
        $name = $data["full-name"];
        $company_name = $data["company-name"];
        $phone_number = $data["phone-number"];
        $city = $data["city"];
        $postcode = $data["post-code"];
        $street = $data["street-address"];

        $q = "INSERT INTO `user` (`email`, `password`, `salt`, `name`, `company_name`, `phone_number`, `city`, `postcode`, `street`) ";
        $q .= "VALUES (?,?,?,?,?,?,?,?,?);";
        $stmt =  $this->conn->prepare($q);
        $stmt->bind_param("sssssssss", $email, $password, $salt, $name, $company_name, $phone_number, $city, $postcode, $street);
        $stmt->execute();
        $id = $this->conn->insert_id;

        $q = "SELECT * FROM `user` WHERE `id` = ?";
        $stmt =  $this->conn->prepare($q);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $response["id"] = $user["id"];
        $response["email"] = $user["email"];
        $response["name"] = $user["name"];
        $response["created_at"] = $user["created_at"];
        return $response;
    }

    public function StoreUserPassword($email, $hash)
    {
        $q = "UPDATE `user` SET `password` = ?, `salt` = ? WHERE `email` = ?;";
        $stmt =  $this->conn->prepare($q);
        $stmt->bind_param("sss", $hash[1], $hash[0], $email);
        $success = $stmt->execute();

        if ($success) {
            return true;
        }

        return false;
    }

    public function CheckPassword($data)
    {
        $q = "SELECT `salt`, `password`, `login_attempts` FROM `user` WHERE `email` = ?;";
        $stmt =  $this->conn->prepare($q);
        $stmt->bind_param("s", $data["email"]);
        $stmt->execute();
        $result = $stmt->get_result();
        $result = $result->fetch_assoc();

        if ($result["login_attempts"] > 3) {
            sleep($result["login_attempts"]);
        }

        $salt = base64_decode($result["salt"]);
        $hashDB = $result["password"];
        $iterations = 10000;
        $hash = hash_pbkdf2("sha256", $data["password"], $salt, $iterations);
        $hash = base64_encode(pack('H*', $hash));

        if ($hash == $hashDB) {
            $q = "SELECT `email`, `name`, `company_name`, `phone_number`, `street`, `city`, `postcode` FROM `user` WHERE `email` = ?;";
            $stmt =  $this->conn->prepare($q);
            $stmt->bind_param("s", $data["email"]);
            $stmt->execute();
            $result = $stmt->get_result();
            $result = $result->fetch_assoc();

            $response["email"] = $result["email"];
            $response["name"] = $result["name"];
            $response["company-name"] = $result["company_name"];
            $response["phone-number"] = $result["phone_number"];
            $response["street-address"] = $result["street"];
            $response["city"] = $result["city"];
            $response["post-code"] = $result["postcode"];
            $response = json_encode($response);

            $q = "UPDATE `user` SET `login_attempts` = 0 WHERE `email` = ?";
            $stmt =  $this->conn->prepare($q);
            $stmt->bind_param("s", $data["email"]);
            $stmt->execute();

            return $response;
        } else {
            $q = "UPDATE `user` SET `login_attempts` = login_attempts+1 WHERE `email` = ?;";
            $stmt =  $this->conn->prepare($q);
            $stmt->bind_param("s", $data["email"]);
            $stmt->execute();

            $q = "SELECT `login_attempts` FROM `user` WHERE `email` = ?;";
            $stmt =  $this->conn->prepare($q);
            $stmt->bind_param("s", $data["email"]);
            $stmt->execute();
            $result = $stmt->get_result();
            $result = $result->fetch_assoc();

            $response["login_attempts"] = $result["login_attempts"];
            return $response;
        }
    }

    public function StoreToken($email, $token, $expirationDate)
    {
        $q = "INSERT INTO `password_reset_token` (`email`, `key`, `expiration`) VALUES (?,?,?);";
        $stmt =  $this->conn->prepare($q);
        $stmt->bind_param("sss", $email, $token, $expirationDate);
        $stmt->execute();
    }

    public function DeleteToken($token)
    {
        $q = "DELETE FROM `password_reset_token` WHERE `key` = ?;";
        $stmt =  $this->conn->prepare($q);
        $stmt->bind_param("s", $token);
        $success = $stmt->execute();

        if ($success) {
            return true;
        }

        return false;
    }

    public function TokenExists($token)
    {
        $q = "SELECT `key` FROM `password_reset_token` WHERE `key`= ?;";
        $stmt =  $this->conn->prepare($q);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            $stmt->close();
            return false;
        }

        return true;
    }

    public function TokenValid($token)
    {
        $currentTime = date("Y-m-d H:i:s");
        $q = "SELECT `key`, `expiration`, `email` FROM `password_reset_token` WHERE `key` = ?;";
        $stmt =  $this->conn->prepare($q);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        if ($result->num_rows == 0) {
            $stmt->close();
            return false;
        }

        $expirationTime = $data["expiration"];
        if ($currentTime > $expirationTime) {
            $stmt->close();
            return false;
        }

        return $data;
    }

    public function GetInvoiceTotal($products)
    {
        $total = 0;
        for ($i = 0; $i < count($products); $i++) {
            $q = "SELECT `name`, `price` FROM `product` WHERE `id` = ?;";
            $stmt =  $this->conn->prepare($q);
            $stmt->bind_param("i", $products[$i]["id"]);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();

            if ($result->num_rows == 0) {
                $stmt->close();
                return false;
            }
            $total += $data["price"] * $products[$i]["quantity"];
        }

        return $total;
    }

    public function GetProduct($id)
    {
        $q = "SELECT `name`, `price` FROM `product` WHERE `id` = ?;";
        $stmt =  $this->conn->prepare($q);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        if ($result->num_rows == 0) {
            $stmt->close();
            return false;
        }

        return $product;
    }
}
