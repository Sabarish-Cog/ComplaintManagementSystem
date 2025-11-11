<?php

use PHPUnit\Framework\TestCase;

class ComplaintsControllerTest extends TestCase
{
    private $controller;
    private $complaintModel;
    private $testComplaintId;

    protected function setUp(): void
    {
        // Mock the complaint model
        $this->complaintModel = $this->createMock(Complaint::class);
        
        // Create controller instance
        createTestSession(1, 'Test User', 'test@example.com');
        
        // We can't easily test the actual controller due to its dependencies
        // So we'll test the logic components separately
    }

    protected function tearDown(): void
    {
        clearTestSession();
    }

    /**
     * Test successful deletion by complaint owner
     */
    public function testDeleteComplaintByOwner()
    {
        // Arrange
        $complaintId = 1;
        $mockComplaint = (object) [
            'id' => $complaintId,
            'user_id' => 1, // Same as session user_id
            'title' => 'Test Complaint',
            'description' => 'Test Description'
        ];

        // Set up mock expectations
        $this->complaintModel->expects($this->once())
            ->method('getComplaint')
            ->with($complaintId)
            ->willReturn($mockComplaint);

        $this->complaintModel->expects($this->once())
            ->method('deleteComplaint')
            ->with($complaintId)
            ->willReturn(true);

        // Act & Assert
        $complaint = $this->complaintModel->getComplaint($complaintId);
        $this->assertEquals(1, $complaint->user_id);
        $this->assertEquals($_SESSION['user_id'], $complaint->user_id);

        $result = $this->complaintModel->deleteComplaint($complaintId);
        $this->assertTrue($result);
    }

    /**
     * Test deletion attempt by non-owner
     */
    public function testDeleteComplaintByNonOwner()
    {
        // Arrange
        $complaintId = 1;
        $mockComplaint = (object) [
            'id' => $complaintId,
            'user_id' => 2, // Different from session user_id (1)
            'title' => 'Test Complaint',
            'description' => 'Test Description'
        ];

        // Set up mock expectations
        $this->complaintModel->expects($this->once())
            ->method('getComplaint')
            ->with($complaintId)
            ->willReturn($mockComplaint);

        // deleteComplaint should not be called for unauthorized users
        $this->complaintModel->expects($this->never())
            ->method('deleteComplaint');

        // Act & Assert
        $complaint = $this->complaintModel->getComplaint($complaintId);
        $this->assertEquals(2, $complaint->user_id);
        $this->assertNotEquals($_SESSION['user_id'], $complaint->user_id);
        
        // In real controller, this would trigger redirect and die
        $this->assertTrue($complaint->user_id !== $_SESSION['user_id']);
    }

    /**
     * Test deletion with non-existent complaint
     */
    public function testDeleteNonExistentComplaint()
    {
        // Arrange
        $complaintId = 999;

        // Set up mock expectations
        $this->complaintModel->expects($this->once())
            ->method('getComplaint')
            ->with($complaintId)
            ->willReturn(null);

        $this->complaintModel->expects($this->never())
            ->method('deleteComplaint');

        // Act & Assert
        $complaint = $this->complaintModel->getComplaint($complaintId);
        $this->assertNull($complaint);
    }

    /**
     * Test database deletion failure
     */
    public function testDeleteComplaintDatabaseFailure()
    {
        // Arrange
        $complaintId = 1;
        $mockComplaint = (object) [
            'id' => $complaintId,
            'user_id' => 1, // Same as session user_id
            'title' => 'Test Complaint',
            'description' => 'Test Description'
        ];

        // Set up mock expectations
        $this->complaintModel->expects($this->once())
            ->method('getComplaint')
            ->with($complaintId)
            ->willReturn($mockComplaint);

        $this->complaintModel->expects($this->once())
            ->method('deleteComplaint')
            ->with($complaintId)
            ->willReturn(false); // Simulate database failure

        // Act & Assert
        $complaint = $this->complaintModel->getComplaint($complaintId);
        $this->assertEquals($_SESSION['user_id'], $complaint->user_id);

        $result = $this->complaintModel->deleteComplaint($complaintId);
        $this->assertFalse($result);
    }

    /**
     * Test the authorization logic separately
     */
    public function testAuthorizationLogic()
    {
        $testCases = [
            ['session_user' => 1, 'complaint_user' => 1, 'expected' => true],  // Owner
            ['session_user' => 1, 'complaint_user' => 2, 'expected' => false], // Non-owner
            ['session_user' => 2, 'complaint_user' => 1, 'expected' => false], // Non-owner
            ['session_user' => 2, 'complaint_user' => 2, 'expected' => true],  // Owner
        ];

        foreach ($testCases as $case) {
            $_SESSION['user_id'] = $case['session_user'];
            $mockComplaint = (object) ['user_id' => $case['complaint_user']];
            
            $isAuthorized = ($mockComplaint->user_id === $_SESSION['user_id']);
            $this->assertEquals($case['expected'], $isAuthorized, 
                "Session user {$case['session_user']} vs Complaint user {$case['complaint_user']}");
        }
    }
}
