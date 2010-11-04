<?$title = _e('настройка паролей')?>
<?include './templates/header.php'?>

<script lang='text/javascript'>
	var Modules = <?=json_encode($Modules)?>;
	
	$(function (){
		var reloadModules = function (profile){
			for (var key in Modules) {
				$('option[value='+key+']').text(
					Modules[key].name + 
					' ('+Modules[key].passwordsCount[profile]+')'
				);
			}
		}
		var loadPasswords = function (moduleName, profile){
			$.getJSON('./ajax/passwords.php', {action: 'get', module: moduleName, profile: profile}, function (data){
				//$('#passwordsArea').empty();
				$('#passwordsArea').val(data);
			});
		}
		$('#profileSelect').change(function (){
			$('#modulesSelect').change();
		});
		$('#modulesSelect').change(function (){
			moduleName = $(this).val();
			$('#moduleLink').html(
				'<a href="'+Modules[moduleName].registrationUrl+'">' +
				'<img src="./modules/icons/'+Modules[moduleName].icon+'" /> ' +
				Modules[moduleName].name +
				'</a>'
			);
			var profile = $('#profileSelect').val();
			loadPasswords(moduleName, profile);
			reloadModules(profile);
		});
		$('#SaveButton').click(function () {
			var moduleName = $('#modulesSelect').val();
			var data = $('#passwordsArea').val();
			var profile = $('#profileSelect').val();
			$.getJSON('./ajax/passwords.php', {action: 'set', module: moduleName, profile: profile, data: data}, function (data){
				Modules[moduleName].passwordsCount[profile] = data.length;
				reloadModules(profile);
				loadPasswords(moduleName, profile);
			});
		});
		$('#profileSelect').change();
		$('#modulesSelect').change();
	});
</script>

<div id='setting'>

<ul id='header'>
	<li id='logo'>
		<a href='./' title='<?=_e('Снова нажать кнопку. Снова и снова...')?>'><?=_e('Закладочник')?> <?=VERSION?></a>
	</li>
	<li class='deployed'>
		<?=_e('Настройка')?>
		<ul>
			<li><?=_e('пароли')?></li>
			<li><a href='./settings.php?action=passwords_export' title='<?=_e('Сохранить пароли в отдельном файле')?>'><?=_e('экспорт')?></a></li>
			<li><a href='./settings.php?action=passwords_import' title='<?=_e('Импортировать пароли со старой версии или из программы-регистратора')?>'><?=_e('импорт')?></a></li>
			<li><a href='./settings.php?action=groups' title='<?=_e('Наборы модулей')?>'><?=_e('наборы')?></a></li>
			<li><a href='./settings.php?action=profiles' title='<?=_e('Группы аккаунтов')?>'><?=_e('профили')?></a></li>
			<li><a onclick='alert("<?=_e('Работает только из панели закладок ;)')?>");return false;' href='javascript:s=(d=document).selection;e=encodeURIComponent;t=d.title;r=d.getSelection?d.getSelection():s.createRange?s.createRange().text:t;d.location=&quot;<?=$bmUrl?>?u=&quot;+e(d.location.href)+&quot;&amp;t=&quot;+e(t)+&quot;&amp;d=&quot;+e(r);' title='<?=_e('Перетащите на панель закладок')?>' id='jeka'><?=_e('БМ')?></a></li>
		</ul>
	</li>
	<li><a href='./history.php' title='<?=_e('Уже добавленные закладки')?>'><?=_e('История')?></a></li>
	<li><a href='./faq.php' title='<?=_e('Или FAQ')?>'><?=_e('ЧАВО')?></a></li>
</ul>

<fieldset>
	<label title='<?=_e('Выбор профиля (набора аккаунтов)')?>' class='lat nobr'>
		<select id='profileSelect'>
		<?foreach ($Profiles->get() as $profile):?>
			<option value='<?=$profile?>'><?=$Profiles->get($profile)?></option>
		<?endforeach?>
		</select>
		&nbsp; &larr; <?=_e('профиль')?>
	</label>
	<label class='lat' >
		<select id='modulesSelect' id='modulesSelect'>
		<?foreach ($Groups->get() as $groupName):?>
			<optgroup label='<?=$groupName?>'>
				<?foreach ($Groups->get($groupName) as $moduleName):?>
				<option value='<?=$moduleName?>'><?=$Modules[$moduleName]['name']?></option>
				<?endforeach?>
			</optgroup>
		<?endforeach?>
		</select>
		<span id='moduleLink'></span>
	</label>
	<label class='lat'><?=_e('Логин;пароль, login;password...')?>
		<textarea id='passwordsArea' cols='39' rows='6'></textarea>
	</label>
	<button id='SaveButton'><?=_e('Сохранить')?></button>
	<div id='loading'><?=_e('Загрузка...')?></div>
</fieldset>

</div>
<?include './templates/footer.php'?>