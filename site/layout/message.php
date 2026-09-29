<?php
        // session_start();
        // 
        $message = '';
        $classType = '';
        if (!empty($_SESSION['success'])) {
            $classType = 'success';
            $message = $_SESSION['success'];
            unset($_SESSION['success']); //xóa phần tử có key là success
        } else if (!empty($_SESSION['error'])) {
            $classType = 'danger';
            $message = $_SESSION['error'];
            unset($_SESSION['error']); //xóa phần tử có key là success
        }
        if ($message):

        ?>
<!-- .alert.alert-success -->
<div class="text-center alert alert-<?= $classType ?> m-0"><?= $message ?></div>
<?php
        endif;
