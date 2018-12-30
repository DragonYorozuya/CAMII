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
.searchBdAnimes img{
    height: 150px;
}
.colorSit{
    width: 10px;
}
</style>
<body>
<div class="container-fluid">
	<div ng-app="CAMII" ng-controller="camiiCtr">
    	<div class="row">
    		<div class="col-md-12 col-sm-12 border ">
    		<!--  
    			Busca: <input type="text" class="busca" ng-change="busca($event)" ng-model="ftxt"><br /><br />
    			  -->
    			
    			<div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">Busca</span>
                  </div>
                  <input type="text" class="form-control busca" ng-change="busca(ftxt)" ng-model="ftxt">
                  <div class="input-group-append">
                    <button class="btn btn-danger pl-4 pr-4" ng-click="clearBusca();busca('')"> X </button><br /><br />
                  </div>
    			</div> 
        			
        		<div class="row p-0 m-0">
        			<div class="col-md-6  searchBdAnimes " ng-repeat="x in bMen" >
            			<div class="row p-0 m-1 border">
            				<div class="col-md-4">
                          		<img alt="" src="./img/anime/{{x.ANIIMG}}.jpg" class="img-thumbnail">
                          	</div>
                            <div class="col-md-8">
                              <div ng-bind-html="x.ANINOME" class="pb-5"></div>  
                              <button ng-click="addBTN(x.ANICOD)" class="btn btn-primary" style='display:{{x.a}}'>
                              	<h4>add</h4>
                              </button>
                        	</div>
                    	</div>
                    </div>
                </div>
    		
    		</div>
    			
    		<div class="col-md-12 ">
    			<malsearch></malsearch>
            	<anime></anime>
            	<stats></stats>
            	<animeinfo></animeinfo>
    		</div>
    	</div>
    	
	</div>	
</div>	

