<?$title = 'История'?>
<?include './templates/header.php'?>
<ul id='header'>
	<li id='logo'><a href='./' title='<?=_e('Снова нажать кнопку. Снова и снова...')?>'><?=_e('Закладочник 2.0')?></a></li>
	<li><a href='./settings.php' title='<?=_e('Букмарклет, профили, наборы...')?>'><?=_e('Настройка')?></a></li>
	<li><?=_e('История')?></li>
	<li><a href='./faq.php' title='<?=_e('Или FAQ')?>'><?=_e('ЧАВО')?></a></li>
	<li><a href='http://ru.bmsubmitter.com/automate/' title='<?=_e('Если даже кнопку нажимать лень...')?>'  class='disabled'><?=_e('Автомат')?></a></li>
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
	});
</script>
<?if ($History->count() > 0):?>
<p id='hiddenNote' class='hidden'><?=_e('Здесь ничего нет, совсем ничего.')?></p>
<div id='history'>
	<dl id='historyList'>
		<?foreach ($History->get() as $id):?>
		<?$historyData = $History->get($id)?>
		<dt><a  href='./index.php?historyId=<?=$id?>'><?=$historyData['data']['name']?></a></dt>
		<dd><?=Helpers::declineNumber(count($historyData['modules']), array('закладка', 'закладки', 'закладок'))?>:
			<?foreach ($historyData['modules'] as $moduleName => $moduleData):?>
			<a href='<?=$moduleData['url']?>'><img  src='./modules/icons/<?=$Modules[$moduleName]['icon']?>'/></a>
			<?endforeach?>
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