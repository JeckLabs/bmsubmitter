<?$title = _e('импорт паролей')?>
<?include './templates/header.php'?>

<script lang='text/javascript'>
	$(function (){
		$('#importButton').click(function () {
			var profile = $('#profileSelect').val();
			var data = $('#importArea').val();
			$.ajax({
				url: './ajax/passwords.php?action=import', 
				data: {data: data, profile: profile}, 
				type: 'POST', 
				dataType: 'json', 
				success: function (data){
					alert('<?=_e('Данные импортированы')?>');
					$('#importArea').val(' ');
				},
				cache: false
			});
			return false;
		});
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
			<li><a href='./settings.php?action=passwords'><?=_e('пароли')?></a></li>
			<li><?=_e('импорт')?></li>
			<li><a href='./settings.php?action=passwords_export' title='<?=_e('Сохранить пароли в отдельном файле')?>'><?=_e('экспорт')?></a></li>
			<li><a href='./settings.php?action=groups' title='<?=_e('Наборы модулей')?>'><?=_e('наборы')?></a></li>
			<li><a href='./settings.php?action=profiles' title='<?=_e('Группы аккаунтов')?>'><?=_e('профили')?></a></li>
			<li><a onclick='alert("<?=_e('Работает только из панели закладок ;)')?>");return false;' href='javascript:s=(d=document).selection;e=encodeURIComponent;t=d.title;r=d.getSelection?d.getSelection():s.createRange?s.createRange().text:t;d.location=&quot;<?=$bmUrl?>?u=&quot;+e(d.location.href)+&quot;&amp;t=&quot;+e(t)+&quot;&amp;d=&quot;+e(r);' title='<?=_e('Перетащите на панель закладок')?>' id='jeka'><?=_e('БМ')?></a></li>
		</ul>
	</li>
	<li><a href='./history.php' title='<?=_e('Уже добавленные закладки')?>'><?=_e('История')?></a></li>
	<li><a href='./faq.php' title='<?=_e('Или FAQ')?>'><?=_e('ЧАВО')?></a></li>
</ul>


<form id='bookmarkForm'>

<fieldset>
	<label class='nobr lat'>
		<?=_e('Имя профиля (для старого формата)')?>
		<select id='profileSelect'>
		<?foreach ($Profiles->get() as $profile):?>
			<option value='<?=$profile?>'><?=$Profiles->get($profile)?></option>
		<?endforeach?>
		</select>
	</label>

	<label class='lat'>
		<?=_e('Список логинов;паролей (любая версия BmSubmitter)')?>
		<textarea id='importArea' cols='39' rows='6'></textarea>
	</label>
	<span id='loading'><?=_e('Загрузка...')?></span>
	<button id='importButton'><?=_e('Импортировать')?></button>
</fieldset>

</form>
</div>

<?include './templates/footer.php'?>