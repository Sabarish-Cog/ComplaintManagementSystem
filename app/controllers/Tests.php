<?php

class Tests extends Controller
{
    public function __construct()
    {
        // Allow access without authentication for testing
    }

    public function index()
    {
        $this->view('tests/index');
    }

    public function delete_function()
    {
        // Set up test session
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 1;
            $_SESSION['user_name'] = 'Test User';
            $_SESSION['user_email'] = 'test@example.com';
        }

        $this->runDeleteFunctionTests();
    }

    public function database()
    {
        $this->runDatabaseTests();
    }

    public function standalone()
    {
        $this->runStandaloneTests();
    }

    private function runDeleteFunctionTests()
    {
        require_once '../tests/test_delete_function.php';
        
        echo "<style>
            .test-pass { color: green; }
            .test-fail { color: red; }
            .test-info { color: blue; }
            body { font-family: Arial, sans-serif; margin: 20px; }
        </style>";
        
        $test = new DeleteFunctionTest();
        $test->runTests();
    }

    private function runDatabaseTests()
    {
        require_once '../tests/direct_test.php';
        
        $test = new DirectDeleteTest();
        $test->testDatabaseOperations();
    }

    private function runStandaloneTests()
    {
        require_once '../tests/standalone_test.php';
        
        $test = new StandaloneDeleteTest();
        $test->runAllTests();
    }
}
