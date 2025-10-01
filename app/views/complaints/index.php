<?php require APPROOT . "/views/univ/header.php"; ?>
<h4 class="center mb-3"> All Complaints
    <a href="<?php echo URLROOT ?>/complaints/add" class="btn btn-primary float-end"><i class="bi bi-plus-circle"></i> Raise a Complaint</a>
</h4>

<?php flash("complaint_success") ?>
<?php foreach ($data['complaints'] as $complaint) : ?>
    <div class="card text-center m-5">
        <div class="card-header">
            <?php echo ($complaint->user_id == $_SESSION['user_id']) ? "You" : $complaint->name ?>
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <h5 class="card-title w-75"><?php echo $complaint->title ?></h5>
                    <?php if ($complaint->user_id == $_SESSION['user_id']) : ?>
                        <div>
                            <a href="<?php echo URLROOT ?>/complaints/edit/<?php echo $complaint->complaint_id ?>" class="btn"><i class="bi bi-pencil-square"></i></a>
                            <a href="<?php echo URLROOT ?>/complaints/delete/<?php echo $complaint->complaint_id ?>" class="btn"><i class="bi bi-trash"></i></a>
                        </div>
                    <?php else: ?>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                    <?php endif; ?>
                </li>
                <li class="list-group-item">
                    <p class="card-text"><?php echo $complaint->description ?></p>
                </li>
            </ul>
        </div>
        <div class="card-footer text-body-secondary">
            <?php echo $complaint->complaint_created_on ?>
            <?php $date = new DateTime($complaint->complaint_created_on);
            $curr = new DateTime();
            $days = $curr->diff($date)->days ?>
            <?php if ($days > 0) echo ($days > 1) ? $days . " days ago." : "A day ago" ?>
        </div>
    </div>
<?php endforeach ?>

<?php require APPROOT . "/views/univ/footer.php"; ?>