<script type="text/javascript">
//remover sanitize
	
	var app = angular.module('CAMII',['ngSanitize']);
	app.controller('camiiCtr', function($scope, $http){

		//########### SEARCH
		var time;//tempo para iniciar a busca
		$scope.busca = function (txt) {
// 			console.log(e);
			clearTimeout(time);
			time = setTimeout(function(){
				$http.get("./searchAnimetJSON.php?search="+txt).then(function(response){
					//console.log(response.data);
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

		$scope.clearBusca = function(){
			$scope.ftxt = "";
		}

		//#### ADD a lista
		$scope.addBTN = function(anime){
			var txt = anime;
// 			alert("./saveAnimeLista.php?add="+txt);
			$http.get("./saveAnimeLista.php?add="+txt).then(function(response){
				console.log(response.data);
				if(response.data.sit == 1){
					$scope.clearBusca();
					$scope.busca('');
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
            scope: false,
            controller: ['$scope', function m($scope) {

				function exeListaAnime(){
    				$http.get("./myListJSON.php").then(function(response){
        				//          A,C,P,D,ova,F,filme
        				var tA = [0,0,0,0,0,0,0,0];
      					for(i=0; i<response.data.length;i++){
       					//console.log(response.data[i]);
      						var tx = response.data[i];
      						tx.a = i+1;
      						// remove botao + dos animes completos
      						response.data[i].btn1dd = "";
      						response.data[i].colorSit = "bg-success";
      						if(response.data[i].MINSITUACAO == 2){
								response.data[i].btn1dd = "d-none";
								response.data[i].colorSit = "bg-info";
              				}
      						
      						
      						response.data[i].texto = tx;
      						if(response.data[i].ANITIPO == 1){
        						switch(response.data[i].MINSITUACAO){
        							case '1': tA[1] += parseInt(response.data[i].EP); break;
        							case '2': tA[2] += parseInt(response.data[i].EP); break;
        							case '3': tA[3] += parseInt(response.data[i].EP); break;
        							case '4': tA[4] += parseInt(response.data[i].EP); break;
        							case '6': tA[5] += parseInt(response.data[i].EP);
        						}
      						}
      						if(response.data[i].ANITIPO == 2 || response.data[i].ANITIPO == 3 || response.data[i].ANITIPO == 5){
      							tA[5] += parseInt(response.data[i].EP);
              				}
      						if(response.data[i].ANITIPO == 4){
      							tA[7] += parseInt(response.data[i].EP);
              				}
      					}
      					

    					//FUNÃ‡ÃƒO de filtro     tipo 2 passar como 0
        				 $scope.filtroListaF = function(xa=null,tipo=1,tipo2=0,tipo3=0){
 							if(xa!=null){
 								$scope.filtroLista = function (x) {
 		          					//console.log(x);
 		          					if(xa == "")
 		          						return x.ANITIPO == tipo || x.ANITIPO == tipo2 || x.ANITIPO == tipo3;
 		          					return (x.MINSITUACAO == xa || x.MINSITUACAO == xa) && x.ANITIPO == tipo ;
 		      				  	}
// 								$scope.filtroLista = {"MINSITUACAO" : 1,"ANITIPO": tipo};

								if(tipo==2 || tipo==3 || tipo==5){
									$("#animeEpTotal").html(tA[5]);return;
								}
								if(tipo==4){
									$("#animeEpTotal").html(tA[7]);return;
								}
								$("#animeEpTotal").html(tA[xa]);
							}	
    					}
    
    					$scope.filtroListaF(1); // DEFINI a LISTA para os anime com status de assistindo
    					$scope.camiiMen = response.data;
    					$scope.totalEpisodios = tA[0];	
        			}); 
				}
				exeListaAnime();
			
				$scope.reloadListaAnime = function($event){
					exeListaAnime();
				};
				
    			//##### Editar info anime na lista
  				$scope.editarAnimeLs = function(event) {
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

       				});
           			//## get Episodios DE um certo anime assistido
           			$http.get("./API/getEpAnimeList.php?anime="+anime).then(function(response){
						for(i=0; i<response.data.length;i++){
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
							exeListaAnime();
						}		
       				})
				}
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
					 	//console.log(epMax);
					 	if(epMax[1]==0){
					 		console.log(ep);
  					}
					if( (ep[1]+1) < epMax[1] || epMax[1]==0){ //arrumar
						$http.get("./API/save1ep.php?anime="+anime+"&ep="+(ep[1]+1)).then(function(response){
							if(response.data.sit ==1){
								//exeListaAnime();
								$(ep[0]).html(ep[1]+1);
							}
           				});
           				return;
					}

					if((ep[1]+1) == epMax[1]){
						$http.get("./API/save1epStatusCompleto.php?anime="+anime+"&ep="+(ep[1]+1)).then(function(response){
							if(response.data.sit ==1){
								//exeListaAnime();
								$(ep[0]).html(ep[1]+1);
							}
           				})
						return;
					}	
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
							$(event.target).addClass("d-none");
						}		
     				})
				}
				///######## FIM

				$scope.atualizarAnime = function(ani){
// 					alert(ani);
					$http.get("./API/updateAnimeMAL.php?n="+ani).then(function(r){
						console.log(r.data);
						if(r.data.sit == 1){
							alert("Anime Atualizado");
						}		
     				})
				}
				
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
        			$scope.sucessoMALNEW = "";
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
        						$scope.sucessoMALNEW = "";
								//console.log(event.target.value);
								var an = event.target.value;
								$http.get("./API/saveNewAnime.php?cod="+an).then(function(response){
									//console.log(response.data);
									if(response.data.sit == 1){
										$scope.sucessoMALNEW = "OK";
// 										alert(10);
										return;
									}	
									$scope.sucessoMALNEW = "ERROR";	
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
				$scope.getStats = function(event){
					$http.get("./API/getAnimeStats.php?anime=a").then(function(response){
						//console.log(response.data);
						// ### ANO
						$scope.statsAno = response.data.ANO;
						var anoTep = 0;
						for(i=0;i<response.data.ANO.length;i++){
							anoTep += parseInt(response.data.ANO[i].ANIME);
						}
						$scope.AnoEpTotal = anoTep;

						//### DIAS FALTADO
						var diaFinal =  365-response.data.META.DIA;
						if(response.data.META.B == "1" && response.data.META.DIA>59)
							diaFinal++;
						$scope.diafalta = diaFinal;
						var epT = response.data.ANO[0].ANIME;
						$scope.epAssistidos = epT;
						$scope.epTotal = 1000-epT;
						$scope.epDia = (1000-epT)/diaFinal;

						//#### ANOS COMPLETOS
						$scope.statsAnoComp = response.data.COMPLETO;
						var AC = 0,ACova = 0, ACfilme = 0;
						for(i=0;i<response.data.COMPLETO.length;i++){
							AC += parseInt(response.data.COMPLETO[i].ANIMECOMP);
						}
						for(i=0;i<response.data.COMPLETO.length;i++){
							ACova += parseInt(response.data.COMPLETO[i].OVA);
						}
						for(i=0;i<response.data.COMPLETO.length;i++){
							ACfilme += parseInt(response.data.COMPLETO[i].FILME);
						}
						$scope.totalCompleto = AC;
						$scope.OVAtotalCompleto = ACova;
						$scope.FILMEtotalCompleto = ACfilme;
						

						//### ANIME POR MES
						// ### ANO
						$scope.statsMes = response.data.MES;

						
						//FUNÃ‡ÃƒO de filtro
           				 $scope.filtroANOstatsFnc = function(x=null){
								
								//console.log(x.target);
 								if(typeof x.target === 'undefined' || x.target === null){
    								$scope.filtroANOstats = {"ANO" : x};
    								$scope.statANOmes = x
    								return
    							}
 								$scope.filtroANOstats = {"ANO" : x.target.value};
 								$scope.statANOmes = x.target.value;	
       					}

           				var d = new Date();
           			    var n = d.getFullYear();
           				$scope.filtroANOstatsFnc(n);
           				

           				
						
// 						var anoTep = 0;
// 						for(i=0;i<response.data.ANO.length;i++){
// 							anoTep += parseInt(response.data.ANO[i].ANIME);
// 						}
	     			})
				}
            }],
            templateUrl: './template/statsTL.html' 
		};
	}]);
	
	</script>
	<script src="./js/app/anime.js"></script>
<a href="#" onclick="history.pushState('teste','Titulo de teste','/testae'); return false;">/teste</a></h2>
</body>
</html>