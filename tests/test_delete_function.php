<?php
// Simple test script for delete function
// Run this script from: http://localhost/complaintit/tests/test_delete_function.php

// Set up a mock session to bypass authentication
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_name'] = 'Test User';
$_SESSION['user_email'] = 'test@example.com';

require_once "../app/bootstrap.php";

class DeleteFunctionTest {
    private $complaintModel;
    
    public function __construct() {
        $this->complaintModel = new Complaint();
    }
    
    public function runTests() {
        echo "<h2>Testing Complaints Delete Function</h2>";
        echo "<style>
            .test-pass { color: green; }
            .test-fail { color: red; }
            .test-info { color: blue; }
            body { font-family: Arial, sans-serif; margin: 20px; }
        </style>";
        
        // Test 1: Check if complaint exists
        $this->testComplaintExists();
        
        // Test 2: Test authorization logic
        $this->testAuthorizationLogic();
        
        // Test 3: Test database deletion
        $this->testDatabaseDeletion();
        
        echo "<h3>Manual Testing Instructions:</h3>";
        echo "<ol>";
        echo "<li>Login to the application</li>";
        echo "<li>Create a test complaint</li>";
        echo "<li>Try to delete your own complaint (should work)</li>";
        echo "<li>Login as different user and try to delete another user's complaint (should fail)</li>";
        echo "<li>Try to delete with invalid ID (should fail)</li>";
        echo "</ol>";
    }
    
    private function testComplaintExists() {
        echo "<h3>Test 1: Complaint Existence Check</h3>";
        
        // Get all complaints to find a valid ID
        $complaints = $this->complaintModel->getAllComplaints();
        
        if (empty($complaints)) {
            echo "<p class='test-info'>No complaints found. Create a complaint first to test.</p>";
            return;
        }
        
        $testId = $complaints[0]->complaint_id;
        $complaint = $this->complaintModel->getComplaint($testId);
        
        if ($complaint) {
            echo "<p class='test-pass'>✅ getComplaint($testId) returns valid complaint</p>";
            echo "<pre>Complaint found: ID={$complaint->id}, Title={$complaint->title}, User ID={$complaint->user_id}</pre>";
        } else {
            echo "<p class='test-fail'>❌ getComplaint($testId) failed</p>";
        }
        
        // Test with invalid ID
        $invalidComplaint = $this->complaintModel->getComplaint(99999);
        if (!$invalidComplaint) {
            echo "<p class='test-pass'>✅ getComplaint(99999) correctly returns null for invalid ID</p>";
        } else {
            echo "<p class='test-fail'>❌ getComplaint(99999) should return null for invalid ID</p>";
        }
    }
    
    private function testAuthorizationLogic() {
        echo "<h3>Test 2: Authorization Logic</h3>";
        
        // Simulate session data
        $_SESSION['user_id'] = 1;
        
        $complaints = $this->complaintModel->getAllComplaints();
        if (empty($complaints)) {
            echo "<p class='test-info'>No complaints to test authorization</p>";
            return;
        }
        
        foreach ($complaints as $complaint) {
            $isOwner = ($complaint->user_id == $_SESSION['user_id']);
            $status = $isOwner ? "ALLOWED" : "BLOCKED";
            $class = $isOwner ? "test-pass" : "test-info";
            
            echo "<p class='$class'>Complaint ID {$complaint->complaint_id}: User {$complaint->user_id} vs Session User {$_SESSION['user_id']} = $status</p>";
        }
    }
    
    private function testDatabaseDeletion() {
        echo "<h3>Test 3: Database Deletion Method</h3>";
        
        // Create a test complaint first
        $testData = [
            'user_id' => 999, // Test user ID
            'title' => 'Test Complaint for Deletion',
            'description' => 'This is a test complaint that will be deleted'
        ];
        
        $added = $this->complaintModel->addComplaint($testData);
        if ($added) {
            echo "<p class='test-pass'>✅ Test complaint created successfully</p>";
            
            // Get the ID of the complaint we just created
            $complaints = $this->complaintModel->getAllComplaints();
            $testComplaint = null;
            foreach ($complaints as $complaint) {
                if ($complaint->title === 'Test Complaint for Deletion') {
                    $testComplaint = $complaint;
                    break;
                }
            }
            
            if ($testComplaint) {
                echo "<p class='test-info'>Test complaint ID: {$testComplaint->complaint_id}</p>";
                
                // Test deletion
                $deleted = $this->complaintModel->deleteComplaint($testComplaint->complaint_id);
                if ($deleted) {
                    echo "<p class='test-pass'>✅ deleteComplaint() method works correctly</p>";
                    
                    // Verify it's actually deleted
                    $deletedComplaint = $this->complaintModel->getComplaint($testComplaint->complaint_id);
                    if (!$deletedComplaint) {
                        echo "<p class='test-pass'>✅ Complaint successfully removed from database</p>";
                    } else {
                        echo "<p class='test-fail'>❌ Complaint still exists in database after deletion</p>";
                    }
                } else {
                    echo "<p class='test-fail'>❌ deleteComplaint() method failed</p>";
                }
            }
        } else {
            echo "<p class='test-fail'>❌ Could not create test complaint</p>";
        }
    }
}

// Run the tests if accessed directly
if (basename($_SERVER['PHP_SELF']) === 'test_delete_function.php') {
    $test = new DeleteFunctionTest();
    $test->runTests();
}
?>
