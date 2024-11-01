<?php

class ImportYml_Categories
{
	private $projectID;
	public function __construct($projectID)
	{
		$this->projectID = $projectID;
	}
	
	/**
	 * Возвращает категории проекта.
	 * @return stdClass
	 */
	public function getCategories()
	{
		global $wpdb;
		$results = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}importyml_category` where `project_id` = '{$this->projectID}'");
		return $results;
	}
  
  public function getCategoriesId()
	{
		global $wpdb;
		$results = $wpdb->get_results("SELECT cat.id FROM `{$wpdb->prefix}importyml_category` as cat where `project_id` = '{$this->projectID}'");
		return $results;
	}
	
	public function getCategoryById($id)
	{
		global $wpdb;
		$results = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}importyml_category` where `project_id` = '{$this->projectID}' and `id` = '{$id}'");
		return $results;
	}
	
	
	private function setParents()
	{
		global $wpdb;
		foreach($this->getCategories() as $category)
		{
			if ($category->parent_id != 0)
			{
				$wp_parentID = $this->getWpID($category->parent_id);
				$wpdb->query("update `{$wpdb->prefix}term_taxonomy` set `parent` = '{$wp_parentID}' where `term_id` = '{$category->affiliate_id}'");
			}
		}	
	}
	
	public function getWpID($xmlID)
	{
		global $wpdb;
		return $wpdb->get_var("SELECT `affiliate_id` FROM `{$wpdb->prefix}importyml_category` WHERE `id` = '{$xmlID}'");
	}
	
	public function generateCategories()
	{
		global $wpdb;
		srand();
		foreach($this->getCategories() as $category)
		{
			if ($category->affiliate_id == 0)
			{
				// Создаем новую term
				$wpdb->insert("{$wpdb->prefix}terms",
				array(
						'name' => $category->category_name,
						'slug' => rand(1000,10000).'_'.ImportYml_Yml::translit($category->category_name)
				),
				array('%s','%s',));
	
				// Запоминаем ID нового термса
				$termID = $wpdb->insert_id;
				// Указываем WordPress, что это категория
				$wpdb->insert("{$wpdb->prefix}term_taxonomy",
				array('term_id' => $termID,'taxonomy' => "category"),
				array('%d','%s')
				);
	
				// Обновляем информацию о связке в таблицу категорий проекта
				$wpdb->update(
						"{$wpdb->prefix}importyml_category",
						array('affiliate_id' => $termID),
						array('id' => $category->id),
						array('%d'),
						array('%s')
				);
        $wpdb->update(
						"{$wpdb->prefix}importyml_offer",
						array('affiliate_cat_id' => $termID),
						array('offer_category' => $category->id),
						array('%d'),
						array('%s')
				);
			}
		}
		
		$this->setParents();
	}
  
  public function generateCategoryById($cat_id)
	{
		global $wpdb;
		srand();
    $category = $this->getCategoryById($cat_id); 
    if ($category->affiliate_id == 0)
		{
				// Создаем новую term
				$wpdb->insert("{$wpdb->prefix}terms",
				array(
						'name' => $category->category_name,
						'slug' => rand(1000,10000).'_'.ImportYml_Yml::translit($category->category_name)
				),
				array('%s','%s',));
	
				// Запоминаем ID нового термса
				$termID = $wpdb->insert_id;
				// Указываем WordPress, что это категория
				$wpdb->insert("{$wpdb->prefix}term_taxonomy",
				array('term_id' => $termID,'taxonomy' => "category"),
				array('%d','%s')
				);
	
				// Обновляем информацию о связке в таблицу категорий проекта
				$wpdb->update(
						"{$wpdb->prefix}importyml_category",
						array('affiliate_id' => $termID),
						array('id' => $category->id),
						array('%d'),
						array('%s')
				);
        $wpdb->update(
						"{$wpdb->prefix}importyml_offer",
						array('affiliate_cat_id' => $termID),
						array('offer_category' => $category->id),
						array('%d'),
						array('%s')
				);
		} 
	}
	
	/**
	 * Перерасчет информации о категориях
	 */
	public function recalculate()
	{
		$this->setParents();
	}
}