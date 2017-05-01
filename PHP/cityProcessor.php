//****************************************************************************
// Programmar: Dawood Khan
// 
// Description: This php file opens a data base connection, takes in input 
// from an html form, validates it, adds it to the database, and then outputs
// everything in the database. 
// This program uses styles.css for styling certain elements.
//****************************************************************************
<?php
// link to CSS file for formatting
echo '<link href="styles.css" rel="stylesheet">';
// attempt to connect to database
$servername = "localhost";
$username = "Khan";
$password = "";
$databaseName = "city_db";
$con = mysqli_connect($servername, $username, $password, $databaseName);
// if the connection failed output error meg. if succeeded also output msg
if (!$con) {
	die ("Connection error: " . mysqli_connect_errno() . " info: " . 
		mysqli_connect_error());
} else {
	echo "<font color='green'> Connection successful!</font><br>"; 
}

// define the variables and set them to empty values 
$cityNameErr = $stateErr = $populationErr = ""; // for error msgs
$cityName = $state = $population = ""; // for storing data fields in
$inputValid = true; // assume all the input is valid
// checking if the method of passing data is post, then 
// process the data and put them into variables
// double check it is passed in through POST
if ($_SERVER["REQUEST_METHOD"] == "POST") { 
	// if the required information is not entered, initialize
	// appropriate error messages
	// if empty output message
	if(empty($_POST["cityName"])){
		$cityNameErr = "City is Required.";	
		echo $cityNameErr . "<br>";
		$inputValid = false;
	}
	else { // after cleaning input check if it is valid CITY FIELD
		$cityName = cleanInput($_POST["cityName"]);

		if( !(validateLocationName($cityName)) ){
			echo "City Name must only contain alpha and space characters." + 
				" <br>";
			$inputValid = false;
		}
	}
	// if empty output message for STATE FIELD
	if(empty($_POST["state"])){
		$stateErr = "State is Required.";
		echo $cityNameErr . "<br>";
		$inputValid = false;
	}
	else { // after cleaning input check if valid
		$state = cleanInput($_POST["state"]);
		if( !(validateLocationName($state)) ){
			echo "State Name must only contain alpha and space characters." + 
				"<br>";
			$inputValid = false;
		}
	}
	// if empty output message for POPULATION FIELD
	if(empty($_POST["population"])){
		$populationErr = "Population is Required.";
		echo $populationErr . "<br>";
		$inputValid = false;
	}
	else { // after cleaning input check if valid
		$population = cleanInput($_POST["population"]);
		if( !(validatePopulation($population)) ){
			echo "Population must only contain numbers. <br>";
			$inputValid = false;
		}
	}
}

// Next depending if the data is valid, add it to the database
if($inputValid){ // add information to data base
	// make the squl statement by choosing table to insert into with the right
	// fields and values
	$sql = "INSERT INTO us_city_tb (city_name, population, state_name)
	VALUES ('$cityName', '$population', '$state')";

	// if the query was successful state a message as well as otherwise
	if ($con->query($sql) === TRUE) {
    	echo "New record created successfully<br>";
	} 	
	else {
    	echo "Error: " . $sql . "<br>" . $con->error . "<br>";
	}

	// next we want to output the contents of the data base
	echo "Contents of the database: <br><br>";
	// making the sql command selecting all the field names from the table
	$sql = "SELECT city_id, city_name, state_name, population FROM us_city_tb";
	$result = $con->query($sql);// querying all the data we need

	if($result->num_rows > 0){ // as long as there are records to process
		// this code is making a table based on the field names
		echo "<table ><tr><th> City ID </th><th> City Name </th><th> 
			State </th><th> Population </th></tr>";

		// will process row by row or record by record
		while($row = $result->fetch_assoc()){
			// what this code is doing is defining a table to put all the 
			// values in each record into a html table
			echo "<tr><td>" . $row["city_id"] . "</td><td>" . $row["city_name"]
				. "</td><td>" . $row["state_name"] . "</td><td>" . 
				$row["population"] . "</td></tr>";
		}
		echo "</table>";// end the table after processing a row
	}
	else {
		echo "0 results.<br>";
	}

}
else { // if not valid state an error message
	echo "Do to the error(s) above, the information has not been entered" +
		"into the database.<br>";
}

$con->close(); 			// close connection to database

//****************************************************************************
// Function Name: validate_input
// 
// Description: This function takes in a variable and takes away leading and 
// trailing whitespaces, slashes, and special html characters.
// 
// Precondition: $data must be declared and initialized
//
// PostCondition: returns $data with extra characters removed
//****************************************************************************
function cleanInput($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);

	return $data;
}

//****************************************************************************
// Function Name: validate_location_name
// 
// Description: This function takes in a city or state name and checks if 
// there an any invalid characters: anything besides letters and spaces
// 
// Precondition: $location must be declared and initialized
//
// PostCondition: returns true if it is valid, false otherwise
//****************************************************************************
function validateLocationName($location){

	$isValid = true; 		// assume the input is valid
	$locationLength = strlen($location);// get length of string

	for($i = 0; $i < $locationLength && $isValid; $i++){
		if( !(ctype_alpha($location[$i])) && !(ctype_space($location[$i])) ){
// if the character is not a space or an alpha character, then not valid
			$isValid = false; 
		}
	}

	return $isValid; // return if the name is valid or not
}


//****************************************************************************
// Function Name: validate_population
// 
// Description: This function takes in an integer or string and it makes sure 
// it is only integers and no other characters.
// 
// Precondition: $population must be declared and initialized
//
// PostCondition: returns true if it is valid, false otherwise
//****************************************************************************
function validatePopulation($population){
	// return if the name is valid or not based on if all the 
	// characters are digits or valid exponential part and pos.
	return (is_numeric($population) && $population >= 0); 
}
?>
