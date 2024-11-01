<?php

class ImportYml_Helper {
  
	public function copy($source, $target) {
		if (!is_dir($source)) {//it is a file, do a normal copy
		  copy($source, $target);
		  return;
		}

		//it is a folder, copy its files & sub-folders
		@mkdir($target);
		$d = dir($source);
		$navFolders = array('.', '..');
		while (false !== ($fileEntry=$d->read() )) {//copy one by one
		  //skip if it is navigation folder . or ..
		  if (in_array($fileEntry, $navFolders) ) {
			continue;
		  }

		  //do copy
		  $s = "$source/$fileEntry";
		  $t = "$target/$fileEntry";
		  self::copy($s, $t);
		}
		 $d->close();
	}
  
	public function shailan_get_file( $url, $target ){
		
		if( FALSE !== file_put_contents( $target , file_get_contents( $url ) ) ){
			return $target ;	
		}
	}
	
	public function recursiveRemoveDirectory($directory){
		foreach(glob("{$directory}/*") as $file){
			if(is_dir($file)) { 
				$this->recursiveRemoveDirectory($file);
			} else {
				unlink($file);
			}
		}
		rmdir($directory);
	}
	
	public function downloadZipSource($file,$filename){
		$files_source[] = self::shailan_get_file($file,ImportYml_dir_ymls."/{$filename}.zip");
		$zip_s = new ZipArchive;
		$res = $zip_s->open($files_source[0]);
			  
		if ($res === TRUE) {
			$filenames = array();
			for($i=0; $i<$zip_s->numFiles; $i++){
				//с помощью метода getNameIndex получаем имя элемента по индексу 
				//и помещаем в наш массив имён ;) 
				$filenames[] = $zip_s->getNameIndex($i);
			}
			$zip_s->extractTo(ImportYml_dir_ymls."/{$filename}");
			if(isset($filenames)&&is_array($filenames)){
				self::copy(ImportYml_dir_ymls."/{$filename}/".$filenames[0],ImportYml_dir_ymls."/{$filename}.yml");
			}else {
				echo "Архив не распакован";
			}
			self::recursiveRemoveDirectory(ImportYml_dir_ymls."/{$filename}");
			$zip_adr = ImportYml_dir_ymls."/{$filename}.zip";
			if(file_exists($zip_adr)){
				unlink($zip_adr);
			}
			$zip_s->close();
		} else {
			echo "Архив не распакован";
		} 
	}
	
	public function localZipSource($time_stump,$file_name){
		$zip_s = new ZipArchive;
		$res = $zip_s->open(ImportYml_dir_ymls."/".$time_stump."_".$file_name);
		if ($res === TRUE) {
			$filenames = array();
				for($i=0; $i<$zip_s->numFiles; $i++){
				//с помощью метода getNameIndex получаем имя элемента по индексу 
				//и помещаем в наш массив имён ;) 
				$filenames[] = $zip_s->getNameIndex($i);
			}
			$zip_s->extractTo(ImportYml_dir_ymls."/{$time_stump}_folder");
			if(isset($filenames)&&is_array($filenames)){
				self::copy(ImportYml_dir_ymls."/{$time_stump}_folder/".$filenames[0],ImportYml_dir_ymls."/{$time_stump}_file.yml");
			}else {
				echo "Архив не распакован";
			}
			self::recursiveRemoveDirectory(ImportYml_dir_ymls."/{$time_stump}_folder");
			$zip_adr = ImportYml_dir_ymls."/{$time_stump}_{$file_name}";
			if(file_exists($zip_adr)){
				unlink($zip_adr);
			}
			$zip_s->close();
		} else {
			echo "Архив не распакован";
		} 
	}
}