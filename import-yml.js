function ArraySplitTo ( arr, n) {
		var plen = Math.ceil(arr.length / n);
		return arr.reduce( function( p, c, i, a) {
		if(i%plen === 0) p.push({});
		p[p.length-1][i] = c;
		return p;
		}, []);
	}

function ImportYml($)
{
	this.editingForm = function(idProject)
	{
		$("#iySaveProject").click(function(){
			if($('input[name=projectName]').val()==''||($('input[name=yi-projectYML]').val()==''&&$('input[name=yi-projectYML_file]').val()=='')){
				alert('Для добавления проекта заполните необходимые поля');
			}else if($('input[name=yi-projectYML]').val()!=''&&$('input[name=yi-projectYML_file]').val()!=''){
				alert('Вы указали 2 вида источника');
			}else {
				$('[name="yi-import-template"]').val($('[name="importTemplate"]').val());
				$("[name='yi-projectForm']").submit();
			}
		});
		
		if (idProject == "new")
		{

		}
		else
		{
			//$("#iySaveProject").attr('disabled','disabled');
			
			$('#yiShowImportView').click(function()
			{
				$("#importView").css('display','block');
			});
			
			
			//После нажатия на кнопку происходить полный импорт категорий и записей
			$("#iyImportProject").click(function()
			{
				jQuery('<div id="parser_shadow_window"></div>').prependTo('body');
				jQuery('<div id="parser_background_message"><div><a id="continueButton">X</a><h2>Добавляем категории</h2><div id="createPostBar_cats"></div><div id="createPostBarMessage_cats" style="font-size:20px;margin:10px 0;">Обработано <span class="howmany">0</span> из <span class="total">0</span></div><h2>Добавляем записи</h2><div id="createPostBar_offers"></div><div id="createPostBarMessage_offers" style="font-size:20px;margin:10px 0;">Обработано <span class="howmany">0</span> из <span class="total">0</span></div></div></div>').prependTo('body');
				// Добавляем категории
				function create_cats(){	
					var cats = null;
					$.ajax({
						url: 'tools.php',
						data: {'iy-ajax':1,'iy-project-id':idProject,'iy-project-action':'getCategoriesId'},
						async: true,
						dataType: "json",
						success: function(data)
						{
							cats = data;
							if (cats.length>0) {
								$("#createPostBarMessage_cats .total").html(cats.length);
								var howmany = 0;
								var pack_size= 100;
								
								$( "#createPostBar_cats" ).progressbar({value: 0});
								
								var result = ArraySplitTo(cats,(cats.length/pack_size)*1);
								(function create_cat(i){
									if (i<result.length) {
										$.ajax({
											url: 'tools.php',
											data: {'iy-ajax':1,'iy-project-id':idProject,'iy-project-action':'createCatsId','cats' : result[i]},
											async: true,
											success: function(data)
											{
												var one = 100 / result.length;
												var all_cats = cats.length;
												howmany = howmany+pack_size;
												if(all_cats<howmany) {
													howmany = all_cats;
												}
												$("#createPostBarMessage_cats .howmany").html(howmany);
												jQuery( "#createPostBar_cats" ).progressbar({
													value: jQuery( "#createPostBar_cats" ).progressbar('value') + one
												});
												create_cat(i+1);
											}
										});
									}else {
										return recalculate();
									}
								})(0); 
							}else {
								return create_posts();
							}

						}
					});
					
				}
				
        		function create_posts(){	
					// Добавляем посты
					var offers = null;
					$.ajax({
						url: 'tools.php',
						data: {'iy-ajax':1,'iy-project-id':idProject,'iy-project-action':'getOffers'},
						async: true,
						dataType: "json",
						success: function(data)
						{
							offers = data;
							if (offers.length>0) {
								//work with packages
								$("#createPostBarMessage_offers .total").html(offers.length);
								var howmany = 0;
								var pack_size= $('.package_size').val();
								pack_size =  pack_size*1;
								if (pack_size<=0) {
									pack_size = 100;
								}
								$( "#createPostBar_offers" ).progressbar({value: 0});
								
								var result = ArraySplitTo(offers,(offers.length/pack_size)*1);
								
								(function create(i){
									if (i<result.length) {
										$.ajax({
											url: 'tools.php',
											data: {'iy-ajax':1,'iy-project-id':idProject,'iy-project-action':'createPostsId','offers' : result[i]},
											async: true,
											success: function(data)
											{
												var one = 100 / result.length;
												var all_offers = offers.length;
												howmany = howmany+pack_size;
												if(all_offers<howmany) {
													howmany = all_offers;
												}
												$("#createPostBarMessage_offers .howmany").html(howmany);
												jQuery( "#createPostBar_offers" ).progressbar({
													value: jQuery( "#createPostBar_offers" ).progressbar('value') + one
												});
												create(i+1);
											}
										});
									}/* else {
										return recalculate();
									} */
								})(0); 
							}/* else {
								return recalculate();
							}	 */
						}
					});
				}
				
				function recalculate(){	
					$.ajax({
						url: 'tools.php',
						data: {'iy-ajax':1,'iy-project-id':idProject,'iy-project-action':'recalculate'},
						async: true,
						dataType: "json",
						success: function(data)
						{
              
						}
					}); 
          return create_posts();
				}
				create_cats();
				
			});
			
			$("#yiAnalizeProject").click(function()
			{	
				var site_url = jQuery(this).data('site');
				$.ajax({
					url: 'tools.php',
					data: {'iy-ajax':1,'iy-project-id':idProject,'iy-project-action':'analize'},
					beforeSend: function () {
						jQuery('<div id="parser_shadow_window"></div>').prependTo('body');
						jQuery('<div id="parser_background_message"><div><a id="continueButton">X</a><h2 class="wait">Ожидайте идет анализ структуры проекта!</h2><img class="wait_img" src="'+site_url+'/loader.gif"/><div id="iy-log1"></div></div></div>').prependTo('body');
					},
					complete: function () {
						jQuery('#parser_background_message .wait_img').hide();
					},
					success: function(data1){
						var res = $.parseJSON(data1);
						$('.feed_info .offers span').text(res.offers);
						$('.feed_info .cats span').text(res.categs);
						if (res.no_file ==1) {
							jQuery('#parser_background_message .wait').html('Файл проекта не найден! провеьте доступность файла на сервере.');
						}
						if (res.no_file ==2) {
							jQuery('#parser_background_message .wait').html('Файл проекта не валиден!');
						}
						if (res.no_file ==0){
							jQuery('#parser_background_message .wait').html('Анализ и создание контента прошло успешно!');
						}						  
					}
				});
			});
			
			$("#yiDeleteBeforeParsing").click(function()
			{	
				$.ajax({
					  url: 'tools.php',
					  data: {'iy-ajax':1,'iy-project-id':idProject,'iy-project-action':'delete_before'},
					  success: function(data)
					  {
						  if (data == 'OK'){
							   $('.feed_info .offers span').text(0);
								$('.feed_info .cats span').text(0);
							  alert('Таблицы проекта очищены успешно!');
							 
						  }else {
							alert('Ошибка! Проект уже импортирован.');
						  }
					  }
					});
			});
			
		$("#yiCreateByParentCat").click(function()
		{	
			var quont= $('.clone_cat_quont').val();
			$.ajax({
				url: 'tools.php',
				data: {'iy-ajax':1,'iy-project-id':idProject,'iy-project-action':'create_project_by_par_cat','iy-quont_cat':quont},
				success: function(data)
				{
					var result_ar = $.parseJSON(data);
					if(result_ar.error==true) {
						alert('Ошибка! файл проекта не найден.');
					}else {
						if (parseInt(result_ar.project_count)>0){
							alert('Создано проектов: '+result_ar.project_count);
						}else {
							alert('Проекты не созданы проверьте структуру xml документа.');
						}
					}
				}
			});
			
		});
		
		jQuery('body').on('click','#continueButton',function() {
					jQuery('#parser_shadow_window').remove();
					jQuery('#parser_background_message').remove();
					return false;
		});
			
		$("#yiCreateByEndCat").click(function()
			{	
				$.ajax({
					  url: 'tools.php',
					  data: {'iy-ajax':1,'iy-project-id':idProject,'iy-project-action':'create_project_by_end_cat'},
					  success: function(data)
					  {
						var result_ar = $.parseJSON(data);
						if(result_ar.error==true) {
							alert('Ошибка! файл проекта не найден.');
						}else {
							if (parseInt(result_ar.project_count)>0){
								alert('Создано проектов: '+result_ar.project_count);
							}else {
								alert('Проекты не созданы проверьте структуру xml документа.');
							}
						}
					  }
					});
			});
      
		$("#yiCloneProject").click(function(){	
			var site_url = jQuery(this).data('site');
			$.ajax({
				url: 'tools.php',
				data: {'iy-ajax':1,'iy-project-id':idProject,'iy-project-action':'clone'},
				beforeSend: function () {
					jQuery('<div id="parser_shadow_window"></div>').prependTo('body');
					jQuery('<div id="parser_background_message"><div><a id="continueButton">X</a><h2 class="wait">Ожидайте идет создание копии проекта!</h2><img class="wait_img" src="'+site_url+'/loader.gif"/><div id="iy-log1"></div></div></div>').prependTo('body');
				},
				complete: function () {
					jQuery('#parser_background_message .wait_img').hide();
				},
				success: function(data) {
					jQuery('#parser_background_message .wait').html('Проект '+idProject+' скопирован успешно.');
				}
			});
		});
      
		$("#yiCronProject").change(function(){	
			var duration= $(this).val();
			$.ajax({
				url: 'tools.php',
				data: {'iy-project-action':'crone','iy-time':duration},
				success: function(data){
				
				}
			});
		});
			
			
			$("#yiDeleteProjectContent").click(function()
			{
				jQuery('<div id="parser_shadow_window"></div>').prependTo('body');
				jQuery('<div id="parser_background_message"><div><a id="continueButton">X</a><h2>Удаление медиафайлов</h2><div id="createPostBar_attach"></div><div id="createPostBarMessage_attach" style="font-size:20px;margin:10px 0;">Обработано <span class="howmany">0</span> из <span class="total">0</span></div><h2>Удаление записей</h2><div id="createPostBar_post"></div><div id="createPostBarMessage_post" style="font-size:20px;margin:10px 0;">Обработано <span class="howmany">0</span> из <span class="total">0</span></div></div></div>').prependTo('body');
				function delete_atachs(){
					var attachs = null;
					$.ajax({
						url: 'tools.php',
						data: {'iy-ajax':1,'iy-project-id':idProject,'iy-project-action':'getAttachs'},
						async: true,
						dataType: "json",
						success: function(data)
						{
							attachs = data;
							if (attachs.length>0) {
								var pack_size= $('.package_size').val();
								pack_size =  pack_size*1;
								if (pack_size<=0) {
									pack_size = 100;
								}
								$("#createPostBarMessage_attach .total").html(attachs.length);
								var howmany = 0;
								$( "#createPostBar_attach" ).progressbar({value: 0});			
								var result = ArraySplitTo(attachs,(attachs.length/pack_size)*1);							
								(function delete_att(q){
									if (q<result.length) {
										$.ajax({
											url: 'tools.php',
											data: {'iy-ajax':1,'iy-project-id':idProject,'iy-project-action':'deletePostsbyIds','posts' : result[q]},
											async: true,
											success: function(data)	{
												var one = 100 / result.length;
												var all_posts= attachs.length;
												howmany=howmany+pack_size;
												if(all_posts<howmany) {
													howmany = all_posts;
												}
												$("#createPostBarMessage_attach .howmany").html(howmany);
												jQuery( "#createPostBar_attach" ).progressbar({
													value: jQuery( "#createPostBar_attach" ).progressbar('value') + one
												});
												delete_att(q+1);
											}
										});
									}else {
										return delete_posts();
									}
								})(0); 
							}else {
								return delete_posts();
							}
						}
					});		
				}
				
				function delete_posts(){
					var posts = null;
					$.ajax({
						url: 'tools.php',
						data: {'iy-ajax':1,'iy-project-id':idProject,'iy-project-action':'getPosts'},
						async: true,
						dataType: "json",
						success: function(data)
						{
							posts = data;
							if (posts.length>0) {
								var pack_size= $('.package_size').val();
								pack_size =  pack_size*1;
								if (pack_size<=0) {
									pack_size = 100;
								}
								$("#createPostBarMessage_post .total").html(posts.length);
								var howmany_post = 0;
								$( "#createPostBar_post" ).progressbar({value: 0});		
								var result = ArraySplitTo(posts,(posts.length/pack_size)*1);
								(function delete_posts(p){
									if (p<result.length) {
										$.ajax({
										url: 'tools.php',
										data: {'iy-ajax':1,'iy-project-id':idProject,'iy-project-action':'deletePostsbyIds','posts' : result[p]},
										async: true,
										success: function(data){
												var piece = 100 / result.length;
												var all_posts= posts.length;
												howmany_post=howmany_post+pack_size;
												if(all_posts<howmany_post) {
													howmany_post = all_posts;
												}
												$("#createPostBarMessage_post .howmany").html(howmany_post);
												jQuery( "#createPostBar_post" ).progressbar({
													value: jQuery( "#createPostBar_post" ).progressbar('value') + piece
												});
												delete_posts(p+1);
											}
										});
									}else {
										return delete_cont();
									}
								})(0); 
								
							}else {
								return delete_cont();
							}
						}
					});	
				}
				
				function delete_cont(){
					$.ajax({
						  url: 'tools.php',
						  data: {'iy-ajax':1,'iy-project-id':idProject,'iy-project-action':'deleteContent'},
						  async: true,
						  success: function(data)
						  {
							
						  }
					});	
				}
				
				delete_atachs();
			});

		}

	}
	
	this.init = function()
	{
		if ($("[name='import-yml-project']").val() !="undefined")
		{
			/* Значит работаем с формой редактирования.*/
			this.editingForm($("[name='import-yml-project']").val());
		}
	}
	this.init();

	this.updateProject = function(id,img_path) {
		jQuery('<div id="parser_shadow_window"></div>').prependTo('body');
		jQuery('<div id="parser_background_message"><div><a id="continueButton">X</a><h2 class="wait">Ожидайте идет обновление!</h2><img class="wait_img" src="'+img_path+'/loader.gif"/><div id="iy-log1"></div></div></div>').prependTo('body');
		var url = "tools.php?iy-ajax&iy-project-id=" + id + "&iy-project-action=update";
		$.get(url,function(data) {
			
		}).done(function(data) {
			$('#iy-log1').html(data);
			$('.wait').hide();
			$('.wait_img').hide();
		})
		.fail(function(data) {
			$('#iy-log1').html('<h2>Ошибка выполнения скрипта</h2><br><p>Для обновления большого кол-ва товарных позиций требуются большие вычислительные ресурсы сервера. Попробуйте увеличить параметр "memory_limit" до 128M</p>');
			$('.wait').hide();
			$('.wait_img').hide();
		});
	}
	
	
}


