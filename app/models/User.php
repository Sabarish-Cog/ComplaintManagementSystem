<?php
class User {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getUserByEmail($email) {
        $this->db->query("SELECT * FROM users WHERE email = :email");
        $this->db->bind(':email', $email);
        
        $row = $this->db->single();
        
        if ($this->db->rowCount() > 0) {
            return true;
        }
        return false;
    }
    
    public function addUser($data){
        $this->db->query("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);
        
        if ($this->db->execute()) {
            return true;
        }
        return false;
    }

    public function login($email, $password) {
        $this->db->query("SELECT * FROM users WHERE email = :email");
        $this->db->bind(':email', $email);
        
        $row = $this->db->single();
        
        if (isset($row->password) && password_verify($password, $row->password)) {
            return $row;
        }
        return false;
    }
}