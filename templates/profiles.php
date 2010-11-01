<?$title = 'профили'?>
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
			<li><a href='./settings.php?action=groups' title='<?=_e('Наборы модулей')?>'><?=_e('наборы')?></a></li>
			<li><?=_e('профили')?></li>
			<li><a onclick='alert("<?=_e('Работает только из панели закладок ;)')?>");return false;' href='javascript:s=(d=document).selection;e=encodeURIComponent;t=d.title;r=d.getSelection?d.getSelection():s.createRange?s.createRange().text:t;d.location=&quot;<?=$bmUrl?>?u=&quot;+e(d.location.href)+&quot;&amp;t=&quot;+e(t)+&quot;&amp;d=&quot;+e(r);' title='<?=_e('Перетащите на панель закладок')?>' id='jeka'><?=_e('БМ')?></a></li>
		</ul>
	</li>
	<li><a href='./history.php' title='<?=_e('Уже добавленные закладки')?>'><?=_e('История')?></a></li>
	<li><a href='./faq.php' title='<?=_e('Или FAQ')?>'><?=_e('ЧАВО')?></a></li>
</ul>


<script lang='text/javascript'>
	var Modules = <?=json_encode($Modules)?>;
	$(function (){
		$('#profiles .close').click(function (){
			if (confirm('<?=_e('Вы действительно хотите удалить профиль?')?>')) {
				profile = $(this).attr('rel');
				$.getJSON('./ajax/profiles.php', {action: 'remove', profile: profile}, function (profile){
					$('#profile-'+profile).remove();
				});
			}
		});
	});
</script>

<form method='post' action='' id='bookmarkForm'>

<fieldset>
	<label class='nobr both'><?=_e('Новый профиль')?>
		<input id='profileName' name='profileName' type='text' value='' />
		<input id='SaveButton' type='submit' value='<?=_e('Сохранить')?>' />
		<span id='loading'><?=_e('Загрузка...')?></span>
	</label>
</fieldset>

<div id='aside'>
	<ul id='profiles'>
	<?foreach ($Profiles->get() as $profile):?>
		<li id='profile-<?=$profile?>'><span class='profileName'><?=$Profiles->get($profile)?></span> <span rel='<?=$profile?>' class='close'>&times;</span></li>
	<?endforeach?>
	</ul>
</div>

</form>
</div>

<?include './templates/footer.php'?>