<?php require APPROOT."/views/univ/header.php" ?>
<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card card-body bg-light mt-5">
            <?php echo flash("register_success") ?>
            <h4>Proceed to Login!</h4>
            <p>Please Fill-in Your Credentials.</p>
            <form action="<?php echo URLROOT . '/users/login'?>" method="post">
                <div class="form-group m-1">
                    <label for="email">E-Mail: </label>
                    <input type="email" name="email" id="email" class="form-control form-control-lg <?php echo (empty($data['err_email']))? '' : 'is-invalid' ?>" value="<?php echo $data['email'] ?>">
                    <span class="invalid-feedback"><?php echo $data['err_email'] ?></span>
                </div>
                <div class="form-group m-1">
                    <label for="password">Password: </label>
                    <input type="password" name="password" id="password" class="form-control form-control-lg <?php echo (empty($data['err_password']))? '' : 'is-invalid' ?>" value="<?php echo $data['password'] ?>">
                    <span class="invalid-feedback"><?php echo $data['err_password'] ?></span>
                </div>

                <div class="row p-2">
                    <div class="col-12 d-flex justify-content-center mb-2">
                        <input type="submit" value="Login" class="btn btn-success w-50 me-2">
                        <a href="<?php echo URLROOT; ?>/users/register" class="btn btn-light w-50 ms-2">Need a New Account? Register Here!</a>
                    </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require APPROOT."/views/univ/footer.php" ?>