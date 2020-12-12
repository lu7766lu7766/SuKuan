<?php
Bundle::addLink("datetime");
Bundle::addLink("loading");
Bundle::addLink("alertify");
$this->partialView($top_view_path);
?>
<h3 id="title"><?php echo $this->menu->currentName ?></h3>
<!--<input id="switch" class="bootstrap-switch" type="checkbox" checked data-size="mini" data-off-color="danger">-->

<div id="app">
  <communication-search />
</div>

<?php
Bundle::echoLink("laravel-mix");
$this->partialView($bottom_view_path);
?>