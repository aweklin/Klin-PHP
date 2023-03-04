<?php use Framework\Core\Html;  ?>

<?php $this->section('body'); ?>

<div class="row main-content">

    <?php Html::partial('sidebar_tutorial'); ?>

    <div class="col-md-9">
    
        <div class="about-agileits-title"> 
            <h3>KlinPHP Project Structure</h3>
        </div>

        <div class="col-md-5">
            <img src="<?= URL_PUBLIC_IMG ?>tutorial/02-ProjectStructure_02.png" style="width: 100%">
        </div>
        <div class="col-md-6 col-md-offset-1">
            
        </div>
    
    </div>

</div>

<?php $this->end(); ?>