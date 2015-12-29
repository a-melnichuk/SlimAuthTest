<?php

class User
{
    private $db;
    
    function __construct($db) 
    {
        $this->db = $db;
    }
    
    function addUser($username,$password,$email)
    {
       if($this->userExists($username)) return false;
       
       $hashed_password = $this->getHash($password);
       return $this->insertUser($username, $hashed_password, $email);
    }
   
    
    function checkUser($username,$password)
    {
        $db_password_hash = $this->getUserHash($username)['password'];
        if($db_password_hash === false) return false;
        $valid_password = $this->isValidPassword($password, $db_password_hash);
        if($valid_password === false) return false; 
        $token = bin2hex(openssl_random_pseudo_bytes(16));
        $this->updateUserToken($username, $token);
        return $token;
    }
    
    
    function isValidToken($token)
    {
        $query = $this->db->prepare("SELECT username
                                    FROM user 
                                    WHERE token = :token
                                    AND TIMESTAMPDIFF(MINUTE,token_expire, CURRENT_TIMESTAMP) < 30 ");
        $query->bindParam(':token', $token,PDO::PARAM_STR);
        $query->execute();
        if($query->rowCount() > 0)
        {   //keep token alive
            $this->updateToken($token);
            return true;
        }
        else
        {
            return false;
        }
    }
   
    private function insertUser($username,$hashed_password,$email)
    {
        try{
            $query = $this->db->prepare("INSERT INTO user(username,password,email)
                                        VALUES (:username,:password,:email)");
            $query->bindParam(':username', $username,PDO::PARAM_STR);
            $query->bindParam(':password', $hashed_password,PDO::PARAM_STR);
            $query->bindParam(':email', $email,PDO::PARAM_STR);
            $query->execute();
            return true;
        } 
        catch(PDOException $e)
        {
            
            return $e;
        }
    }
    
    private function userExists($username)
    {
        $query = $this->db->prepare("SELECT username
                                    FROM user 
                                    WHERE username = :username");
        $query->bindParam(':username', $username,PDO::PARAM_STR);
        $query->execute();
        return $query->rowCount() > 0;
    }
    
    private function updateUserToken($username,$token)
    {
       // $expire = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $query = $this->db->prepare("UPDATE user
                                    SET token =  '$token', token_expire = NOW() + INTERVAL 12 HOUR
                                    WHERE username = :username ");
        $query->bindParam(':username', $username);
        $query->execute();
    }
    
    private function updateToken($token)
    {
        $expire = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $query = $this->db->prepare("UPDATE user
                                    SET token_expire = '$expire'
                                    WHERE token = :token ");
        $query->bindParam(':token', $token);
        $query->execute();
    }
    
    private function getHash($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    private function getUserHash($username)
    {
        $query = $this->db->prepare("SELECT password
                                    FROM user 
                                    WHERE username = :username");
        $query->bindParam(':username', $username,PDO::PARAM_STR);
        $query->execute();
        if($query->rowCount() === 0) return false;
        return $query->fetch(PDO::FETCH_ASSOC);  
    }


    private function isValidPassword($password,$db_password_hash)
    {
        return password_verify( $password, $db_password_hash );
    }
    
}