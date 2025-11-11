<?php
// Direct database test - no authentication required
// Access via: http://localhost/complaintit/tests/direct_test.php

// Include only database components
require_once "../app/config/config.php";
require_once "../app/libraries/Database.php";

class DirectDeleteTest {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function testDatabaseOperations() {
        echo "<!DOCTYPE html>";
        echo "<html><head><title>Direct Database Test</title>";
        echo "<style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .success { color: green; }
            .error { color: red; }
            .info { color: blue; }
            .warning { color: orange; }
            table { border-collapse: collapse; width: 100%; margin: 10px 0; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
            .test-section { background: #f8f9fa; padding: 15px; margin: 15px 0; border-left: 4px solid #007cba; }
        </style>";
        echo "</head><body>";
        
        echo "<h1>🔧 Direct Database Test for Delete Function</h1>";
        echo "<p>This test directly interacts with the database to test the delete functionality.</p>";
        
        // Test 1: Show all complaints
        $this->showAllComplaints();
        
        // Test 2: Test getComplaint method
        $this->testGetComplaint();
        
        // Test 3: Test deleteComplaint method
        $this->testDeleteComplaint();
        
        // Test 4: Show database connection info
        $this->showDatabaseInfo();
        
        echo "</body></html>";
    }
    
    private function showAllComplaints() {
        echo "<div class='test-section'>";
        echo "<h2>📋 All Complaints in Database</h2>";
        
        try {
            $this->db->query("SELECT *, complaints.id AS complaint_id, users.id AS user_id, complaints.created_on AS complaint_created_on
                             FROM complaints
                             INNER JOIN users
                             ON users.id = complaints.user_id
                             ORDER BY complaints.created_on DESC");
            $complaints = $this->db->resultSet();
            
            if (empty($complaints)) {
                echo "<p class='warning'>⚠️ No complaints found in database.</p>";
                echo "<p>Please create some complaints first by visiting: <a href='../complaints/add'>Add Complaint</a></p>";
            } else {
                echo "<p class='success'>✅ Found " . count($complaints) . " complaints</p>";
                echo "<table>";
                echo "<tr><th>ID</th><th>Title</th><th>User ID</th><th>User Name</th><th>Created</th></tr>";
                foreach ($complaints as $complaint) {
                    echo "<tr>";
                    echo "<td>{$complaint->complaint_id}</td>";
                    echo "<td>" . htmlspecialchars($complaint->title) . "</td>";
                    echo "<td>{$complaint->user_id}</td>";
                    echo "<td>" . htmlspecialchars($complaint->name) . "</td>";
                    echo "<td>{$complaint->complaint_created_on}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
        } catch (Exception $e) {
            echo "<p class='error'>❌ Error fetching complaints: " . $e->getMessage() . "</p>";
        }
        
        echo "</div>";
    }
    
    private function testGetComplaint() {
        echo "<div class='test-section'>";
        echo "<h2>🔍 Test getComplaint() Method</h2>";
        
        // Get a valid complaint ID first
        try {
            $this->db->query("SELECT id FROM complaints LIMIT 1");
            $result = $this->db->single();
            
            if ($result) {
                $testId = $result->id;
                echo "<h3>Testing with valid ID: $testId</h3>";
                
                $this->db->query("SELECT * FROM complaints WHERE id = :id");
                $this->db->bind(':id', $testId);
                $complaint = $this->db->single();
                
                if ($complaint) {
                    echo "<p class='success'>✅ getComplaint($testId) successful</p>";
                    echo "<pre>";
                    echo "ID: {$complaint->id}\n";
                    echo "User ID: {$complaint->user_id}\n";
                    echo "Title: {$complaint->title}\n";
                    echo "Description: {$complaint->description}\n";
                    echo "</pre>";
                } else {
                    echo "<p class='error'>❌ getComplaint($testId) failed</p>";
                }
            } else {
                echo "<p class='warning'>⚠️ No complaints available for testing</p>";
            }
            
            // Test with invalid ID
            echo "<h3>Testing with invalid ID: 99999</h3>";
            $this->db->query("SELECT * FROM complaints WHERE id = :id");
            $this->db->bind(':id', 99999);
            $invalidComplaint = $this->db->single();
            
            if (!$invalidComplaint) {
                echo "<p class='success'>✅ getComplaint(99999) correctly returns false for invalid ID</p>";
            } else {
                echo "<p class='error'>❌ getComplaint(99999) should return false for invalid ID</p>";
            }
            
        } catch (Exception $e) {
            echo "<p class='error'>❌ Error testing getComplaint: " . $e->getMessage() . "</p>";
        }
        
        echo "</div>";
    }
    
    private function testDeleteComplaint() {
        echo "<div class='test-section'>";
        echo "<h2>🗑️ Test deleteComplaint() Method</h2>";
        
        try {
            // First, create a test complaint
            echo "<h3>Creating test complaint...</h3>";
            $this->db->query("INSERT INTO complaints (user_id, title, description) VALUES (:user_id, :title, :description)");
            $this->db->bind(':user_id', 999); // Use a test user ID
            $this->db->bind(':title', 'Test Complaint for Deletion');
            $this->db->bind(':description', 'This complaint will be deleted as part of testing');
            
            if ($this->db->execute()) {
                echo "<p class='success'>✅ Test complaint created</p>";
                
                // Get the ID of the complaint we just created
                $this->db->query("SELECT id FROM complaints WHERE title = 'Test Complaint for Deletion' ORDER BY id DESC LIMIT 1");
                $newComplaint = $this->db->single();
                
                if ($newComplaint) {
                    $testId = $newComplaint->id;
                    echo "<p class='info'>Test complaint ID: $testId</p>";
                    
                    // Now test deletion
                    echo "<h3>Testing deletion...</h3>";
                    $this->db->query("DELETE FROM complaints WHERE id = :id");
                    $this->db->bind(':id', $testId);
                    
                    if ($this->db->execute()) {
                        echo "<p class='success'>✅ deleteComplaint() executed successfully</p>";
                        
                        // Verify deletion
                        $this->db->query("SELECT * FROM complaints WHERE id = :id");
                        $this->db->bind(':id', $testId);
                        $deletedComplaint = $this->db->single();
                        
                        if (!$deletedComplaint) {
                            echo "<p class='success'>✅ Complaint successfully removed from database</p>";
                        } else {
                            echo "<p class='error'>❌ Complaint still exists after deletion</p>";
                        }
                    } else {
                        echo "<p class='error'>❌ deleteComplaint() failed to execute</p>";
                    }
                }
            } else {
                echo "<p class='error'>❌ Failed to create test complaint</p>";
            }
            
        } catch (Exception $e) {
            echo "<p class='error'>❌ Error testing deleteComplaint: " . $e->getMessage() . "</p>";
        }
        
        echo "</div>";
    }
    
    private function showDatabaseInfo() {
        echo "<div class='test-section'>";
        echo "<h2>🔗 Database Connection Info</h2>";
        
        echo "<table>";
        echo "<tr><th>Setting</th><th>Value</th></tr>";
        echo "<tr><td>Database Host</td><td>" . DB_HOST . "</td></tr>";
        echo "<tr><td>Database Name</td><td>" . DB_NAME . "</td></tr>";
        echo "<tr><td>Database User</td><td>" . DB_USER . "</td></tr>";
        echo "<tr><td>Connection Status</td><td class='success'>✅ Connected</td></tr>";
        echo "</table>";
        
        // Test basic query
        try {
            $this->db->query("SELECT COUNT(*) as total FROM complaints");
            $result = $this->db->single();
            echo "<p class='success'>✅ Database query test successful - Total complaints: {$result->total}</p>";
        } catch (Exception $e) {
            echo "<p class='error'>❌ Database query test failed: " . $e->getMessage() . "</p>";
        }
        
        echo "</div>";
    }
}

// Run the test
$test = new DirectDeleteTest();
$test->testDatabaseOperations();
?>
