<?$title = _e('вход')?>
<?include './templates/header.php'?>
<form method='post' action='./login.php'>
	<fieldset>
		<label class='lat'><?=_e('Пароль')?>
			<input id='password' name='password' type='text' value=''/>
		</label>
		<button id='submitButton'><?=_e('Вход')?></button>
	</fieldset>
</form>
<?include './templates/footer.php'?>