<?php use Framework\Core\Html; ?>

<?php $this->section('body') ?>

<h1 class="text-center red">Welcome to <?php echo SITE_TITLE ?>!</h1>

<div class="col-md-6 col-lg-4 offset-md-3 offset-lg-4" style="margin-top: 2rem;">
    
    <form class="form" id="loginForm" action="<?= APP_BASE_URL ?>account/login" method="post">
        <?= Html::formToken() ?>

        <h3 class="text-center">Log In</h3>

        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" class="form-control" value="" autocomplete="off" autofocus>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" class="form-control" value="" autocomplete="off" >
        </div>

        <div class="form-group">
            <label for="remember_me">
                Remember me 
                <input type="checkbox" id="remember_me" name="remember_me" value="on">
            </label>

            <a href="<?= APP_BASE_URL ?>account/recoverPassword" class="float-right text-right" title="Recover password">I forgot my password</a>
        </div>

        <div class="form-group">
            <p id="loginMessage"></p>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-large btn-primary">Login</button>
        </div>

    </form>
</div>

<?php $this->end(); ?>

<?php $this->section('footer'); ?>

<script>
    $(function() {

        $('#loginForm').submit(async function(e) {
            e.preventDefault();

            const $messageDiv = $('#loginMessage');
            $messageDiv.showMsg('i', 'Authenticating your credentials...');
            try {
                const result = await noneGetAsync(
                    'POST',
                    $(this).attr('action'),
                    {
                        username: $('#username').val(),
                        password: $('#password').val(),
                        form_token: $('#form_token').val(),
                    }
                );
                if (result.hasError) {
                    $messageDiv.showMsg('e', result.message);
                } else {
                    $messageDiv.showMsg('s', result.message);
                    window.location.href = '<?= APP_BASE_URL ?>home/index';
                }
            } catch (error) {
                $messageDiv.showMsg('e', error);
            }
        });

    });
</script>

<?php $this->end(); ?>