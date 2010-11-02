<?$title = ''?>
<?include './templates/header.php'?>
<script lang='text/javascript'>
	var Modules = <?=json_encode($Modules)?>;
	
	var successCallback = function (data) {
		if (data.status == 'success') {
			$('#result-'+data.module).append(' <a href="'+data.url+'">'+data.login+'</a>');
		} else {
			$('#result-'+data.module).append(
				' <span class="red">'+data.msg+'</span> ('+data.login+') <span class="restart" rel="'+data.module+'">ещё раз</span>'
			);
			$('#result-'+data.module+' .restart').click(restart);
		}
	}
	
	var restart = function (){
		restartModule($(this).attr('rel'));
	}

	function restartModule(moduleName) {
		data = moduleData;
		data.action = 'start';
		data.module = moduleName;
		$('#result-'+moduleName).html(
			'<img src="./modules/icons/'+Modules[moduleName].icon+'" /> ' +
			Modules[moduleName].name
		);
		$.ajax({
			url: './ajax/modules.php', 
			data: data, 
			type: 'GET', 
			dataType: 'json', 
			success: successCallback,
			cache: false,
			complete: startModule
		});
	}
	
	function getId(data) {
		requestData = {
			action: 'getId',
			url: $('#bookmarkUrl').val(),
			title: $('#bookmarkTitle').val(),
			description: $('#bookmarkDescription').val(),
			tags: $('#bookmarkTags').val()
		};
		data = $.ajax({
			url: './ajax/modules.php', 
			data: requestData, 
			async: false,
			type: 'GET',
			cache: false
		});
		return data.responseText;
	}
	
	function startModule() {
		if (moduleName = modulesList.pop()) {
			//alert($('#result-'+moduleName).size() == 0);
			$('#results').append(
				'<div id="result-'+moduleName+'">' +
				'<img src="./modules/icons/'+Modules[moduleName].icon+'" /> ' +
				Modules[moduleName].name +
				'</div>'
			);
			data = moduleData;
			data.action = 'start';
			data.module = moduleName;
			data.profile = $('#profileSelect').val();
			$.ajax({
				url: './ajax/modules.php', 
				data: data, 
				type: 'GET', 
				dataType: 'json', 
				success: successCallback,
				cache: false,
				complete: startModule
			});
		} else {
			//alert('End of stack');
		}
	}
	
	$(function (){
		$('#profileSelect').change(function () {
			$('#modulesSelect').change();
		});
		$('#modulesSelect').change(function (){
			modulesNames = $(this).val().split(';');
			profile = $('#profileSelect').val();
			$('#modulesCheckboxes .moduleCheckbox').remove();
			for (var key in modulesNames) {
				moduleName = modulesNames[key];
				module = Modules[moduleName];
				if (module.passwordsCount[profile]) {
					$('#modulesCheckboxes').append(
						'<label class="moduleCheckbox" for="'+moduleName+'">' +
						'<input id="'+moduleName+'" name="modules[]" type="checkbox" value="'+moduleName+'" /> ' +
						'<img src="./modules/icons/'+module.icon+'" /> '+module.name +
						'</label>'
					);
				} else {
					$('#modulesCheckboxes').append(
						'<label class="moduleCheckbox disabled" for="'+moduleName+'">' +
						'<input id="'+moduleName+'" disabled="disabled" name="modules[]" type="checkbox" value="'+moduleName+'" /> ' +
						'<img src="./modules/icons/'+module.icon+'" /> '+module.name +
						'</label>'
					);
				}
			}
		});
		$('#modulesSelect').change();
		$('#checkAll').click(function (){
			if ($(this).is(':checked')) {
				$('#modulesCheckboxes input:checkbox').not(':disabled').attr('checked', true);
			} else {
				$('#modulesCheckboxes input:checkbox').removeAttr('checked');
			}
		});
		$('#oneThread').click(function (){
			if ($(this).is(':checked')) {
				oldThreads = $('#threads').val();
				$('#threads').val(1);
				$('#threads').attr('disabled', true);
			} else {
				$('#threads').val(oldThreads);
				$('#threads').removeAttr('disabled');
			}
		});
		$('#loadLastButton').click(function (){
			$.ajax({
				url: './ajax/history.php', 
				data: {action: 'loadLast'}, 
				type: 'GET', 
				dataType: 'json', 
				success: function (data) {
					$('#bookmarkUrl').val(data.url)
					$('#bookmarkTitle').val(data.name);
					$('#bookmarkDescription').val(data.description);
					$('#bookmarkTags').val(data.tags);
					$("#bookmarkForm").valid();
				},
				cache: false,
				complete: startModule
			});
			return false;
		});
		modulesList = [];
		$('#submitButton').click(function (){
			if (!$("#bookmarkForm").valid()) {
				return false;
			}
			checkboxes = $('.moduleCheckbox input:checked');
			if (checkboxes.length == 0) {
				alert('<?=_e('Вы должны выбрать хотя бы один модуль')?>');
				return false;
			}
			for (var i=0;i<checkboxes.length;i++) {
				modulesList.push($(checkboxes[i]).val());
			}
			var threads = $('#threads').val();
			if (modulesList.length < threads) {
				threads = modulesList.length;
			}
			
			$('#results').empty();
			id = getId();
			moduleData = {
				id: id
			}
			for (var i=0;i<threads;i++) {
				startModule();
			}
			return false;
		});
		
		$("#bookmarkForm").validate({
			rules: {
				bookmarkUrl: {
					required: true,
					url: true
				},
				bookmarkTitle: 'required',
			},
			messages: {
				bookmarkUrl: {
					required: "<?=_e('URL очень нужен, честно')?>",
					url: "<?=_e('Если вы не введете корректный URL скрипт сломается')?>",
				},
				bookmarkTitle: "<?=_e('Введите название закладки')?>"
			}
		});
	});
