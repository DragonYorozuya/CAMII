app.directive('animeinfo', ['$http', function($http) { 
		//### GET ANIME LISTA
        return {
            restrict: 'AE',
            replace: true,
            transclude: true,
            scope: false,
            controller: ['$scope', function m($scope) {

				var txt = 10162;
            	$http.get("./API/anime/anime.php?cod="+txt).then(function(r){
					console.log(r.data);

					$scope.nome = r.data.ANINOME;
					if(r.data != ""){
    					//console.log(response.data.categories[0].items.length);
    					
					}
					
				});


            	//## MAL
//         		var time;
//         		$scope.buscaMal = function (event) {
//         			$scope.sucessoMALNEW = "";
//         			clearTimeout(time);
//         			time = setTimeout(function(){
//         				var txt = $(".buscaMal").val();
//         				$http.get("./API/MALanimeSearch.php?search="+txt).then(function(response){
//         					//console.log(response.data);
        					
//         					if(response.data != ""){
//             					//console.log(response.data.categories[0].items.length);
//             					for(i=0; i<response.data.categories[0].items.length;i++){
//             						//console.log(response.data.categories[1].items[i]);
//             						var tx = response.data.categories[0].items[i];
//             						response.data[i] = tx;
//             					}
//         					}
//         					$scope.malMen = response.data;
// 							//### ADD novo anime evento
//         					$scope.addNewAnimeBTN = function(event) {
//         						$scope.sucessoMALNEW = "";
// 								//console.log(event.target.value);
// 								var an = event.target.value;
// 								$http.get("./API/saveNewAnime.php?cod="+an).then(function(response){
// 									//console.log(response.data);
// 									if(response.data.sit == 1){
// 										$scope.sucessoMALNEW = "OK";
// // 										alert(10);
// 										return;
// 									}	
// 									$scope.sucessoMALNEW = "ERROR";	
// 								});
// 							}	
//         				});
//         			},1000);
//         		}




            }],
            templateUrl: './template/animeInfo.html'
		};
	}])