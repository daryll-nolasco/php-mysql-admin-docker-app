## Rest Application
 - Database: MySQL
 - PHP Framework: Slim

## Installation and setup
 - Update your docker file sharing setup. Make sure to add the path of the app in your file sharing list.
 
 - Build the app:
 	```
 	docker-compose up --build -d
	```
 - You will be able to access the api via http://localhost:8080/ link. But incase you get an error with the database host connection, update the dbhost config in the source/config/database.php with the ip address of the mysql docker container. run below command to check the ip. And rebuild after updating db config.
 	```
 	docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' <mysql-container-id>
	```

## API Details
 - Add new vehicle
	- link: /api/vehicle/add
	- method: POST
	- body: "JSON(application/json)"
	- Note: displacement_unit is required but will not be save in the database. This is just to check if a convertion is necessary inorder to have a uniquie unit of measurement which is set as L. This are the possible options "L / CC /CI" an below is the sample request data.
	```
	{
	    "name": "Hyundai Accent MT",
	    "displacement_unit": "CI",
	    "engine_displacement": "81",
	    "engine_power": "102"
	}
	```
 - Get the vehicle list:
	- link: /api/vehicles
	- method: GET
