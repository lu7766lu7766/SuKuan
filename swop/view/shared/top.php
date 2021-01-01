<!DOCTYPE html>
<html>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">
<title>
	ZH行銷
</title>

<head>
	<?php
	Bundle::echoLink("jquery");
	Bundle::echoLink("bootstrap");
	Bundle::echoLink("default");
	echo Bundle::$allLink;

	$choice_id = $model->session["choice"];
	$isRoot = $model->session["isRoot"];
	?>

	<script>
		var folder = "<?php echo config("folder") ?>";
		var apiUrl = "<?php echo getApiUrl('') ?>";
		var controller = "<?php echo $this->controller ?>";
		var ctrl_uri = folder + controller + "/";
		var action = "<?php echo $this->action ?>";
		var choice = "<?php echo $choice_id ?>";
		var isRoot = <?php echo $isRoot ? 'true' : 'false' ?>;
		var permission = <?php echo $model->session["permission_control"] ?>;
		var isLoginRoot = <?php echo $model->session['login']['UserID'] == 'root' ? 'true' : 'false' ?>;
		var current_sub_emp = <?php echo json_encode($model->session["current_sub_emp"]) ?>;

		function callApi() {

			const request = function(method, url, data = {}, option = {}) {
				return new Promise(function(resolve, reject) {
					const defaultOption = {
						url,
						type: method,
						data,
						dataType: "json",
						success(res, status) {
							if (status === "success" && res.code === 0) {
								resolve(res)
							} else {
								res && res.data && alert(res.data)
								reject(res)
							}
						},
						error(xhr, errorType, error) {
							alert("系統錯誤，請稍後在試，或聯繫管理員。")
							reject(error)
						}
					}
					if (option.formData) {
						defaultOption["contentType"] = false
						defaultOption["processData"] = false
						defaultOption["cache"] = false
					}
					$.ajax(defaultOption)
				})
			}

			const requestWithoutCode = function(method, url, data = {}) {
				return new Promise(function(resolve, reject) {
					$.ajax({
						url,
						type: method,
						data,
						dataType: "json",
						success(res, status) {
							if (status === "success") {
								resolve(res)
							} else {
								reject(res)
							}
						},
						error(xhr, errorType, error) {
							alert("系統錯誤，請稍後在試或聯繫管理員。")
							reject(error)
						}
					})

				})
			}

			this.post = function(uri, data, option) {
				return request('POST', folder + uri, data, option)
			}

			this.get = function(uri, data) {
				return request('GET', folder + uri, data)
			}

			this.go = function(uri, data) {
				return requestWithoutCode('POST', apiUrl + uri, data)
			}
		}
		$.callApi = new callApi()
		$.updateSession = function() {
			return new Promise(resolve => {
				var $userSelect = $("#userSelet")
				var choice = $userSelect.val();
				$.post(folder + "index/changeUser", {
					choice,
				}, function(data) {
					resolve()
				});
			})
		}

		$(document).ready(function() {

			<?php
			if ($model->warning != "") {
				echo "alert('{$model->warning}');";
			}
			?>

			$("body").css("padding-top", $("#header").height());

			$("#page-content-wrapper").css("min-height", $(window).height() - $("#header").height());

			$("#main-content").css({
				"min-height": $("#page-content-wrapper").height() - $("#footer").height(),
				"padding-bottom": 15
			});

			$("#menu-toggle").click(function(e) {
				e.preventDefault();
				$("#wrapper").toggleClass("toggled");
			});

			if ($(".slideactive").length)
				$('#sidebar-wrapper').animate({
					scrollTop: $(".slideactive").offset().top - $(".slideactive").height() * 2
				}, 1000);

			$(".reboot_btn").confirm({
				text: "確定要重啟?",
				confirm: function(button) {
					location.href = folder + 'index/reboot'
				},
				post: true,
				confirmButton: "確定",
				cancelButton: "取消",
				confirmButtonClass: "btn-danger",
				cancelButtonClass: "btn-default"
			});

			$(".shotdown_btn").confirm({
				text: "確定要關機?",
				confirm: function(button) {
					location.href = folder + 'index/shotdown'
				},
				post: true,
				confirmButton: "確定",
				cancelButton: "取消",
				confirmButtonClass: "btn-danger",
				cancelButtonClass: "btn-default"
			});
		})
	</script>
	<style>
		[v-cloak] {
			display: none;
		}
	</style>
</head>

<body>

	<nav id="header" class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" id="drop_btn" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar eye-protector-processed" style="transition: background 0.3s ease; background-color: rgb(193, 230, 198);"></span>
					<span class="icon-bar eye-protector-processed" style="transition: background 0.3s ease; background-color: rgb(193, 230, 198);"></span>
					<span class="icon-bar eye-protector-processed" style="transition: background 0.3s ease; background-color: rgb(193, 230, 198);"></span>
				</button>
				<a class="navbar-brand" id="menu-toggle" style="cursor:pointer">ZH行銷</a>
			</div>
			<div id="navbar" class="navbar-collapse collapse" aria-expanded="false" style="height: 1px;">
				<div class="navbar-right">
					<div class="navbar-form navbar-text navbar-left text-primary">
						<?php
						echo Html::selector($model->empSelect);
						?>
					</div>
					<ul class="nav navbar-nav navbar-left">
						<li><a href="<?php echo config("folder") . "index/index" ?>">首頁</a></li>
						<!--<li><a href="<?php echo config("folder") . "index/service" ?>">服務</a></li>-->
						<?php if ($isRoot) { ?>
							<li><a class="shotdown_btn" href="javascript:;">關機</a><?php } ?>
							<!-- -->
							<?php if ($isRoot) { ?>
							<li><a class="reboot_btn" href="javascript:;">重啟</a><?php } ?>
							<li><a href="<?php echo config("folder") . "index/password" ?>">密碼</a></li>
							<li><a href="<?php echo config("folder") . "index/logout" ?>">登出</a></li>
					</ul>
				</div>

			</div>
		</div>
	</nav>
	<div id="wrapper">
		<div id="sidebar-wrapper">
			<?php echo $this->menu->CreateMenu($model->permission, $choice_id); ?>
		</div>
		<div id="page-content-wrapper">
			<div id="main-content" class="container-fluid">