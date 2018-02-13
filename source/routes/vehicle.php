<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

$app->options('/{routes:.+}', function($request, $response, $args) {
    return $response;
});

$app->add(function($request, $response, $next) {
    $response = $next($request, $response);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

/* Get The List of Vehicles */
$app->get('/api/vehicles', function(Request $request, Response $response) {
    // query to select all vehicles
    $query = "SELECT * FROM vehicle";

    try {
        // Get DB Object and connect
        $db = new Database();
        $db = $db->connect();

        // Query and fetch
        $stmt = $db->query($query);
        $vehicles = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        
        return $response->withStatus(200)
					    ->withHeader('Content-Type', 'application/json')
					    ->write(json_encode($vehicles));
    } catch(PDOException $e) {
    	$error = new \stdClass();
    	$error->messsage = $e->getMessage();
    	return $response->withStatus(500)
					    ->withHeader('Content-Type', 'application/json')
					    ->write(json_encode($error));
    }
});

/* Add New Vehicle Data */
$app->post('/api/vehicle/add', function(Request $request, Response $response) {
    $name = $request->getParam('name');
    $displacement_unit = $request->getParam('displacement_unit');
    $engine_displacement = $request->getParam('engine_displacement');
    $engine_power = $request->getParam('engine_power');

    /*
     * Check the displacement unit that was set and 
     * convert engine_displacement if necessary.
     * The value that will be save should be converted to liters
     * to have a unique pattern of measurement unit.
     */
    $unit = strtolower($displacement_unit);
    if ($unit === 'cc') {
	    $value = $engine_displacement * 0.001;
	} else if ($unit === 'ci') {
		$value = $engine_displacement * (1/61.02374409);
	} else {
		$value = $engine_displacement;
	}

    // format final displacement value
	$final_displacement = round($value, 1)."L";

    // query to insert record
    $query = "INSERT INTO vehicle (name, engine_displacement, engine_power) 
    		VALUES
    		  (:name, :engine_displacement, :engine_power)";
    
    try {
        // Get DB Object and connect
        $db = new Database();
        $db = $db->connect();

        // prepare query
        $stmt = $db->prepare($query);
        // bind parameters
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':engine_displacement', $final_displacement);
        $stmt->bindParam(':engine_power', $engine_power);

        $return = new \stdClass();
        if ($stmt->execute()) {
	        $status_code = 200;
	        $return->messsage = "Vehicle record added.";
		} else {
			$status_code = 400;
	        $return->messsage = "Unable to add vehicle.";
		}

		return $response->withStatus($status_code)
						->withHeader('Content-Type', 'application/json')
						->write(json_encode($return));
    } catch(PDOException $e) {
    	$error = new \stdClass();
        $error->messsage = $e->getMessage();
    	return $response->withStatus(500)
					    ->withHeader('Content-Type', 'application/json')
					    ->write(json_encode($error));
    }
});

$app->run();
