<?php use Framework\Core\Html; ?>

<?php $this->setTitle('Getting Started') ?>

<?php $this->section('body'); ?>

    <div class="row main-content">
        <p>&nbsp;</p>

        <?php Html::partial('sidebar_tutorial'); ?>

        <div class="col-md-9">

            <div class="about-agileits-title"> 
                <h3>Getting Started with KlinPHP</h3>
            </div>

            <h5>Requirements</h5>
            <p>To run a KlinPHP app, your web server must be running the following minimum requirements:</p>
            <ul>
                <li>A web server: Apache, IIS, etc</li>
                <li>Web server Rewrite module</li>
                <li>PHP Version: 7.0 or higher</li>
                <li>PHP PDO extension</li>
                <li>PHP mbstring extension</li>
                <li>PHP OpenSSL extension</li>
            </ul>

            <p>&nbsp;</p>
            <h5>Installation</h5>
            <p>For now, installation is only possible by cloning the stater app on <a href="<?= LINK_GITHUB ?>">GitHub.com</a>.</p>
            <p>The starter app gives you the complete framework dependencies and few pages to start building your app.</p>
            <p>
                Head up to <a href="<?= LINK_GITHUB_START_APP ?>"><?= LINK_GITHUB_START_APP ?></a> to clone th starter app!
            </p>
            <p>
                Once your have cloned the app, copy the cloned app to your app on your preferred web browser, 
                then visit <code>http://localhost/startApp</code> and you should get this
            </p>

            <img src="<?= URL_PUBLIC_IMG ?>tutorial/01-Welcome.png" style="width: 100%">

            <hr>
            <p>
                <strong class="red">
                    Having issues?<br>
                </strong>
                <p>
                    If you do not have the page shown above, do not worry, we will explain everything you need to do to resolved issues like this in the 
                    <a href="<?= APP_BASE_URL ?>tutorial/configuring_your_app">Configuring your app</a> section of this tutorial.
                </p>
            </p>

            <div class="readmore text-right">
                <a href="<?= APP_BASE_URL ?>tutorial/project_structure">Next ></a>
            </div>
            <p>&nbsp;</p>
        </div>
    </div>
<?php $this->end(); ?>

<?php $this->section('footer'); ?>

<script>
    
</script>

<?php $this->end(); ?>