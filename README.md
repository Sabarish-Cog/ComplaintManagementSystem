# ComplaintIt

ComplaintIt is a small PHP MVC application for raising and managing user complaints. It uses a minimal custom MVC structure (controllers, models, views) and a simple PDO-based Database wrapper.

This repository includes a test suite and multiple testing approaches to validate the `Complaints` controller, specifically the `delete` function.

---

## Project Structure

- `app/` - main application source
  - `controllers/` - controllers (Complaints, Users, Pages, Api, Tests)
  - `models/` - models (Complaint, User)
  - `views/` - view templates
  - `libraries/` - core framework helpers (Controller, Core, Database)
  - `helpers/` - helper functions (session, url)
  - `config/` - configuration
- `public/` - web root (index.php, assets)
- `tests/` - test scripts, PHPUnit tests and helpers
- `run_tests.php` - command-line test runner
- `composer.json` / `phpunit.xml` - PHPUnit setup

---

## Requirements

- PHP 7.4+ (or compatible)
- MySQL / MariaDB
- XAMPP (optional) or any webserver that can serve `public/`
- Composer (for PHPUnit development dependencies)

---

## Quick Setup

1. Place the project in your web server document root (e.g. `C:\xampp\htdocs\ComplaintIt`).
2. Ensure `app/config/config.php` DB constants match your environment (DB_HOST, DB_USER, DB_PASS, DB_NAME).
3. Create the database and tables. You can use the included `tests/test_database.sql` as a starter for test data.

---

## Running the App

- Open in browser: `http://localhost/complaintit/`
- Public entry point is `public/index.php`.

---

## Testing

This project includes multiple test options to run the `delete` function and related model logic.

Options (choose one):

1. Direct web tests (if server allows):
   - `http://localhost/complaintit/tests/` (test dashboard)
   - `http://localhost/complaintit/tests/direct_test.php` (database-level tests)
   - `http://localhost/complaintit/tests/standalone_test.php` (logic flow)
   - `http://localhost/complaintit/tests/integration_test.php` (end-to-end simulation)

2. MVC-based tests (access through the app routing):
   - `http://localhost/complaintit/tests/delete_function`
   - `http://localhost/complaintit/tests/database`
   - `http://localhost/complaintit/tests/standalone`

3. API endpoints (JSON responses):
   - `http://localhost/complaintit/api/complaints` — list complaints
   - `http://localhost/complaintit/api/test_delete/{id}` — simulate delete checks

4. Command-line (always works):
   - From project root, run:
     ```powershell
     php run_tests.php
     ```

5. PHPUnit (developer):
   - Install dev dependencies: `composer install`
   - Run: `vendor\bin\phpunit` (or use `php vendor\bin\phpunit` on Windows)
   - Tests live in `tests/ComplaintsControllerTest.php` and are designed to mock model behavior.

---

## .htaccess Notes

- Root `.htaccess` is configured to route requests to `public/` for the MVC app.
- The tests folder has a dedicated `.htaccess` to allow direct access to test files. If you get redirected when accessing `tests/`, restart Apache/XAMPP and clear browser cache.

---

## Known Issues & Suggested Improvements

- `Complaints::delete($id)` currently does not validate the `$id` or check for null before accessing properties. Suggested improvement (replace method body):

```php
public function delete($id)
{
    if (!is_numeric($id) || $id <= 0) {
        flash('complaint_error', 'Invalid complaint ID');
        redirect('complaints');
        return;
    }

    $complaint = $this->complaintModel->getComplaint($id);
    if (!$complaint) {
        flash('complaint_error', 'Complaint not found');
        redirect('complaints');
        return;
    }

    if ($complaint->user_id !== $_SESSION['user_id']) {
        flash('complaint_error', 'Unauthorized access');
        redirect('complaints');
        return;
    }

    if (!$this->complaintModel->deleteComplaint($id)) {
        flash('complaint_error', 'Failed to delete complaint');
        redirect('complaints');
        return;
    }

    flash('complaint_success', 'Complaint Deleted Successfully');
    redirect('complaints/index');
}
```

---

## Debugging Tips

- Enable PHP errors for development: `ini_set('display_errors', 1); error_reporting(E_ALL);`
- Use `error_log()` to log important events (e.g. deletion attempts).
- Check database connection in `app/libraries/Database.php`.

---

## Contact

This is a sample app. For questions, modify tests or code directly in the project and re-run the tests.
