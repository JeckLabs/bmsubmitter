<?$title = 'История'?>
<?include './templates/header.php'?>
<ul id='header'>
	<li id='logo'><a href='./' title='<?=_e('Снова нажать кнопку. Снова и снова...')?>'><?=_e('Закладочник')?> <?=VERSION?></a></li>
	<li><a href='./settings.php' title='<?=_e('Букмарклет, профили, наборы...')?>'><?=_e('Настройка')?></a></li>
	<li><?=_e('История')?></li>
	<li><a href='./faq.php' title='<?=_e('Или FAQ')?>'><?=_e('ЧАВО')?></a></li>
</ul>
<script lang='text/javascript'>
	$(function (){
		$('#emptyButton').click(function (){
			if (confirm('<?=_e('Вы уверены что хотите очистить историю?')?>')) {
				$.getJSON('./ajax/history.php', {action: 'empty'}, function (data){
					$('#history').empty();
					$('#hiddenNote').show();
				});
			}
		});
		$('.listToggle').click(function () {
			var key = $(this).attr('href');
			$(key).toggle();
			return false;
		});
	});
</script>
<?if ($History->count() > 0):?>
<p id='hiddenNote' class='hidden'><?=_e('Здесь ничего нет, совсем ничего.')?></p>
<div id='history'>
	<dl id='historyList'>
		<?foreach ($History->get() as $key => $id):?>
		<?$historyData = $History->get($id)?>
		<dt><a  href='./index.php?historyId=<?=$id?>'><?=$historyData['data']['name']?></a></dt>
		<dd>
			<p>
			<?=Helpers::declineNumber(count($historyData['modules']), array('закладка', 'закладки', 'закладок'))?>:
			<?foreach ($historyData['modules'] as $moduleName => $moduleData):?>
			<a href='<?=$moduleData['url']?>'><img  src='./modules/icons/<?=$Modules[$moduleName]['icon']?>'/></a>
			<?endforeach?>
			</p>
			<p>
				<a href='#linksList-<?=$key?>' class='listToggle'>Список</a>
			</p>
			<p class='linksList' id='linksList-<?=$key?>'>
				<?foreach ($historyData['modules'] as $moduleName => $moduleData):?>
				<a href='<?=$moduleData['url']?>'><?=$moduleData['url']?></a><br/>
				<?endforeach?>
			</p>
		</dd>
		<?endforeach?>
	</dl>
	<span id='loading'><?=_e('Загрузка...')?></span>
	<button id='emptyButton'><?=_e('Очистить историю')?></button>
</div>
<?else:?>
	<p><?=_e('Здесь ничего нет, совсем ничего.')?></p>
<?endif;?>
<?include './templates/footer.php'?>