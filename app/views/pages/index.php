<?php require APPROOT."/views/univ/header.php" ?>
<?php echo flash("login_success"); ?>
<div class="jumbotron jumbotron-flud text-center">
    <div class="container">
        <h3 class="display-3"><?php echo $data["title"] ?></h3>
        <p class="lead"><?php echo $data["data"] ?></p>
    </div>
</div>
<?php require APPROOT."/views/univ/footer.php" ?>