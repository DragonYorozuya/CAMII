<html>

<head>
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
.borda{border: 1px solid}

.
</style>
<body>

<div class="container-fluid borda">
	<div class="row ">
	<div ng-app="CAMII" ng-controller="camiiCtr">
		
	
		<div class="col-md-12 col-xs-6 ">
			Busca: <input type="text" class="busca" ng-change="busca($event)" ng-model="ftxt"><br /><br />
			<span ng-repeat="x in bMen">
				{{ x.ANINOME }} <button value="{{ x.ANICOD }}" ng-click="addBTN($event)">add</button><br/>
			</span>
			
			<div class="boxMens" ng-repeat="x in camiiMen">{{ x.ANINOME }} {{ x.ANIEPI }}</div>
				
		</div>
		
		
	</div>	
	
	<script type="text/javascript">
	var app = angular.module('CAMII',['ngSanitize']);

	app.controller('camiiCtr', function($scope, $http){
		//#### GET ANIMES
		$http.get("./myListJSON.php").then(function(response){
			//console.log(response.data);
			for(i=0; i<response.data.length;i++){
				//console.log(response.data[i]);
				var tx = response.data[i];
				response.data[i].texto = tx;
			}
			$scope.camiiMen = response.data	;
		});

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
						response.data[i].texto = tx;
					}
					$scope.bMen = response.data	;
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
		
	});
	

	</script>
		
		
	
	</div>	
	
	
</div>


</body>
</html>