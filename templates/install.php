<?$title = _e('установка')?>
<?include './templates/header.php'?>
<?if (count($errors) > 0):?>
	<p><?=_e('Возникли ошибки:')?></p>
	<ul>
	<?foreach ($errors as $error):?>
		<li class='red'><?=$error?></li>
	<?endforeach?>
	</ul>
	<?if ($accessError):?>
	<p>
		<?=_e('Инструкция с правами.')?>
	</p>
	<?endif;?>
<?else:?>
<p>
<?=_e('Все готово для установки пожалуйста введите желаемый пароль')?>
</p>
<form method='post' action='./install.php'>
	<fieldset>
		<label>
			<?=_e('Пароль:')?>
			<input type='text' name='password' value='<?=(isset($_POST['password']) ? htmlspecialchars($_POST['password']) : '')?>'/>
		</label>
		<input type='submit' name='' value='<?=_e('Установить')?>'/>
	</fieldset>
</form>
<?endif?>
<?include './templates/footer.php'?>