<?php
class Users extends Controller
{
    private $userModel;

    public function __construct() {
        if (isset($_SESSION['user_id'])) {
            redirect("");
        }
        $this->userModel = $this->model('User');
    }

    public function index()
    {
        $this->view('pages/index', [
            "title" => "Users",
            "data" => "Index Page"
        ]);
        echo "Hello, World - USERS INDEX";
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Santize Post Data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            // Load Data
            $data = [
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'err_name' => '',
                'err_email' => '',
                'err_password' => '',
                'err_confirm_password' => '',
            ];

            // Validation of each Input
            if (empty($data['name'])) {
                $data['err_name'] = "Please Enter Your Name";
            }

            if (empty($data['email'])) {
                $data['err_email'] = "Please Enter Your E-Mail";
            } elseif ($this->userModel->getUserByEmail($data['email'])) {
                $data['err_email'] = "E-Mail already exists! Kindly Login";
            }
            
            if (empty($data['password'])) {
                $data['err_password'] = "Please Enter A New Password";
            } elseif (strlen($data['password']) < 8) {
                $data['err_password'] = "Password Must be of 8 characters";
            }
            
            if (empty($data['confirm_password'])) {
                $data['err_confirm_password'] = "Please Enter Confirm Password";
            } elseif ($data['confirm_password'] != $data['password']) {
                $data['err_confirm_password'] = "Password and Confrim Passwords are Not Matching";
            }
            
            if (empty($data['err_name']) && empty($data['err_email']) && empty($data['err_password']) && empty($data['err_confirm_password'])) {
                
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                unset($data["confirm_password"]);
                if (!$this->userModel->addUser($data)) {
                    die("Something went wrong!");
                }
                // unset($data["password"]);
                // $this->view("users/login");
                flash("register_success", "Successfully Registered! \n Proceed to Login!!");
                redirect("users/login");
            } else {
                $this->view("users/register", $data);
            }
        } else {
            $data = [
                'name' => '',
                'email' => '',
                'password' => '',
                'confirm_password' => '',
                'err_name' => '',
                'err_email' => '',
                'err_password' => '',
                'err_confirm_password' => '',
            ];
            
            $this->view('users/register', $data);
        }
    }
    
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Santize Post Data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            
            // Load Data
            $data = [
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'err_email' => '',
                'err_password' => '',
            ];
            
            // Validation of each Input
            if (empty($data['email'])) {
                $data['err_email'] = "Please Enter Your E-Mail";
            } elseif (!$this->userModel->getUserByEmail($data['email'])) {
                $data['err_email'] = "E-Mail does not exists! Kindly Register";
            }
            
            if (empty($data['password'])) {
                $data['err_password'] = "Please Enter Your Password";
            }
            
            if (empty($data['err_email']) && empty($data['err_password'])) {
                $user = $this->userModel->login($data['email'], $data['password']);
                if (!$user) {
                    $data['err_password'] = "Password is incorrect!";
                    $this->view("users/login", $data);
                } else {
                    flash("login_success", "You are Successfully Logged In!");
                    $this->createUserSession($user);
                }
            } else {
                $this->view("users/login", $data);
            }
        } else {
            $data = [
                'email' => '',
                'password' => '',
                'err_email' => '',
                'err_password' => '',
            ];

            $this->view('users/login', $data);
        }
    }
    function createUserSession($user) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['user_email'] = $user->email;
        // redirect("pages/index");
        redirect("complaints");
    }
    function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_email']);
        session_destroy();
        // flash("logout_successfull", "You are Successfully Logged Out");
        redirect("users/login");
    }

}
