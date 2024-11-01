<?php

class ImportYml_AnalizePackage {
	
	public function __construct() {		
		global $wpdb;
		$analize_result = array();
		$not_analized = array();
		$all_projects_list = $wpdb->get_results("SELECT `id` FROM `{$wpdb->prefix}importyml_project` where 1");
		if (!empty($all_projects_list)){
			foreach($all_projects_list as $pr_id){
				$result = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}importyml_category` where `project_id` = '{$pr_id->id}'");
				if (empty($result)){
					$not_analized[] = $pr_id->id;
				}
			}
		}else {
			$analize_result['no_projects'] = 1;
		}
		
		if(count($not_analized)>0) {
			$analize_result['projects_id'] = json_encode($not_analized);
		}else {
			$analize_result['all_analized'] = 1;
		}
		echo json_encode($analize_result);
	}

}
