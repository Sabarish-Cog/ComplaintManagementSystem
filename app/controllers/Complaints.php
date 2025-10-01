<?php

class Complaints extends Controller
{

    private $complaintModel;


    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect("users/login");
        } else {
            $this->complaintModel = $this->model("Complaint");;
        }
    }
    public function index()
    {
        $data = ['You are in Complaints page', 'complaints' => $this->complaintModel->getAllComplaints()];
        
        $this->view("complaints/index", $data);
    }
    public function delete($id)
    { 
        $complaint = $this->complaintModel->getComplaint($id);
        if ($complaint->user_id !== $_SESSION['user_id']) {
            redirect('complaints');
            die("Not Deleted");
        }
        if (!$this->complaintModel->deleteComplaint($id)) {
            die("Error in DB");
        }
        flash("complaint_success", "Complaint Deleted Successfully");
        redirect("complaints/index");
    }
    
    public function add()
    { 
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            $data = [
                'user_id' => $_SESSION['user_id'],
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'err_title' => '',
            ];

            if (empty($data['title'])) {
                $data['err_title'] = 'Please Enter a Complaint Title!';
            }

            if (empty($data['err_title'])) {
                if (!$this->complaintModel->addComplaint($data)) {
                    die("Something went wrong!");
                }
                flash("complaint_success", "Successfully Raised the Complaint!");
                redirect("complaints");
            } else {
                $this->view("complaints/add", $data);
            }

        }else {
            $data = [
                'title' => '',
                'description' => '',
            ];

            $this->view("complaints/add", $data);
        }
    }
    public function edit($id)
    { 
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            $data = [
                'id' => $id,
                'user_id' => $_SESSION['user_id'],
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'err_title' => '',
            ];

            if (empty($data['title'])) {
                $data['err_title'] = 'Please Enter a Complaint Title!';
            }

            if (empty($data['err_title'])) {
                if (!$this->complaintModel->editComplaint($data)) {
                    die("Something went wrong!");
                }
                flash("complaint_success", "Successfully Modified the Complaint!");
                redirect("complaints");
            } else {
                $this->view("complaints/edit", $data);
            }

        }else {
            $complaint = $this->complaintModel->getComplaint($id);
            if ($complaint === null || $complaint->user_id !== $_SESSION['user_id']) {
                redirect('complaints');
                die("No Complaint or permission");
            }
            $data = [
                'id' => $id,
                'title' => $complaint->title,
                'description' => $complaint->description,
            ];

            $this->view("complaints/edit", $data);
        }
    }
}
