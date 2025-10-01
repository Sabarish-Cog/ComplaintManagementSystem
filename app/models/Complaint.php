<?php

class Complaint
{
    private $db;
    public function __construct()
    {
        $this->db = new Database();
    }
    
    function getAllComplaints()
    {
        $this->db->query("SELECT *, complaints.id AS complaint_id, users.id AS user_id, complaints.created_on AS complaint_created_on
                             FROM complaints
                             INNER JOIN users
                             ON users.id = complaints.user_id
                             ORDER BY complaints.created_on DESC");
        $complaints = $this->db->resultSet();

        return $complaints;
    }
    
    function getComplaint($id)
    {
        $this->db->query("SELECT * 
                             FROM complaints
                             WHERE id = :id");
        $this->db->bind(':id', $id);
        $complaint = $this->db->single();
        return $complaint;
    }

    public function addComplaint($data)
    {
        $this->db->query("INSERT INTO complaints (user_id, title, description) VALUES (:user_id, :title, :description)");
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description']);

        if ($this->db->execute()) {
            return true;
        }
        return false;
    }

    public function editComplaint($data)
    {
        $this->db->query("UPDATE complaints SET title=:title, description=:description WHERE id=:id");
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':id', $data['id']);

        if ($this->db->execute()) {
            return true;
        }
        return false;
    }

    public function deleteComplaint($id)
    {
        $this->db->query("DELETE FROM complaints WHERE id = :id");
        $this->db->bind(':id', $id);
        if ($this->db->execute()) {
            return true;
        }
        return false;
    }
}
