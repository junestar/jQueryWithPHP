<?php require('header.php'); ?>
<?php include('login_column.php'); ?>

<div class="main-page">
	
<header>
	<nav>
		<div class="nav-content">
			<ul>
				<li class="nav-menu"><span><img src="images/menu.png"></span></li>
				<li class="main-title recipe-title">一手好菜</li>
				<li class="search-form">
					<input type="search" placeholder="食谱 食材 工具 话题" />
					<div style="top:15px;" class="search-input-icon" id="recipe-search-icon"><span class="glyphicon glyphicon-search"></span></div>
				</li>
			</ul>
		</div>

		<div class="nav-recipe-menu">
			<ul>
				<li id="recipe-material-index"><a id="recipe-menu-name-first">食材检索</a> <span class="glyphicon glyphicon-triangle-right"></span></li>
				<li>|</li>
				<li id="recipe-material-style"><a id="recipe-menu-name-second">料理风格</a> <span class="glyphicon glyphicon-triangle-right"></span></li>
			</ul>
		</div>
		
		<div id="recipe-menu-index" class="recipe-menu-container">
			<div class="recipe-menu-slidedown"></div>
		</div>
		
		<div id="recipe-menu-style" class="recipe-menu-container">
			<div class="recipe-menu-slidedown"></div>
		</div>

	</nav>
</header>


<section>

</section>

<div class="loading">
	<div class="loading-main"><span class="glyphicon glyphicon-option-horizontal"></span><span class="glyphicon glyphicon-option-horizontal"></span></div>
</div>

</div>

