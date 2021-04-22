<?php use Framework\Core\Html; ?>

<?php $this->section('body'); ?>

<h1 class="text-center red">Welcome to <?php echo SITE_TITLE ?>!</h1>


<?php $this->end(); ?>

<?php $this->section('footer'); ?>

<script src="<?= URL_PUBLIC_JS; ?>custom.js"></script>
<script>
    
</script>

<?php $this->end(); ?>