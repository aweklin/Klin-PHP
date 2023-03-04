<?php

use Framework\Core\Request;
use Framework\Utils\Str;

$mainMenus = [
    ['text' => 'Home', 'url' => Str::toLower(APP_BASE_URL . 'home')],
    ['text' => 'Get Started', 'url' => Str::toLower(APP_BASE_URL . 'tutorial')],
    ['text' => 'Documentation', 'url' => Str::toLower(APP_BASE_URL . 'documentation')],
    ['text' => 'Team', 'url' => Str::toLower(APP_BASE_URL . 'home/team')],
    ['text' => 'Contribute', 'url' => Str::toLower(APP_BASE_URL . 'home/contribute')]
];
?>

<ul class="nav navbar-nav link-effect">

<?php foreach($mainMenus as $menuItem) { ?>
    <li class="<?= (Request::getCurrentPage() == $menuItem['url'] ? 'active' : ''); ?>"><a href="<?= $menuItem['url']; ?>" class="scroll" onclick="window.location.href = $(this).attr('href');"><?= $menuItem['text']; ?></a></li>
<?php } ?>

</ul>