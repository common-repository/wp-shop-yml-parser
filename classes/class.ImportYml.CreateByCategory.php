<?php

class ImportYml_CreateByCategory {
	
	public function __construct($project_id,$quont) {		
    $result = array();
		$project = new ImportYml_Project($project_id);
		
		$path = ImportYml_dir_ymls . "/{$project->getFile()}";
		ImportYml_Yml::xmlrefactor($path);
		$xml= simplexml_load_file($path);
		if(FALSE === $xml) {
			$result['error']=true;
		}else{
			$data = $xml->shop->categories;
			$cats_for_project = array();
			foreach($data->category as $category) {
				$ids = array();
				foreach($category->attributes() as $attr){
					$ids[$attr->getName()] = (string)$attr;
				}
				$cats_for_project[] = $ids['id'] ;
			}			
		}
		
		$pr_name = "subproject of {$project->getName()}";
		$file = $project->getFile();
		$url = $project->getUrl();
		$temp = $project->getTemplate();
		$local = $project->checkLocal();
		
		
		
		$opts = array();
		preg_match("/(.*)<yml_options>(.*)<\/yml_options>/s",$temp,$opts);
		
		if (count($opts) > 1){//est nabor predustanovlennih nastroek
			$full_options = $opts[2];
			$setting = explode(";", $full_options);
			$option_list = array();
			
			foreach($setting as $option) {
				$ar_opt = explode("=", $option);
				$key = $ar_opt[0];
				$value = $ar_opt[1];
				$option_list[$key] = $value;
			}
			
			
			if (array_key_exists('cat_ar', $option_list)) {
				unset ($option_list['cat_ar']);
			}
			
			if (count($option_list)>0) {
				$options_list = '';
				foreach($option_list as $name => $value) {
					$options_list .= $name.'='.$value.';';
				}
			}
			$temp_for_base = $opts[1].'<yml_options>'.$options_list;
		}else {
			$temp_for_base = $temp.'<yml_options>';
		}
		
    $project_count = 0;
		if(isset($quont)&&$quont!=''){
			if(isset($cats_for_project)&&count($cats_for_project)>0){
        $quont = $quont*1;
        $cats_groups = array_chunk($cats_for_project,$quont);
				foreach($cats_groups as $categs) {
          $cat_str = implode(',',$categs);
					$temp_for_base_cat = $temp_for_base.'cat_ar='.$cat_str.';</yml_options>';
          $cat_str_for_name = substr($cat_str, 0,50);
          $pr_name_cat = $pr_name.' by categories:'.$cat_str_for_name;
					$this->create_project($pr_name_cat,$file,$temp_for_base_cat,$url,$local); 
          $project_count++;
				} 
      }
		}else {
			if(isset($cats_for_project)&&count($cats_for_project)>0){
				foreach($cats_for_project as $cat) {
					$temp_for_base_cat = $temp_for_base.'cat_ar='.$cat.';</yml_options>';
          $pr_name_cat = $pr_name.' by category:'.$cat;
					$this->create_project($pr_name_cat,$file,$temp_for_base_cat,$url,$local);
          $project_count++;
				}
			}
		}
		$result['project_count'] = $project_count;
    echo json_encode($result);
	}
	

	
	public function create_project($name,$file,$temp,$url,$local) {

		global $wpdb;
		$wpdb->insert("{$wpdb->prefix}importyml_project",array(
                    'project_name' => $name,
                    'project_file' => $file,
					'project_template' => $temp,
                    'project_url' => $url,
                    'project_local'=>$local
        ),array('%s','%s','%s','%s','%d'));
	}

}
