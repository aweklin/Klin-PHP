<?php $this->setTitle('Not Found!'); ?>
<?php $this->section('body'); ?>

<h1>Not Found</h1>

<p>The requested page could not be found.</p>

<p>
    <a href="javascript:history.go(-1);">Back to previous page</a> | 
    <a href="<?= APP_BASE_URL ?>">Got to home page</a>
</p>

<?php $this->end(); ?>