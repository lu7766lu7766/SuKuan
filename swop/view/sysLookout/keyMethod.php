<?php
$this->partialView($top_view_path);
?>
<h3 id="title"><?php echo $this->menu->currentName ?></h3>

<div class="table-responsive">
	<table class="table table-v">
		<tbody>
			<tr>
				<td>自動撥號指令</td>
				<td>
				</td>
			</tr>
			<tr>
				<td>*11</td>
				<td>
					分機輸入此指令，暫停接聽自動撥號的來電
				</td>
			</tr>
			<tr>
				<td>*12</td>
				<td>
					分機輸入此指令，啟用接聽自動撥號的來電
				</td>
			</tr>
			<tr>
				<td>*14</td>
				<td>
					分機輸入此指令，經由密碼驗證後，即啟動自動撥號
				</td>
			</tr>

			<tr>
				<td>分機前言指令</td>
				<td>
				</td>
			</tr>
			<tr>
				<td>*21</td>
				<td>
					錄製分機前言，通知音後開始錄製，錄製完成請輸入＃字鍵
				</td>
			</tr>
			<tr>
				<td>*22</td>
				<td>
					聽取分機前言
				</td>
			</tr>
			<tr>
				<td>*23</td>
				<td>
					分機輸入此指令，暫停分機前言功能
				</td>
			</tr>
			<tr>
				<td>*24</td>
				<td>
					分機輸入此指令，啟用分機前言功能
				</td>
			</tr>

			<tr>
				<td>三方通話指令</td>
				<td>
				</td>
			</tr>
			<tr>
				<td>*3 + 分機號</td>
				<td>
					對該分機啟用監聽模式,無法與該分機通話,欲切換模式<br>
					輸入3切換為監聽模式, 輸入 4 切換為單向模式, 輸入5 切換為 三方模式
				</td>
			</tr>
			<tr>
				<td>*4 + 分機號</td>
				<td>
					對該分機啟用單向模式 ,可以與該分機單向通話,欲切換模式<br>
					輸入3切換為監聽模式, 輸入 4 切換為單向模式, 輸入5 切換為 三方模式
				</td>
			</tr>
			<tr>
				<td>*5 + 分機號</td>
				<td>
					對該分機啟用三方模式,可以參與該通話形成三方通話,欲切換模式<br>
					輸入3切換為監聽模式, 輸入 4 切換為單向模式, 輸入5 切換為 三方模式
				</td>
			</tr>
		</tbody>
	</table>
</div>

<?php
$this->partialView($bottom_view_path);
?>