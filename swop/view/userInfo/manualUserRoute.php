<?php
$this->partialView($top_view_path);
?>
<h3 id="title"><?php echo $this->menu->currentName ?></h3>

<div id="app">
    <manual-user-route />
</div>

<?php
Bundle::echoLink("laravel-mix");
$this->partialView($bottom_view_path);
?>