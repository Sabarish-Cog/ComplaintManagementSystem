<?php
// Command line test script
// Run with: php run_tests.php

require_once "app/bootstrap.php";

// Set up test session
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_name'] = 'Test User';
$_SESSION['user_email'] = 'test@example.com';

// Mock functions for CLI testing
function redirect($page) {
    echo "REDIRECT: $page\n";
}

function flash($name, $message = '', $class = 'alert alert-success') {
    if (!empty($message)) {
        echo "FLASH: $name = $message\n";
    }
}

class CommandLineTest {
    private $complaintModel;
    
    public function __construct() {
        $this->complaintModel = new Complaint();
    }
    
    public function runAllTests() {
        echo "=================================\n";
        echo "  DELETE FUNCTION TEST SUITE\n";
        echo "=================================\n\n";
        
        $this->testDatabaseConnection();
        $this->testGetComplaint();
        $this->testDeleteLogic();
        $this->testAuthorizationScenarios();
    }
    
    private function testDatabaseConnection() {
        echo "1. Testing Database Connection...\n";
        try {
            $complaints = $this->complaintModel->getAllComplaints();
            echo "   ✅ Database connected successfully\n";
            echo "   📊 Found " . count($complaints) . " complaints\n\n";
        } catch (Exception $e) {
            echo "   ❌ Database connection failed: " . $e->getMessage() . "\n\n";
        }
    }
    
    private function testGetComplaint() {
        echo "2. Testing getComplaint() method...\n";
        
        // Get first complaint for testing
        $complaints = $this->complaintModel->getAllComplaints();
        if (!empty($complaints)) {
            $testId = $complaints[0]->complaint_id;
            $complaint = $this->complaintModel->getComplaint($testId);
            
            if ($complaint) {
                echo "   ✅ getComplaint($testId) successful\n";
                echo "   📝 Title: {$complaint->title}\n";
                echo "   👤 User ID: {$complaint->user_id}\n";
            } else {
                echo "   ❌ getComplaint($testId) failed\n";
            }
        } else {
            echo "   ⚠️  No complaints found for testing\n";
        }
        
        // Test invalid ID
        $invalidComplaint = $this->complaintModel->getComplaint(99999);
        if (!$invalidComplaint) {
            echo "   ✅ getComplaint(99999) correctly returns null\n";
        } else {
            echo "   ❌ getComplaint(99999) should return null\n";
        }
        echo "\n";
    }
    
    private function testDeleteLogic() {
        echo "3. Testing Delete Function Logic...\n";
        
        $complaints = $this->complaintModel->getAllComplaints();
        if (empty($complaints)) {
            echo "   ⚠️  No complaints available for testing\n\n";
            return;
        }
        
        $testComplaint = $complaints[0];
        $this->simulateDeleteFunction($testComplaint->complaint_id);
        echo "\n";
    }
    
    private function simulateDeleteFunction($id) {
        echo "   🧪 Simulating delete($id)...\n";
        
        // Step 1: Get complaint
        $complaint = $this->complaintModel->getComplaint($id);
        if (!$complaint) {
            echo "   ❌ Step 1: Complaint not found\n";
            return false;
        }
        echo "   ✅ Step 1: Complaint found (User: {$complaint->user_id})\n";
        
        // Step 2: Check authorization
        if ($complaint->user_id !== $_SESSION['user_id']) {
            echo "   ⚠️  Step 2: Authorization failed (Not owner)\n";
            redirect('complaints');
            echo "   🔄 Would die with 'Not Deleted'\n";
            return false;
        }
        echo "   ✅ Step 2: Authorization passed (User is owner)\n";
        
        // Step 3: Test deletion (mock)
        echo "   🔄 Step 3: Would call deleteComplaint($id)\n";
        echo "   ✅ Step 4: Would flash success message\n";
        echo "   🔄 Step 5: Would redirect to complaints/index\n";
        
        return true;
    }
    
    private function testAuthorizationScenarios() {
        echo "4. Testing Authorization Scenarios...\n";
        
        $complaints = $this->complaintModel->getAllComplaints();
        if (empty($complaints)) {
            echo "   ⚠️  No complaints available for testing\n\n";
            return;
        }
        
        $originalUserId = $_SESSION['user_id'];
        
        foreach ($complaints as $complaint) {
            $isOwner = ($complaint->user_id == $originalUserId);
            $status = $isOwner ? "ALLOWED" : "BLOCKED";
            echo "   📋 Complaint {$complaint->complaint_id}: User {$complaint->user_id} vs Session {$originalUserId} = $status\n";
        }
        echo "\n";
        
        // Restore original session
        $_SESSION['user_id'] = $originalUserId;
    }
}

// Run tests
$test = new CommandLineTest();
$test->runAllTests();

echo "Tests completed! Run this script with: php run_tests.php\n";
?>
