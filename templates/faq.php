<?$title = _e('FAQ')?>
<?include './templates/header.php'?>
<ul id='header'>
	<li id='logo'><a href='./' title='<?=_e('Снова нажать кнопку. Снова и снова...')?>'><?=_e('Закладочник')?> <?=VERSION?></a></li>
	<li><a href='./settings.php' title='<?=_e('Букмарклет, профили, наборы...')?>'><?=_e('Настройка')?></a></li>
	<li><a href='./history.php' title='<?=_e('Уже добавленные закладки')?>'><?=_e('История')?></a></li>
	<li><?=_e('ЧАВО')?></li>
</ul>

<div id='<?=_e('faq')?>'>
	<dl>
	<?=_e('Пока ничего нет')?>
	</dl>
</div>
<?include './templates/footer.php'?>