<!doctype html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta charset="utf-8">
	<link href="./css/bootstrap.min.css" rel="stylesheet">
	<script type="text/javascript" src="./js/jquery.js"></script>
	<script type="text/javascript" src="./js/bootstrap.min.js"></script>
	<script src="./js/angular.js"></script>
	<script src="./js/angular-sanitize.js"></script>
</head>
<style>
body{
    background: #f9f9f9;
}

.animeLShead{
    color: #fff;
    background: #009688;
    text-align: center;
    font-weight: 600;
    font-size: 22px;
    border-left: 1px solid;
}
.animeLSbody{
    font-size: 18px;
}
</style>
<body>
<div class="container-fluid">
	<div ng-app="CAMII" ng-controller="camiiCtr">
    	<div class="row">
    		<div class="col-md-12 col-sm-12 border ">
    			Busca: <input type="text" class="busca" ng-change="busca($event)" ng-model="ftxt"><br /><br />
    			<span ng-repeat="x in bMen" >
    				<img alt="" src="./img/anime/{{x.ANIIMG}}.jpg">
    				<div ng-bind-html="x.ANINOME"></div>  <button value="{{ x.ANICOD }}" ng-click="addBTN($event)" style='display:{{x.a}}'>add</button><br/>
    			</span>
    		</div>
    			
    		<div class="col-md-12 ">
            	<anime></anime>
            	<malsearch></malsearch>
            	<stats></stats>
            	
    		</div>
    	</div>
    	
	</div>	
</div>	<div class="bg-white">

