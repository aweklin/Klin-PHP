<?php 
  use Framework\Core\Html;
  use Framework\Infrastructure\Session; 
  use App\Src\Models\User; 
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="<?= APP_BASE_URL; ?>app/public/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= APP_BASE_URL; ?>app/public/css/custom.css">

    <title><?= $this->getTitle(); ?></title>

    <?= $this->getContent('head'); ?>
  </head>
  <body>

    
    <!-- navigation bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <a class="navbar-brand" href="#">KlinPHP</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav">
          <li class="nav-item active">
            <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Features</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Pricing</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Dropdown link
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
              <a class="dropdown-item" href="#">Action</a>
              <a class="dropdown-item" href="#">Another action</a>
              <a class="dropdown-item" href="#">Something else here</a>
            </div>
          </li>
        </ul>
      </div>
    </nav>    
    
    <div class="container-fluid">

      <!-- body -->
      <div id="pageContent" class="container" style="min-height: cal(100% - 125px) !important;">
        <?php if (Session::exists(APP_MESSAGE)) { ?>
          <p>&nbsp;</p>
          <div class="alert alert-<?= Session::get(APP_MESSAGE_TYPE) ?>" alert-dismissable>
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button"> × </button>
            <?= Session::get(APP_MESSAGE) ?>
          </div>
          <?php Session::delete(APP_MESSAGE); Session::delete(APP_MESSAGE_TYPE); ?>
        <?php } ?>
		<p>&nbsp;</p>
        <?= $this->getContent('body'); ?>
      </div>

      <footer class="fixed-bottom bg-light" style="padding-top: 15px; border-top: solid 1px lightgray">        
        <p class="text-center">&copy; <?= date('Y') ?> Aweklin.</p>
      </footer>
    </div>

    <script src="<?= APP_BASE_URL; ?>app/public/js/jquery-2.2.4.min.js"></script>
    <script src="<?= APP_BASE_URL; ?>app/public/js/bootstrap.min.js"></script>
    <script src="<?= APP_BASE_URL; ?>app/public/js/custom.js"></script>    
    <script>
      window.removeContainerClassFromPage = function() {
        $('#pageContent').removeClass('container');
      }
    </script>
    
    <?= $this->getContent('footer'); ?>
  </body>
</html>