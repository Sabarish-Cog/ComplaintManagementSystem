<?php 
    class Maps extends Controller {
        public function fun($id) {
            $this->view($id);
            echo "Hello, Maps!!" . $id;
        }
    }
    