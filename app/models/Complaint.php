<?php

    class Complaint {
        private $db;
        public function __construct() {
            $this->db = new Database();
        }
    }