<script type="text/javascript">
//remover sanitize
	
	var app = angular.module('CAMII',['ngSanitize']);
	app.controller('camiiCtr', function($scope, $http){

		//########### SEARCH
		var time;//tempo para iniciar a busca
		$scope.busca = function (event) {
			clearTimeout(time);
			time = setTimeout(function(){
				var txt = $(".busca").val();
				$http.get("./searchAnimetJSON.php?search="+txt).then(function(response){
					console.log(response.data);
					for(i=0; i<response.data.length;i++){
						//console.log(response.data[i]);
						var tx = response.data[i];
						tx.a = "";
						if(tx.MINCLIENTE == 1){
							tx.a = "none";
						}
						response.data[i].texto = tx;
					}
					$scope.bMen = response.data;
				});
			},1000);
		}

		//#### ADD a lista
		$scope.addBTN = function(event){
			var txt = event.target.value;
			//alert("./saveAnimeLista.php?add="+txt);
			$http.get("./saveAnimeLista.php?add="+txt).then(function(response){
				//console.log(response.data);
				if(response.data.sit == 1){
					alert("add");
				}else{
					alert("erro");
				}
			});
		}
	}).directive('anime', ['$http', function($http) { 
		//### GET ANIME LISTA
		  return {
    		  restrict: 'AE',
    		  replace: true,
    		  transclude: true,
    	      scope: {title: '@'},
    		  controller: ['$scope', function m($scope) {
    			  $http.get("./myListJSON.php").then(function(response){
    					//console.log(response.data);
    					$scope.filtroLista = {"MINSITUACAO" : 1}; // DEFINI a LISTA para os anime com status de assistindo
    					for(i=0; i<response.data.length;i++){
    						//console.log(response.data[i]);
    						var tx = response.data[i];
    						tx.a = i+1;
    						response.data[i].texto = tx;
    					}
    					$scope.camiiMen = response.data	;

    					//#### ADD 1 ep
    					$scope.addMais1BTN = function(event) { 
        					//console.log( event.target.value);
        					var ep = new Array();
    						 	ep[0] = $(event.target).parent().children('.ep');
    						 	ep[1]= parseInt($(ep[0]).html());
    						 	var epMax = new Array();
    						 	epMax[0] = $(event.target).parent().children('.epMax');
    						 	epMax[1]= parseInt($(epMax[0]).html());
    						 	var anime = event.target.value;
							if( (ep[1]+1) < epMax[1]){ //arrumar
								$http.get("./API/save1ep.php?anime="+anime+"&ep="+(ep[1]+1)).then(function(response){
									//alert(response.data.sit);
									$(ep[0]).html(ep[1]+1); // desativar a execulsao até concluir para evitar erro
	                 			})
	                 			return;
							}

							if((ep[1]+1) == epMax[1]){
								alert(ep[1]+1)
								$http.get("./API/save1epStatusCompleto.php?anime="+anime+"&ep="+(ep[1]+1)).then(function(response){
									//alert(response.data.sit);
									$(ep[0]).html(ep[1]+1); // desativar a execulsao até concluir para evitar erro
	                 			})
								
								return;
							}
							
							
        				}
						
						$scope.filtroListaF = function(x){
							$scope.filtroLista = {"MINSITUACAO" : x};
						}
						
						
        				//##### Editar info anime na lista
        				$scope.editarAnimeLs = function(event) {
 							//alert(event.target.value);
 							var anime = event.target.value;

							$http.get("./API/get1AnimeInfoList.php?anime="+anime).then(function(response){
								console.log(response.data);
								$scope.nome = response.data.ANINOME;
								$scope.cod = anime;
								$('#status').val(response.data.MINSITUACAO);
								$('#ep').attr("max", response.data.ANIEPI);
								$('#ep').val(response.data.EP);
								$('#epTotal').val(response.data.ANIEPI);
								
								var dateStr=response.data.MININICIO;
								$("#dataI").val(dateStr);

								var datefStr=response.data.MINFINAL;
								$("#dataF").val(datefStr);

                 			})
                 			//## get Episodios DE um certo anime assistido
                 			$http.get("./API/getEpAnimeList.php?anime="+anime).then(function(response){
								//console.log(response.data);
								for(i=0; i<response.data.length;i++){
									//console.log(response.data[i]);
									var tx = response.data[i];
									
									response.data[i].texto = tx;
								}
								$scope.episodios = response.data;

                 			})
						}
						//##### Save Editar Anime
						$scope.saveEditarAnime = function(){
							
							$("#ep").val();
							var anime = $("#cod").val();
							var sit = $("#status").val();
							var dI = $("#dataI").val();
							var dF = $("#dataF").val();
							if(sit ==2){
								alert(sit);
								$http.get("./API/updateAnimeLsCompleto.php?anime="+anime+"&sit="+sit+"&dI="+dI+"&dF="+dF).then(function(response){
								console.log(response.data);
								if(response.data.sit == 1){
									$('#ModalEditar').modal('toggle');
    								}		
                     			})
                     			
                     			
								return
							}
							$http.get("./API/animeUpdateList.php?anime="+anime+"&sit="+sit+"&dI="+dI+"&dF="+dF).then(function(response){
								console.log(response.data);
								if(response.data.sit == 1){
									$('#ModalEditar').modal('toggle');
								}		
                 			})
						}

						//##  Editar ep DATA abilitar btn
						$scope.editEpData = function(event) {
							var ep = $(event.target).attr("name");
							var btnEp = $("#btnEp"+ep).removeClass("d-none");
						}
						//## SAVE editar Ep
						$scope.saveEditEp = function(event) {
							var ep = $(event.target).val();
							var data = $("#epData"+ep).val();
							var anime = $("#cod").val();
							

							$http.get("./API/animeUpdateEpList.php?anime="+anime+"&ep="+ep+"&d="+data).then(function(response){
								console.log(response.data);
								if(response.data.sit == 1){
									alert(2);
									//$('#ModalEditar').modal('toggle');
								}		
                 			})
							
						}
    				}); 
    		  }],
    		  templateUrl: './template/animeLSitem.html'
		  };
	}])
	.directive('malsearch', ['$http', function($http) { 
		//### GET ANIME LISTA
        return {
            restrict: 'AE',
            replace: true,
            transclude: true,
            scope: {title: '@'},
            controller: ['$scope', function m($scope) {
            	//## MAL
        		var time;
        		$scope.buscaMal = function (event) {
        			clearTimeout(time);
        			time = setTimeout(function(){
        				var txt = $(".buscaMal").val();
        				$http.get("./API/MALanimeSearch.php?search="+txt).then(function(response){
        					//console.log(response.data);
        					if(response.data != ""){
            					//console.log(response.data.categories[0].items.length);
            					for(i=0; i<response.data.categories[0].items.length;i++){
            						//console.log(response.data.categories[1].items[i]);
            						var tx = response.data.categories[0].items[i];
            						response.data[i] = tx;
            					}
        					}
        					$scope.malMen = response.data;
							//### ADD novo anime evento
        					$scope.addNewAnimeBTN = function(event) {
								console.log(event.target.value);
								var an = event.target.value;
								$http.get("./API/saveNewAnime.php?cod="+an).then(function(response){
									console.log(response.data);
									if(response.data.sit == 1){
										alert(10);
									}		
								});
							}	
        				});
        			},1000);
        		}
            }],
            templateUrl: './template/animeBoxNewTL.html'
		};
	}])
	.directive('stats',['$http',function($http){
		//##### STATS
		return{
			restrict: 'AE',
            replace: true,
            transclude: true,
            scope: {title: '@'},
            controller: ['$scope', function m($scope) {
				//$scope.a = 1;

				$http.get("./API/getAnimeStats.php?anime=a").then(function(response){
					console.log(response.data);
					// ### ANO
					$scope.statsAno = response.data.ANO;

					//### DIAS FALTADO
					var diaFinal =  365-response.data.META.DIA;
					if(response.data.META.B == "1" && response.data.META.DIA>59)
						diaFinal++;
					$scope.diafalta = diaFinal;
					var epT = response.data.ANO[0].ANIME;
					$scope.epAssistidos = epT;
					$scope.epTotal = 1000-epT;
					$scope.epDia = (1000-epT)/diaFinal;
     			})

				
            }],
            templateUrl: './template/statsTL.html' 
		};
	}]);
	
	</script>
</body>
</html>