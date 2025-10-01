<?php require APPROOT . "/views/univ/header.php" ?>
<div class="card card-body bg-light mt-5">
    <h5>Kindly Modify the below details!</h5>
    <form action="<?php echo URLROOT . '/complaints/edit/' . $data['id'] ?>" method="post">
        <div class="form-group m-1">
            <label for="title">Complaint Title: </label>
            <input type="text" name="title" id="title" class="form-control form-control-lg <?php echo (empty($data['err_title'])) ? '' : 'is-invalid' ?>" value="<?php echo $data['title'] ?>">
            <span class="invalid-feedback"><?php echo $data['err_title'] ?></span>
        </div>
        <div class="form-group m-1">
            <label for="description">Description: </label>
            <textarea name="description" id="description" class="form-control form-control-lg"><?php echo $data['description'] ?></textarea>
        </div>
        <input type="submit" value="Submit" class="btn btn-success m-1">
    </form>
</div>

<?php require APPROOT . "/views/univ/footer.php" ?>