<script type="text/javascript">
		
		//食谱上方按钮被单击
		//method可以为up|down
		function toggleBtnArrow(elem,method){
			switch(method){
				case 'up':
					$(elem).removeClass('glyphicon glyphicon-triangle-bottom');
					$(elem).addClass('glyphicon glyphicon-triangle-right');
					break;
				case 'down':
					$(elem).removeClass('glyphicon glyphicon-triangle-right');
					$(elem).addClass('glyphicon glyphicon-triangle-bottom');
					break;
			}
		}

		$('.recipe-menu-container').css('top',$('header').height());

		var recipeMenuIsSlided=false;
		var recipeLeftMenuIsSlided=false;
		var recipeRightMenuIsSlided=false;
		var defaultPage=1;

		function toggleSlideMenu(obj){
			var typeClicked=$(obj).attr('id').split('-');
			if(!recipeMenuIsSlided){
				if(recipeLeftMenuIsSlided){
					toggleBtnArrow('.nav-recipe-menu ul #recipe-material-index span','up');
					$('#recipe-menu-index').slideUp(200);
					recipeLeftMenuIsSlided=false;
					recipeMenuIsSlided=false;
					if(typeClicked[2]=='index'){
						$(obj).find('span').removeClass('glyphicon glyphicon-triangle-bottom');
						$(obj).find('span').addClass('glyphicon glyphicon-triangle-right');
						return;
					}
				}

				if(recipeRightMenuIsSlided){
					toggleBtnArrow('.nav-recipe-menu ul #recipe-material-style span','up');
					$('#recipe-menu-style').slideUp(200);
					recipeRightMenuIsSlided=false;
					recipeMenuIsSlided=false;
					if(typeClicked[2]=='style'){
						$(obj).find('span').removeClass('glyphicon glyphicon-triangle-bottom');
						$(obj).find('span').addClass('glyphicon glyphicon-triangle-right');
						return;
					}
				}

				$(obj).find('span').removeClass('glyphicon glyphicon-triangle-right');
				$(obj).find('span').addClass('glyphicon glyphicon-triangle-bottom');
				
				$('#recipe-menu-'+typeClicked[2]).slideDown(200);
				if(typeClicked[2]=='index'){
					recipeLeftMenuIsSlided=true;
				}else if(typeClicked[2]=='style'){
					recipeRightMenuIsSlided=true;
				}
			}
		}

		$('.nav-recipe-menu ul li').click(function(){
			toggleSlideMenu(this);
		});
		
		displayALertForm('正在加载...');
		getRecipeClassify(function(data){
			if(data!=''){
				var jsonData=JSON.parse(data);
				if(jsonData['msg']=='成功'){
					$('.nav-recipe-menu ul li #recipe-menu-name-first').html(jsonData['data'][0]['title']);
					$('.nav-recipe-menu ul li #recipe-menu-name-second').html(jsonData['data'][1]['title']);
					var menuChild=jsonData['data'][0]['children'];
					var leftRow1='';
					var leftRow2='';
					var rightRow1='';
					var rightRow2='';
					var count=Math.ceil(parseInt(menuChild.length)/2);
					for (var j = 0; j < count; j++) {
						leftRow1+="<li onclick='handleSlidedownMenuEvent(this,\"left\")' idata=\""+menuChild[j]['id']+"\"><img src=\""+menuChild[j]['icon']+"\">"+menuChild[j]['title']+"</li>";
					};
					leftRow1="<ul>"+leftRow1+"</ul>";
					for (var j = 3; j < menuChild.length; j++) {
						leftRow2+="<li onclick='handleSlidedownMenuEvent(this,\"left\")' idata=\""+menuChild[j]['id']+"\"><img src=\""+menuChild[j]['icon']+"\">"+menuChild[j]['title']+"</li>";
					};
					leftRow2="<ul>"+leftRow2+"</ul>";

					var menuChild=jsonData['data'][1]['children'];
					var count=Math.ceil(parseInt(menuChild.length)/2);
					for (var j = 0; j < count; j++) {
						rightRow1+="<li onclick='handleSlidedownMenuEvent(this,\"right\")'  idata=\""+menuChild[j]['id']+"\">"+menuChild[j]['title']+"</li>";
					};
					rightRow1="<ul>"+rightRow1+"</ul>";
					for (var j = 3; j < menuChild.length; j++) {
						rightRow2+="<li onclick='handleSlidedownMenuEvent(this,\"right\")'  idata=\""+menuChild[j]['id']+"\">"+menuChild[j]['title']+"</li>";
					};
					rightRow2="<ul>"+rightRow2+"</ul>";
					$('#recipe-menu-index .recipe-menu-slidedown').append(leftRow1+leftRow2);
					$('#recipe-menu-style .recipe-menu-slidedown').append(rightRow1+rightRow2);
				}else{
					displayALertForm(jsonData['msg']);
				}
			}else{
				displayALertForm('获取失败,请重试');
			}
			
		});

		var currentRecipesType=0;

		function dsiplayRecipePost(data){
			var jsonData=JSON.parse(data);
			displayNoData();
			var homeList=jsonData['data'];
			if(homeList!=null){
				var homeListHtmlDOM='';
				var teacherBrandCSS='';
				var favouriteList=getFavourteList();
				for (var i = 0; i < homeList.length; i++) {
					var favourite_icon='add_grey.png';
					if(homeList[i]['paper'].length>=36){
						homeList[i]['paper']=homeList[i]['paper'].substring(0,36)+'...';
					}
					/*var paperLength=homeList[i]['paper'].length;
					if(paperLength<44){
						teacherBrandCSS='margin-top:-140px!important;'
					}
					if(paperLength>=44){
						teacherBrandCSS='margin-top:-165px!important;'
					}else if(paperLength>=34){
						teacherBrandCSS='margin-top:-150px!important;';
					}
					if(paperLength>=48 && paperLength<65){
						teacherBrandCSS='margin-top:-170px!important;';
					}else if(paperLength>=45){
						teacherBrandCSS='margin-top:-190px!important;';
					}*/

					if(typeof favouriteList!='undefined'){
						for (var k = 0; k < favouriteList.length; k++) {
							var collection=favouriteList[k].split('|');
							var atype=collection[0];
							var aid=collection[1];
							if(aid==homeList[i]['id']){
								favourite_icon='add_red.png';
								break;
							}
						};
					}

					var isVipHTML=homeList[i]['is_vip']=='1' ? '<div class="monograph-vip-img"><img src="images/vip.png"></div><div class="teacher-brand" id="monograph-member">会员专享</div>' : '';
					homeListHtmlDOM+='<div isvip="'+homeList[i]['is_vip']+'" idata="'+homeList[i]['id']+'" class="vip-enjoy"><div isvip="'+homeList[i]['is_vip']+'" ref="introduction.php?id='+homeList[i]['id']+'" onclick="locateToIntroduction(this)" style="background:url('+homeList[i]['image']+') no-repeat scroll center center transparent;background-size:cover;" class="vip-video"></div><div class="vip-content"><div class="teacher-brand"><img src="'+homeList[i]['arrange_image_url']+'"></div><div isvip="'+homeList[i]['is_vip']+'" ref="introduction.php?id='+homeList[i]['id']+'" onclick="locateToIntroduction(this)" class="vip-title">'+homeList[i]["title"]+'</a></div><div isvip="'+homeList[i]['is_vip']+'" ref="introduction.php?id='+homeList[i]['id']+'" onclick="locateToIntroduction(this)" class="vip-post">'+homeList[i]['paper']+'</a></div><div class="vip-menu"><ul><li><img width="30" height="16" style="" src="images/watch_grey.png"></img> <span>'+homeList[i]["browse_num"]+'</span></li><li type="1" articleid="'+homeList[i]['id']+'" onclick="addToReadingList(this);"><img width="18" height="18" src="images/'+favourite_icon+'"></img></li><li onclick="displayShareForm(this);"><img width="18" height="18" src="images/share_grey.png"></img></li></ul></div></div>'+isVipHTML+'</div>';
				};
				$('section').append(homeListHtmlDOM+'<div class="padding-div-row"></div>');
			}else{
				displayNoData('再怎么找也没有啦');
			}
		}
		
		function loadRecipesList(type,page,limit,other){
			$('.loading').fadeIn();
			getRecipeList(type,page,limit,function(data){
				if(data!=''){
					if(other){
						$('section').html('');
					}
					$('.padding-div-row').remove();
					dsiplayRecipePost(data);
					$('.loading').fadeOut();
				}else{
					displayALertForm('获取失败,请重试');
				}
			});
		}

		loadRecipesList(currentRecipesType,defaultPage,defaultLimit,false);
	
		function handleSlidedownMenuEvent(obj,which){
			if(which=='left'){
				$(obj).parent().parent().parent().slideToggle(200);
				toggleBtnArrow('.nav-recipe-menu ul #recipe-material-index span','up');
				recipeLeftMenuIsSlided=false;
			}else if(which=='right'){
				$(obj).parent().parent().parent().slideToggle(200);
				toggleBtnArrow('.nav-recipe-menu ul #recipe-material-style span','up');
				recipeRightMenuIsSlided=false;
			}
			$('.loading').fadeIn();
			currentRecipesType=$(obj).attr('idata');
			recipesDefaultPage=1;
			recipeDefaultLimit=10;
			if($(obj).hasClass('nav-recipe-menu-active')){
				$(obj).removeClass('nav-recipe-menu-active');
				currentRecipesType=10;
			}else{
				$('.nav-recipe-menu-active').removeClass('nav-recipe-menu-active');
				$(obj).addClass('nav-recipe-menu-active');
			}
			loadRecipesList(currentRecipesType,defaultPage,defaultLimit,true);
		}

		function handleRecipesPagination(){
			if(isUserAtBottom()){
				displayALertForm('加载中...');
				loadRecipesList(currentRecipesType,++defaultPage,defaultLimit,false);
			}
		}

		$(window).scroll(handleRecipesPagination);

</script>

<?php include('footer.php'); ?>
