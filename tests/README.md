# Testing Guide for Complaints Delete Function

This guide provides **MULTIPLE** approaches to test the `delete` function in the Complaints controller.

## 🚨 IMPORTANT: .htaccess Fix Applied

The .htaccess files have been updated to allow direct access to test files:
- Root .htaccess now excludes `/tests/` directory from MVC routing
- Tests directory has its own .htaccess to ensure direct access

## 🎯 **MULTIPLE TESTING OPTIONS** (Choose any that works)

### Option 1: Direct File Access (Recommended)
```
http://localhost/complaintit/tests/
http://localhost/complaintit/tests/direct_test.php
http://localhost/complaintit/tests/standalone_test.php
http://localhost/complaintit/tests/integration_test.php
```

### Option 2: Through MVC Framework
```
http://localhost/complaintit/tests/
http://localhost/complaintit/tests/delete_function
http://localhost/complaintit/tests/database
http://localhost/complaintit/tests/standalone
```

### Option 3: API Testing
```
http://localhost/complaintit/api/complaints
http://localhost/complaintit/api/test_delete/1
http://localhost/complaintit/api/test_delete/2
```

### Option 4: Command Line Testing
```powershell
cd c:\xampp\htdocs\ComplaintIt
php run_tests.php
```

### Option 5: Manual Testing
```
http://localhost/complaintit/complaints (login required)
```

## 🔧 **If One Method Doesn't Work, Try Another!**

1. **First try**: Direct file access (tests/)
2. **If redirected**: Use MVC routes (/tests/delete_function)
3. **If still issues**: Use API endpoints (/api/test_delete/1)
4. **Always works**: Command line (php run_tests.php)
5. **Real testing**: Manual testing in the app

## Testing Scenarios

### 1. Successful Deletion (Owner)
- **URL:** `http://localhost/complaintit/complaints/delete/[complaint_id]`
- **Expected:** Complaint deleted, flash message shown, redirect to complaints list
- **Prerequisites:** User must be logged in and own the complaint

### 2. Unauthorized Deletion (Non-owner)
- **URL:** `http://localhost/complaintit/complaints/delete/[other_user_complaint_id]`
- **Expected:** Redirect to complaints list, "Not Deleted" message
- **Prerequisites:** User logged in but trying to delete another user's complaint

### 3. Invalid Complaint ID
- **URL:** `http://localhost/complaintit/complaints/delete/99999`
- **Expected:** May cause error or unexpected behavior
- **Note:** The current code doesn't handle null complaint gracefully

### 4. Database Error Simulation
- **Method:** Temporarily modify database credentials or table structure
- **Expected:** "Error in DB" message

## Setup Instructions

### Option 1: Use Simple Test Scripts (Recommended for Quick Testing)

1. Navigate to your test scripts:
   ```bash
   cd c:\xampp\htdocs\ComplaintIt\tests
   ```

2. Open in browser:
   - `http://localhost/complaintit/tests/test_delete_function.php`
   - `http://localhost/complaintit/tests/integration_test.php`

### Option 2: Set Up PHPUnit (Professional Testing)

1. Install Composer (if not already installed):
   ```bash
   php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
   php composer-setup.php
   php -r "unlink('composer-setup.php');"
   ```

2. Install PHPUnit:
   ```bash
   php composer.phar install
   ```

3. Run PHPUnit tests:
   ```bash
   vendor\bin\phpunit tests\ComplaintsControllerTest.php
   ```

### Option 3: Database Testing

1. Create test database:
   ```bash
   mysql -u root -p < tests\test_database.sql
   ```

2. Update your config for testing (create a test config):
   ```php
   // app/config/test_config.php
   define('DB_NAME', 'complaint_db_test');
   ```

## Test Coverage Checklist

- [ ] **Authorization Check:** Only complaint owner can delete
- [ ] **Valid Deletion:** Successful deletion by owner
- [ ] **Invalid ID:** Non-existent complaint ID handling
- [ ] **Database Error:** Handle deletion failures
- [ ] **Session Validation:** Ensure user is logged in
- [ ] **Flash Messages:** Success message displayed
- [ ] **Redirects:** Proper redirection after operations

## Code Issues Found

The current `delete` function has some potential issues:

1. **No null check:** If `getComplaint()` returns null, accessing `$complaint->user_id` will cause an error
2. **Inconsistent error handling:** Uses `die()` instead of proper error handling
3. **No input validation:** Doesn't validate if `$id` is a valid integer

## Suggested Improvements

```php
public function delete($id)
{ 
    // Validate input
    if (!is_numeric($id) || $id <= 0) {
        flash("complaint_error", "Invalid complaint ID");
        redirect('complaints');
        return;
    }
    
    $complaint = $this->complaintModel->getComplaint($id);
    
    // Check if complaint exists
    if (!$complaint) {
        flash("complaint_error", "Complaint not found");
        redirect('complaints');
        return;
    }
    
    // Check authorization
    if ($complaint->user_id !== $_SESSION['user_id']) {
        flash("complaint_error", "Unauthorized access");
        redirect('complaints');
        return;
    }
    
    // Attempt deletion
    if (!$this->complaintModel->deleteComplaint($id)) {
        flash("complaint_error", "Failed to delete complaint");
        redirect('complaints');
        return;
    }
    
    flash("complaint_success", "Complaint Deleted Successfully");
    redirect("complaints/index");
}
```

## Running Tests

1. **Simple Tests:** Open test scripts in browser
2. **Manual Tests:** Use the application interface
3. **PHPUnit Tests:** Run `vendor\bin\phpunit` (after setup)
4. **Database Tests:** Use test database for safe testing

## Debugging Tips

1. Enable error reporting:
   ```php
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   ```

2. Add logging to the delete function:
   ```php
   error_log("Delete attempt: ID=$id, User={$_SESSION['user_id']}");
   ```

3. Use browser developer tools to inspect network requests

4. Check database logs for SQL execution errors
