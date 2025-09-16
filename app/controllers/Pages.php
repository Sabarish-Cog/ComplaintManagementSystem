<?php 
    class Pages extends Controller {
        public function __construct() {
            // $this->complaintModel = $this->model('Complaint');
        }
        public function index() {
            $this->view("pages/index", [
                "title" => "Welcome to Complaint It!",
                "data" => "Here you can raise complaints about topics or products!",
            ]);
        }
        public function contact() {
            $this->view("pages/contact");
        }
    }
    