jQuery(function($)
{
	window.iy = new ImportYml($);
	
	$("#yiBulkAnalizeProjects").click(function(){	
		var site_url = jQuery(this).data('site');
		$.ajax({
				url: 'tools.php',
				data: {'iy-project-action':'bulk-analize'},
				beforeSend: function () {
					jQuery('<div id="parser_shadow_window"></div>').prependTo('body');
					jQuery('<div id="parser_background_message"><div><a id="continueButton">X</a><h2 class="wait">Определяем количество проектов которые необходимо анализировать</h2><img class="wait_img" src="'+site_url+'/loader.gif"/><div id="createPostBar_project" style="display:none;"></div><div id="createPostBarMessage_project" style="font-size:20px;margin:10px 0;display:none;">Обработано <span class="howmany">0</span> из <span class="total">0</span></div></div></div>').prependTo('body');
				},
				complete: function () {
					jQuery('#parser_background_message .wait_img').hide();
				},
				success: function(data)
				{
					var res = $.parseJSON(data);
					if (res.no_projects==1) {
						jQuery('#parser_background_message .wait').html('Не один проект еще не создан!');
					}else {
						if (res.all_analized==1) {
							jQuery('#parser_background_message .wait').html('Все проекты уже проанализированы!');
						}else {
							jQuery('#parser_background_message .wait').html('Анализируем проекты');
							jQuery('#parser_background_message #createPostBar_project').show();
							jQuery('#parser_background_message #createPostBarMessage_project').show();
							var pr_id_ar = $.parseJSON(res.projects_id);
							$("#createPostBarMessage_project .total").html(pr_id_ar.length);
							var howmany = 0;
							$( "#createPostBar_project").progressbar({value: 0});		
							(function analize(q){
								if (q<pr_id_ar.length) {
									$.ajax({
										url: 'tools.php',
										data:{'iy-ajax':1,'iy-project-id':pr_id_ar[q],'iy-project-action':'analize'},
										async: true,
										success: function(data)
										{
											var one = 100 / pr_id_ar.length;
											howmany++;
											$("#createPostBarMessage_project .howmany").html(howmany);
											jQuery( "#createPostBar_project").progressbar({
												value: jQuery( "#createPostBar_project" ).progressbar('value') + one
											});
											analize(q+1);
										}
									});
								}
							})(0);							
						}
					}
					
				}
		});
	});
});

