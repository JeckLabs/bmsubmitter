<?$title = _e('наборы')?>
<?include './templates/header.php'?>
<style type='text/css'>
	.groupName {
		color: blue;
		text-decoration: underline;
		cursor: pointer;
	}
	.close {
		color: red;
		cursor: pointer;
	}
</style>

<div id='setting'>

<ul id='header'>
	<li id='logo'>
		<a href='./' title='<?=_e('Снова нажать кнопку. Снова и снова...')?>'><?=_e('Закладочник 2.0')?></a>
	</li>
	<li class='deployed'>
		<?=_e('Настройка')?>
		<ul>
			<li><a href='./settings.php?action=passwords'><?=_e('пароли')?></a></li>
			<li><a href='./settings.php?action=passwords_import' title='<?=_e('Импортировать пароли со старой версии или из программы-регистратора')?>'><?=_e('импорт')?></a></li>
			<li><a href='./settings.php?action=passwords_export' title='<?=_e('Сохранить пароли в отдельном файле')?>'><?=_e('экспорт')?></a></li>
			<li><?=_e('наборы')?></li>
			<li><a href='./settings.php?action=profiles' title='<?=_e('Группы аккаунтов')?>'><?=_e('профили')?></a></li>
			<li><a onclick='alert("<?=_e('Работает только из панели закладок ;)')?>");return false;' href='javascript:s=(d=document).selection;e=encodeURIComponent;t=d.title;r=d.getSelection?d.getSelection():s.createRange?s.createRange().text:t;d.location=&quot;<?=$bmUrl?>?u=&quot;+e(d.location.href)+&quot;&amp;t=&quot;+e(t)+&quot;&amp;d=&quot;+e(r);' title='<?=_e('Перетащите на панель закладок')?>' id='jeka'><?=_e('БМ')?></a></li>
		</ul>
	</li>
	<li><a href='./history.php' title='<?=_e('Уже добавленные закладки')?>'><?=_e('История')?></a></li>
	<li><a href='./faq.php' title='<?=_e('Или FAQ')?>'><?=_e('ЧАВО')?></a></li>
	<li><a href='http://ru.bmsubmitter.com/automate/' title='<?=_e('Если даже кнопку нажимать лень...')?>'  class='disabled'><?=_e('Автомат')?></a></li>
</ul>


<script lang='text/javascript'>
	var Modules = <?=json_encode($Modules)?>;
	$(function (){
		$('#groups .groupName').click(function (){
			$('#groupName').val($(this).text());
			list = eval($(this).attr('rel'));
			$('#modules input:checkbox').removeAttr('checked');
			for (key in list) {
				$('#module-'+list[key]).attr('checked', list[key]);
			}
		});
		$('#groups .close').click(function (){
			if (confirm('<?=_e('Вы действительно хотите сделать это?')?>')) {
				groupName = $(this).attr('rel');
				$.getJSON('./ajax/groups.php', {action: 'remove', group: groupName}, function (data){
					$('#groups li[rel='+data+']').remove();
				});
			}
		});
		$('#checkAll').click(function (){
			if ($(this).is(':checked')) {
				$('.module input:checkbox').attr('checked', true);
			} else {
				$('.module input:checkbox').removeAttr('checked');
			}
		});
	});
</script>

<form method='post' action='' id='bookmarkForm'>

<fieldset>
	<div id='modules'>
		<label> 
			<input type="checkbox" id='checkAll'/>
			<img src="./modules/icons/logo.png" />
			Сброс
		</label>
		<?foreach ($Modules as $moduleName => $info):?>
			<label class='module'><img src='./modules/icons/<?=$info['icon']?>' /> <input name='modules[]' type='checkbox' id='module-<?=$moduleName?>' value='<?=$moduleName?>' /> <?=$info['name']?></label>
		<?endforeach?>
	</div>
	<label class='nobr both'><?=_e('Название набора')?>
		<input id='groupName' name='groupName' type='text' value='' />
		<input id='SaveButton' type='submit' value='Сохранить' />
		<span id='loading'><?=_e('Загрузка...')?></span>
	</label>
</fieldset>

<div id='aside'>
	<ul id='groups'>
	<?foreach ($Groups->get() as $key => $groupName):?>
		<li rel='<?=$groupName?>'><span class='groupName' rel='<?=json_encode($Groups->get($groupName))?>'><?=$groupName?></span> <span rel='<?=$groupName?>' class='close'>&times;</span></li>
	<?endforeach?>
	</ul>
</div>

</form>
</div>

<?include './templates/footer.php'?>