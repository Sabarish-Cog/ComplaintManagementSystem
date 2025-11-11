<?php

class Api extends Controller
{
    public function __construct()
    {
        // Allow API access without authentication for testing
        header('Content-Type: application/json');
    }

    public function test_delete($id = null)
    {
        // Set up test session
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 1;
            $_SESSION['user_name'] = 'Test User';
            $_SESSION['user_email'] = 'test@example.com';
        }

        $complaintModel = $this->model('Complaint');
        
        if (!$id) {
            echo json_encode([
                'error' => 'No ID provided',
                'usage' => 'GET /api/test_delete/{id}'
            ]);
            return;
        }

        $result = [
            'test_id' => $id,
            'session_user' => $_SESSION['user_id'],
            'timestamp' => date('Y-m-d H:i:s')
        ];

        try {
            // Step 1: Get complaint
            $complaint = $complaintModel->getComplaint($id);
            $result['step1_get_complaint'] = $complaint ? 'SUCCESS' : 'FAILED - Complaint not found';
            
            if ($complaint) {
                $result['complaint_data'] = [
                    'id' => $complaint->id,
                    'user_id' => $complaint->user_id,
                    'title' => $complaint->title
                ];

                // Step 2: Check authorization
                $authorized = ($complaint->user_id == $_SESSION['user_id']);
                $result['step2_authorization'] = $authorized ? 'AUTHORIZED' : 'UNAUTHORIZED';
                $result['owner_check'] = [
                    'complaint_user_id' => $complaint->user_id,
                    'session_user_id' => $_SESSION['user_id'],
                    'is_owner' => $authorized
                ];

                // Step 3: Test deletion (without actually deleting)
                $result['step3_would_delete'] = $authorized ? 'YES' : 'NO - Not authorized';
                
                if ($authorized) {
                    $result['next_actions'] = [
                        'call_deleteComplaint' => "deleteComplaint($id)",
                        'flash_message' => 'complaint_success: Complaint Deleted Successfully',
                        'redirect' => 'complaints/index'
                    ];
                } else {
                    $result['next_actions'] = [
                        'redirect' => 'complaints',
                        'die_message' => 'Not Deleted'
                    ];
                }
            } else {
                $result['error'] = 'Complaint not found - would cause null pointer exception in original code';
            }

        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }

        echo json_encode($result, JSON_PRETTY_PRINT);
    }

    public function complaints()
    {
        $complaintModel = $this->model('Complaint');
        $complaints = $complaintModel->getAllComplaints();
        
        $result = [
            'total_complaints' => count($complaints),
            'complaints' => []
        ];

        foreach ($complaints as $complaint) {
            $result['complaints'][] = [
                'id' => $complaint->complaint_id,
                'title' => $complaint->title,
                'user_id' => $complaint->user_id,
                'user_name' => $complaint->name,
                'created' => $complaint->complaint_created_on
            ];
        }

        echo json_encode($result, JSON_PRETTY_PRINT);
    }
}
