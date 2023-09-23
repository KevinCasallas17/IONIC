<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . './vendor/autoload.php';

$app = AppFactory::create();

function getDB(){
    $dbhost = "localhost";
    $dbname = "appkevincasallas";
    $dbuser = "root";
    $dbpass = "";
    $mysql_conn_string = "mysql:host=$dbhost;dbname=$dbname";
    $dbConnection = new PDO($mysql_conn_string, $dbuser, $dbpass);
    $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbConnection;
}

$app->get('/home', function ($request, $response, $args) {  //Defino los servicios
    try{
        $db =  getDB(); //Carga los datos
        $sth = $db->prepare("SELECT dataid, datNombre, datApellido,datEdad, datDeporte, datImagen from datos");//Consulta
        $sth->execute(); //Ejecutamos la consulta
        $test = $sth->fetchAll(PDO::FETCH_ASSOC);//Guardar los resultados de la consulta
        //Verificar si se ha cargado algo
if($test){
            $response->getBody()->write(json_encode($test)); //write Escribe la respuesta como texto, pero necesito un Json
            $db = null;//Cerrar la conexion con la base de datos
        }
        else{
            $response->getBody()->write('{"error":"error"}');
        }
}catch(PDOException $e){
            $response->getBody()->write('{"error":{"texto":'.$e->getMessage().'}}'); //En caso que se halla generado algún error
        }
    return $response
    ->withHeader('Content-Type', 'application/json');
});


$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello Kevin Casallas");
    return $response;
});

$app->post('/eliminar', function ($request, $response, $args) {  //Defino los servicios  $app->post('/updateVeces', function ($request, $response)
	try{
		$json = $request->getBody();
		$data = json_decode($json, true);
		$db =  getDB(); //Carga los datos
		$sth = $db->prepare("DELETE FROM datos
							 WHERE dataid = ?");//Insertamos información
$sth->execute(array($data['dataid'])); //Sustituimos y ejecutamos la consulta
		$response->getBody()->write('{"error":"ok"}'); //Cuando la conexión halla terminado
		
	}catch(PDOException $e){

			$response->getBody()->write('{"error":{"texto":'.$e->getMessage().'}}'); //En caso que se halla generado algún error
		}
return $response
    ->withHeader('Content-Type', 'application/json');
});


$app->run();