</script>

<ul id='header'>
	<li id='logo'><?=_e('Закладочник 2.0')?> </li>
	<li><a href='./settings.php' title='<?=_e('Букмарклет, профили, наборы...')?>'><?=_e('Настройка')?></a></li>
	<li><a href='./history.php' title='<?=_e('Уже добавленные закладки')?>'><?=_e('История')?></a></li>
	<li><a href='./faq.php' title='<?=_e('Или FAQ')?>'><?=_e('ЧАВО')?></a></li>
</ul>

<form id='bookmarkForm'>
	<fieldset id='mainForm'>
		<div class='formRow'>
			<label for='bookmarkUrl'><a href='#' id='loadLastButton' title='<?=_e('Заполнить прошлым')?>'>&larr;</a> <?=_e('Ссылка')?></label>
			<input id='bookmarkUrl' name='bookmarkUrl' type='text' value='<?=(isset($data['url']) ? $data['url'] : '')?>'/>
		</div>
		<div class='formRow'>
			<label for='bookmarkTitle'><?=_e('Заголовок')?></label>
			<input id='bookmarkTitle' name='bookmarkTitle' type='text' value='<?=(isset($data['name']) ? $data['name'] : '')?>'/>
		</div>
		<div class='formRow'>
			<label for='bookmarkDescription'><?=_e('Описание')?></label>
			<textarea id='bookmarkDescription' name='bookmarkDescription' cols='39' rows='5'><?=(isset($data['description']) ? $data['description'] : '')?></textarea>
		</div>
		<div class='formRow'>
			<label class='lat' title='<?=_e('Через запятую')?>'><?=_e('Метки')?></label>
			<input id='bookmarkTags' type='text' name='bookmarkTags' value=''/>
		</div>
		<label>
			<?=_e('Число потоков')?>
			<input id='threads' type='text' value='20'/>
		</label>
		<label class='nobr' title='<?=_e('Если у вас медленное соединение')?>'>
			<input id='oneThread' type='checkbox' />
			<?=_e('последовательно')?>
		</label>
		<span id='loading'><?=_e('Загрузка...')?></span>
		<button id='submitButton'><?=_e('Пуск')?></button>
		<div class='clear'></div>
		<div id='results'>
		</div>
	</fieldset>

	<div id='aside'>
		<label title='<?=_e('Выбор профиля (набора аккаунтов)')?>' class='nobr'>
			<?=_e('Профиль')?>
			<select id='profileSelect'>
				<?foreach ($Profiles->get() as $profile):?>
					<option value='<?=$profile?>'><?=$Profiles->get($profile)?></option>
				<?endforeach?>
			</select>
		</label>
		<label title='<?=_e('Выбор набора')?>' class='nobr'>
			<?=_e('Набор')?>
			<select id='modulesSelect'>
				<?foreach ($Groups->get() as $key => $groupName):?>
					<option value='<?=implode(';', $Groups->get($groupName))?>'><?=$groupName?></option>
				<?endforeach?>
			</select>
		</label>
		<div id='modulesCheckboxes'>
			<label for="checkAll"> 
				<input id="checkAll" type="checkbox" />
				<img src="./modules/icons/logo.png" />
				<?=_e('Выбрать все')?>
			</label>
			<div class='clear'></div>
		</div>
		<div class='clear'></div>
		<?if ($lastVersion > VERSION):?>
		<p class='red'>Ваша версия закладочника устарела, пожалуйста <a href='http://jeck.ru/download/'>обновитесь</a>.</p>
		<?endif?>
	</div>
</form>
<?include './templates/footer.php'?>