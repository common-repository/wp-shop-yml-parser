<?php 
$projectName = isset($this->project) ? $this->project->getName() : "";
$projectUrl = isset($this->project) ? $this->project->getUrl() : "";
$projectCountOffers = isset($this->project) ? $this->project->count_offers() : "";
$projectCountCats = isset($this->project) ? $this->project->count_cats() : "";
$importTemplate = isset($this->project) ? $this->project->getTemplate() : "";
?>

<div class="wrap">
	<div id="icon-tools" class="icon32"><br/></div>
	<h2>Импорт и синхронизация с источниками YML</h2>
	<?php if (isset($this->project)):?>
	<h3 class="title">Редактирование проекта</h3>
	<?php else:?>
	<h3 class="title">Добавить новый проект</h3>
	<?php endif;?>
	<form action="tools.php?page=importyml_main" enctype="multipart/form-data" method="post" name="yi-projectForm">
		<!-- Скрытое поле для сохранения шаблона импорта -->
		<input type="hidden" name="yi-import-template"/>
		<input type="hidden" name="import-yml-project" value="<?php echo $_GET['project'];?>"/>
		<table>
			<tr>
				<td>Имя проекта:</td>
				<td><input type="text" name="yi-projectName" value="<?php echo $projectName;?>" style="width:400px"/></td>
			</tr>
			<tr>
				<td>Адрес URL:</td>
				<td><input type="text" name="yi-projectYML" value="<?php echo $projectUrl;?>" style="width:400px"<?php if (isset($this->project)) echo " disabled"?>/></td>
			</tr>
			<tr>
				<td>Файл на сервере:</td>
				<td><input type="checkbox" name="yi-projectYML_server" /></td>
			</tr>
			<tr><td colspan="2"  style="color: green;"><em>Путь указывать относительно папки где хранятся yml файлы плагина</em></td></tr>
			<tr>
				<td>Локальный файл:</td>
				<td><input type="file" name="yi-projectYML_file" style="width:400px"/></td>
			</tr>
		</table>
	</form>
	<div style="margin-top:30px">
		<input type="button" value="Сохранить" class="button" id="iySaveProject"/>
		<input type="button" value="Анализировать" class="button" id="yiAnalizeProject" data-site="<?php echo ImportYml_url; ?>"/>
		<input type="button" value="Импортировать" class="button" id="yiShowImportView"/>
		<!--<input type="button" value="Сопоставить" class="button" id="yiCollateProject"/>-->
		<input type="button" value="Удалить контент" class="button" id="yiDeleteProjectContent"/>
    <input type="button" value="Клонировать проект" class="button" id="yiCloneProject" data-site="<?php echo ImportYml_url; ?>"/>
	</div>
	<div class="feed_info col50">
		<p class="offers">Офферов в YML: <span><?php if (isset($this->project)){ echo $projectCountOffers;}else{echo '0';}?></span></p>
		<p class="cats">Рубрик в YML: <span><?php if (isset($this->project)) {echo $projectCountCats;}else{echo '0';}?></span></p>
		<input type="button" value="Очистить" class="button" id="yiDeleteBeforeParsing"/>
	</div>
	<div class="create_project_by_cats col50">
		<input type="button" value="Клонировать проект пакетами по" class="button" id="yiCreateByParentCat"/> 
    <input type="text" class="clone_cat_quont" name="yi-clone_cat_quont" size="3" value="10"/> категорий<br/>
    <div class="clear"></div>
		<input type="button" value="Клонировать проект по конечным категориям" class="button" id="yiCreateByEndCat"/>
	</div>
<br/><br/><br/>
<table class="widefat">
<tr>
	<th>Дата время</th>
	<th>Обновлено цен</th>
	<th>Выключено</th>
	<th>Включено</th>
	<th>Новые</th>
</tr>
<?php
if (isset($this->changed)){
foreach($this->changed as $change) {
	echo "<tr>";
	echo "<td>{$change->changed}</td><td>{$change->updated_price}</td><td>{$change->updated_off}</td><td>{$change->updated_on}</td><td>{$change->updated_new}</td>";
	echo "</tr>";
}
}
echo "</tr>";
?>
</table>

	<div id="importView" style="display:none" class="yi-actionView">
		<div style="width:630;float:left;">
			<h3>Шаблон</h3>
			<textarea name="importTemplate" style="width:600px;height:300px">
			<?php echo $importTemplate; ?>
			</textarea>
			<div style="clear:both;"></div>
			<a href="http://wp-shop.ru/yandex-xml-parser/conf/" target="_blank">Документация по конструктору шаблонов</a>
      <div style="clear:both;"></div>
			<!--<div>
				<div id="createPostBar"></div>
				<div id="createPostBarMessage" style="font-size:20px;margin:10px 0;">Обработано <span class="howmany">0</span> из <span class="total">0</span></div>
			</div>-->
     <p>Отправлять на сервер пакеты по <input type="text" class="package_size" name="yi-package_size" size="3" value="100"/> заданий</p>
     <div style="clear:both;"></div>
			<input type="button" value="Импортировать" class="button" id="iyImportProject"/>
     <div class="console"></div>
		</div>
	</div>

</div>

