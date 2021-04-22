<?php use Framework\Core\Html; ?>

<?php $this->section('body') ?>

<h1 class="text-center red">Welcome to <?php echo SITE_TITLE ?>!</h1>

<div class="col-md-6 col-lg-4 offset-md-3 offset-lg-4 well" style="margin-top: 2rem;">
    <form class="form" id="registerForm" action="<?= APP_BASE_URL ?>account/register" method="post">
        <?= Html::formToken() ?>
        
        <h3 class="text-center">Register</h3>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" class="form-control" value="" required autocomplete="off" autofocus>
        </div>

        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" class="form-control" value="" required autocomplete="off">
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" class="form-control" value="" required autocomplete="off" >
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" value="" required autocomplete="off" >
        </div>

        <div class="form-group">
            <p id="registerMessage"></p>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-large btn-primary">Register</button>

            <a href="<?= APP_BASE_URL ?>account/login" class="float-right text-right" title="Login">Login</a>
        </div>

    </form>
</div>

<?php $this->end(); ?>

<?php $this->section('footer'); ?>

<script>
    $(function() {

        $('#registerForm').submit(function(e) {
            e.preventDefault();

            const $messageDiv = $('#registerMessage');
            $messageDiv.showMsg('i', 'Creating your profile...');
            try {
                $.post($(this).attr('action'), $(this).serialize())
                .then(function(result) {
                    if (result.hasError) {
                        $messageDiv.showMsg('e', result.message);
                    } else {
                        $messageDiv.showMsg('s', result.message);
                        window.location.href = '<?= APP_BASE_URL ?>home/index';
                    }
                }, function(error) {
                    $messageDiv.showMsg('e', error.responseText);
                });
            } catch (error) {
                $messageDiv.showMsg('e', error);
            }
        });

    });
</script>

<?php $this->end(); ?>