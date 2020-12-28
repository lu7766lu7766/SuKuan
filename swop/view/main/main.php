<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
<?php
echo Bundle::$allLink;
Bundle::addLink("default");
$top_view_path = "";
$this->partialView($top_view_path);
?>

<body class="container-fluid" style="background-color: #666">
	<div class="row">
		<div class="login-box col-xs-12 col-md-offset-4 col-md-4" style="border-radius: 10px;">
			<div class="login-logo">
				<div class="h2 form-signin-heading">電訪系統</ㄎ>
					<!--<img src="/admin/upload/company/images/logo.png" alt="KenKo" s="" clear="" beauty'=""> -->
				</div>
				<p class="login-box-msg" style="letter-spacing:3px;"><b></b></p>
				<!--<form action="<?php echo $this->submit_link; ?>" method="post">-->
				<?php echo Html::form() ?>
				<div class="form-group has-feedback">
					<input class="form-control eye-protector-processed" placeholder="輸入帳號" name="username" type="text" style="border-color: rgba(0, 0, 0, 0.34902);">
					<span class="glyphicon glyphicon-user form-control-feedback"></span>
				</div>
				<div class="form-group has-feedback">
					<input class="form-control eye-protector-processed" placeholder="輸入密碼" name="password" type="password" style="border-color: rgba(0, 0, 0, 0.34902);">
					<span class="glyphicon glyphicon-lock form-control-feedback"></span>
				</div>
				<div class="form-group row">
					<span class="col-xs-5 control-label">
						<label class="sr-only">驗證圖片</label>
						<img width="100%" id='verification_img' alt="點擊換圖" title="點擊換圖" src="<?php echo $base["folder"]; ?>createImg.php" onclick="javascript:this.src='<?php echo $base["folder"]; ?>createImg.php?n='+Math.random();" width="130" height="50" />
					</span>
					<div class="col-xs-7">
						<input style="height:34px;border-color:#DCDDD" class="form-control" placeholder="輸入驗證碼" name="captcha" type="text">
					</div>
				</div>

				<div class="row">
					<div class="col-xs-12">
						<div class="col-md-offset-4 col-md-4 col-xs-offset-4 col-xs-4">
							<input type="submit" class="btn btn-primary btn-block" value="登入">
						</div>
					</div>
				</div>
				<?php echo Html::formEnd() ?>

				<div class="form-group">
					<div class="text-left">
						<div class="errorMessage">
							<?php echo $model->errorMsg ?>
						</div>
					</div>
				</div>

				<div class="text-left <?php echo $model->bulletinBoard->Status == "0" ? 'hide' : '' ?>">
					<hr style="height:1px;border-width:0;color:gray;background-color:gray">
					<div class="h3">公告</div>
					<div class="h4"><?php echo nl2br($model->bulletinBoard->Content) ?></div>
				</div>

			</div>

			<?php
			$bottom_view_path = "";
			$this->partialView($bottom_view_path);
			?>
</body>

</html>