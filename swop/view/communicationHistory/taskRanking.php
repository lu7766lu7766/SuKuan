<?php
Bundle::addLink("datetime");
$this->partialView($top_view_path);

$choice_id = $model->session[ "choice" ];
?>
<h3 id="title"><?php echo $this->menu->currentName ?></h3>
<!--<input id="switch" class="bootstrap-switch" type="checkbox" checked data-size="mini" data-off-color="danger">-->

<div id="app">
	<task-ranking />
</div>

<?php
Bundle::echoLink("laravel-mix");
$this->partialView($bottom_view_path);
?>
