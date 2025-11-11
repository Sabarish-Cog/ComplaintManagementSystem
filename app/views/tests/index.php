<?php require APPROOT."/views/univ/header.php" ?>

<div class="container">
    <h1>🧪 Test Suite Dashboard</h1>
    
    <div class="alert alert-info">
        <strong>Note:</strong> These tests are accessible through the MVC framework without .htaccess issues.
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h5>Delete Function Test</h5>
                </div>
                <div class="card-body">
                    <p>Tests the delete function logic and model methods.</p>
                    <a href="<?php echo URLROOT; ?>/tests/delete_function" class="btn btn-primary">Run Test</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h5>Database Test</h5>
                </div>
                <div class="card-body">
                    <p>Tests database operations directly.</p>
                    <a href="<?php echo URLROOT; ?>/tests/database" class="btn btn-primary">Run Test</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h5>Standalone Test</h5>
                </div>
                <div class="card-body">
                    <p>Tests complete function workflow.</p>
                    <a href="<?php echo URLROOT; ?>/tests/standalone" class="btn btn-primary">Run Test</a>
                </div>
            </div>
        </div>
    </div>

    <hr>
    
    <h2>Alternative Test Access</h2>
    <div class="alert alert-secondary">
        <h5>Direct File Access (if .htaccess is working):</h5>
        <ul>
            <li><a href="../tests/" target="_blank">Test Suite Home</a></li>
            <li><a href="../tests/direct_test.php" target="_blank">Direct Database Test</a></li>
            <li><a href="../tests/standalone_test.php" target="_blank">Standalone Logic Test</a></li>
            <li><a href="../tests/integration_test.php" target="_blank">Integration Test</a></li>
        </ul>
    </div>

    <h2>Manual Testing</h2>
    <div class="alert alert-warning">
        <h5>Application Testing:</h5>
        <ol>
            <li>Go to <a href="<?php echo URLROOT; ?>/complaints">Complaints</a></li>
            <li>Create a test complaint</li>
            <li>Try to delete it (click trash icon)</li>
            <li>Observe the behavior</li>
        </ol>
    </div>
</div>

<?php require APPROOT."/views/univ/footer.php" ?>
