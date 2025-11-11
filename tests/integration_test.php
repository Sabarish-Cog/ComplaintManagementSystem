<?php
/**
 * Integration Test for Delete Function
 * This simulates the actual delete function behavior
 */

require_once "../app/bootstrap.php";

// Mock the redirect and die functions for testing
function redirect($page) {
    echo "REDIRECT: $page\n";
    return;
}

function flash($name, $message = '', $class = 'alert alert-success') {
    echo "FLASH: $name = $message\n";
}

// Test class that mimics the controller behavior
class DeleteFunctionIntegrationTest {
    private $complaintModel;
    
    public function __construct() {
        $this->complaintModel = new Complaint();
    }
    
    // This mimics the actual delete function logic
    public function simulateDelete($id, $sessionUserId) {
        echo "\n=== Testing Delete Function with ID: $id, Session User: $sessionUserId ===\n";
        
        // Set session for test
        $_SESSION['user_id'] = $sessionUserId;
        
        try {
            // Step 1: Get complaint
            $complaint = $this->complaintModel->getComplaint($id);
            echo "Step 1: getComplaint($id) = " . ($complaint ? "Found complaint (User: {$complaint->user_id})" : "NULL") . "\n";
            
            if (!$complaint) {
                echo "Result: Complaint not found\n";
                return false;
            }
            
            // Step 2: Check authorization
            if ($complaint->user_id !== $_SESSION['user_id']) {
                echo "Step 2: Authorization check FAILED (Complaint user: {$complaint->user_id} vs Session user: {$_SESSION['user_id']})\n";
                redirect('complaints');
                echo "Result: Not Deleted - Unauthorized\n";
                return false;
            }
            
            echo "Step 2: Authorization check PASSED\n";
            
            // Step 3: Delete complaint
            $deleteResult = $this->complaintModel->deleteComplaint($id);
            echo "Step 3: deleteComplaint($id) = " . ($deleteResult ? "SUCCESS" : "FAILED") . "\n";
            
            if (!$deleteResult) {
                echo "Result: Error in DB\n";
                return false;
            }
            
            // Step 4: Success
            flash("complaint_success", "Complaint Deleted Successfully");
            redirect("complaints/index");
            echo "Result: Successfully deleted\n";
            return true;
            
        } catch (Exception $e) {
            echo "Exception: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    public function runIntegrationTests() {
        echo "<h2>Delete Function Integration Tests</h2>";
        echo "<pre>";
        
        // Get existing complaints for testing
        $complaints = $this->complaintModel->getAllComplaints();
        
        if (empty($complaints)) {
            echo "No complaints found. Please create some complaints first.\n";
            return;
        }
        
        $firstComplaint = $complaints[0];
        echo "Available complaint for testing: ID={$firstComplaint->complaint_id}, Owner={$firstComplaint->user_id}\n\n";
        
        // Test scenarios
        echo "=== TEST SCENARIOS ===\n";
        
        // Scenario 1: Valid owner deletion
        echo "\n1. Testing valid owner deletion:\n";
        $this->simulateDelete($firstComplaint->complaint_id, $firstComplaint->user_id);
        
        // Scenario 2: Unauthorized deletion
        echo "\n2. Testing unauthorized deletion:\n";
        $unauthorizedUserId = $firstComplaint->user_id + 1;
        $this->simulateDelete($firstComplaint->complaint_id, $unauthorizedUserId);
        
        // Scenario 3: Non-existent complaint
        echo "\n3. Testing non-existent complaint:\n";
        $this->simulateDelete(99999, 1);
        
        // Scenario 4: Test with different user IDs
        if (count($complaints) > 1) {
            $secondComplaint = $complaints[1];
            echo "\n4. Testing second complaint with different user:\n";
            $this->simulateDelete($secondComplaint->complaint_id, $secondComplaint->user_id);
        }
        
        echo "\n=== INTEGRATION TESTS COMPLETED ===\n";
        echo "</pre>";
    }
}

// Create and run test if accessed directly
if (basename($_SERVER['PHP_SELF']) === 'integration_test.php') {
    $test = new DeleteFunctionIntegrationTest();
    $test->runIntegrationTests();
}
?>
