<?php
// Standalone test script that bypasses authentication
// Access via: http://localhost/complaintit/tests/standalone_test.php

// Start session and set up test user
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_name'] = 'Test User';
$_SESSION['user_email'] = 'test@example.com';

// Include only what we need for testing
require_once "../app/config/config.php";
require_once "../app/helpers/session_helper.php";
require_once "../app/helpers/url_helper.php";
require_once "../app/libraries/Database.php";
require_once "../app/models/Complaint.php";

// Override redirect function for testing
function redirect($page) {
    echo "<div style='background: #f0f8ff; padding: 10px; border-left: 4px solid #007cba; margin: 10px 0;'>";
    echo "<strong>REDIRECT:</strong> Would redirect to: $page";
    echo "</div>";
}

// Override flash function for testing
function flash($name, $message = '', $class = 'alert alert-success') {
    if (!empty($message)) {
        echo "<div style='background: #d4edda; color: #155724; padding: 10px; border: 1px solid #c3e6cb; margin: 10px 0;'>";
        echo "<strong>FLASH MESSAGE:</strong> $name = $message";
        echo "</div>";
    }
}

class StandaloneDeleteTest {
    private $complaintModel;
    
    public function __construct() {
        $this->complaintModel = new Complaint();
    }
    
    // Simulate the delete function logic
    public function simulateDeleteFunction($id) {
        echo "<h4>🧪 Testing Delete Function with ID: $id</h4>";
        echo "<div style='background: #f8f9fa; padding: 15px; border: 1px solid #dee2e6; margin: 10px 0;'>";
        
        try {
            // Step 1: Get the complaint
            echo "<p><strong>Step 1:</strong> Getting complaint with ID $id...</p>";
            $complaint = $this->complaintModel->getComplaint($id);
            
            if (!$complaint) {
                echo "<p style='color: red;'>❌ Complaint not found</p>";
                echo "<p><strong>Result:</strong> Function would fail here (accessing null object)</p>";
                echo "</div>";
                return false;
            }
            
            echo "<p style='color: green;'>✅ Complaint found - User ID: {$complaint->user_id}, Title: {$complaint->title}</p>";
            
            // Step 2: Check authorization
            echo "<p><strong>Step 2:</strong> Checking authorization...</p>";
            echo "<p>Complaint User ID: {$complaint->user_id}</p>";
            echo "<p>Session User ID: {$_SESSION['user_id']}</p>";
            
            if ($complaint->user_id !== $_SESSION['user_id']) {
                echo "<p style='color: orange;'>⚠️ Authorization failed - not the owner</p>";
                redirect('complaints');
                echo "<p><strong>Result:</strong> Would redirect and die with 'Not Deleted'</p>";
                echo "</div>";
                return false;
            }
            
            echo "<p style='color: green;'>✅ Authorization passed - user is the owner</p>";
            
            // Step 3: Attempt deletion
            echo "<p><strong>Step 3:</strong> Attempting to delete complaint...</p>";
            $deleteResult = $this->complaintModel->deleteComplaint($id);
            
            if (!$deleteResult) {
                echo "<p style='color: red;'>❌ Database deletion failed</p>";
                echo "<p><strong>Result:</strong> Would die with 'Error in DB'</p>";
                echo "</div>";
                return false;
            }
            
            echo "<p style='color: green;'>✅ Database deletion successful</p>";
            
            // Step 4: Success actions
            echo "<p><strong>Step 4:</strong> Performing success actions...</p>";
            flash("complaint_success", "Complaint Deleted Successfully");
            redirect("complaints/index");
            echo "<p style='color: green;'><strong>Result:</strong> ✅ Deletion completed successfully!</p>";
            
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Exception occurred: " . $e->getMessage() . "</p>";
            echo "</div>";
            return false;
        }
        
        echo "</div>";
        return true;
    }
    
    public function runAllTests() {
        echo "<!DOCTYPE html>";
        echo "<html><head><title>Standalone Delete Function Test</title></head><body>";
        echo "<h1>🚀 Standalone Delete Function Test</h1>";
        echo "<p>Testing the delete function logic without controller dependencies</p>";
        
        // Get available complaints
        echo "<h2>📋 Available Complaints</h2>";
        $complaints = $this->complaintModel->getAllComplaints();
        
        if (empty($complaints)) {
            echo "<p style='color: orange;'>⚠️ No complaints found in database. Please create some complaints first.</p>";
            echo "<h3>How to create test data:</h3>";
            echo "<ol>";
            echo "<li>Go to <a href='../complaints/add'>Add Complaint</a></li>";
            echo "<li>Create a few complaints with different users</li>";
            echo "<li>Come back to this test</li>";
            echo "</ol>";
            echo "</body></html>";
            return;
        }
        
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Title</th><th>User ID</th><th>Owner</th></tr>";
        foreach ($complaints as $complaint) {
            $isOwner = ($complaint->user_id == $_SESSION['user_id']) ? '✅ Yes' : '❌ No';
            echo "<tr>";
            echo "<td>{$complaint->complaint_id}</td>";
            echo "<td>{$complaint->title}</td>";
            echo "<td>{$complaint->user_id}</td>";
            echo "<td>{$isOwner}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<h2>🧪 Test Scenarios</h2>";
        
        // Test 1: Delete own complaint
        $ownComplaint = null;
        $otherComplaint = null;
        
        foreach ($complaints as $complaint) {
            if ($complaint->user_id == $_SESSION['user_id'] && !$ownComplaint) {
                $ownComplaint = $complaint;
            } elseif ($complaint->user_id != $_SESSION['user_id'] && !$otherComplaint) {
                $otherComplaint = $complaint;
            }
        }
        
        if ($ownComplaint) {
            echo "<h3>Test 1: Delete Own Complaint (Should Success)</h3>";
            $this->simulateDeleteFunction($ownComplaint->complaint_id);
        }
        
        if ($otherComplaint) {
            echo "<h3>Test 2: Delete Other User's Complaint (Should Fail)</h3>";
            $this->simulateDeleteFunction($otherComplaint->complaint_id);
        }
        
        echo "<h3>Test 3: Delete Non-existent Complaint (Should Fail)</h3>";
        $this->simulateDeleteFunction(99999);
        
        echo "<h2>🔧 Manual Testing</h2>";
        echo "<p>To test the actual delete function:</p>";
        echo "<ol>";
        echo "<li>Go to <a href='../complaints' target='_blank'>Complaints Page</a></li>";
        echo "<li>Look for the trash icon next to your complaints</li>";
        echo "<li>Click to delete and observe the behavior</li>";
        echo "</ol>";
        
        echo "<h2>📊 Summary</h2>";
        echo "<div style='background: #e7f3ff; padding: 15px; border-left: 4px solid #007cba;'>";
        echo "<p><strong>Session User ID:</strong> {$_SESSION['user_id']}</p>";
        echo "<p><strong>Total Complaints:</strong> " . count($complaints) . "</p>";
        echo "<p><strong>Owned by Current User:</strong> " . count(array_filter($complaints, function($c) { return $c->user_id == $_SESSION['user_id']; })) . "</p>";
        echo "</div>";
        
        echo "</body></html>";
    }
}

// Run the test
$test = new StandaloneDeleteTest();
$test->runAllTests();
?>
