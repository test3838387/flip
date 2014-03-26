<?php
//Include credentials file for MySQL (not in git repository)
require("mysql.php");

//Line break constant (combines a php line break and an html line break)
const br = "<br />\n";




/*
The SQL class will create a connection to the database and provide numerous functions to
access, create, modify, and delete data from the mysql database. WARNING: ANY public function
that queries the database MUST use mysql_real_escape_string(). Otherwise, the function MUST
be declared as private! Public functions must escape the values using the
following method (and format):
	$var1 = mysql_real_escape_string($var1);
This should be done IMMEDIATELY so that there will be no chance of accidentally passing an
unescaped value to a private function. Passing unescaped values into the database will enable
people to take control of our database and delete all of our data. ANY variable data must be 
escaped before being passed through mysql_query().

Return values for public methods:
	 1 or OBJECT if success
	-1 if username is taken
	-2 if email is taken
	-3 if username/password returned duplicates
	-4 if login failed, username existed & faultyLoginAttempts was updated
	-5 if login failed, username doesn't exist &/ faultyLoginAttempts wasn't updated
	
Return values for private methods (common):
	true    if success (or if something exists)
	false   if failure (or if something doesn't exist)
*/
class SQL {
	private $link = null; //SQL Connection
	const alphabet = "0123456789abcdef"; //Hex alphabet
	
	function SQL() //Constructor: Create connection to database
	{
		$link = mysql_connect(MYSQL_SERVER, MYSQL_USERNAME, MYSQL_PASSWORD);
		if (!$link) die ("Mysql: Connect failure");
		mysql_select_db(MYSQL_DATABASE);
	}
	
	function __destruct() //Destructor: Close connection to database
	{
		mysql_close();
	}
	
	function login($username, $password) //Authenticates a user (if proper credentials are used)
	{
		//Escape the values
		$username = mysql_real_escape_string($username);
		$password = mysql_real_escape_string($password);
		
		//Check to see if the username/password combo exists
		$query = mysql_query("SELECT id, accountType, username, email, faultyLoginAttempts FROM account WHERE username = '{$username}' AND password = '{$password}';");
		if (!$query) die ("Mysql: Query Failure");
		
		//Get results and set to $result
		$result = mysql_fetch_assoc($query);
		
		//Get number of rows and set to $numRows
		$numRows = mysql_num_rows($query);
		
		//Ensure that there is no more than one result, otherwise return error -3
		if ($numRows > 1)
			return -3;
			
		//Update faulty login attempts. If successful, return -4. Otherwise, return -5
		if ($numRows == 0)
			return (updateFaultyLoginAttempts($username) ? -4 : -5);
			
		//Create AU and return it
		return new AU($result['id'], $result['username'], $result['accountType'], $result['email']);
		
	}

	function createAccount($username, $isAdmin, $password, $email) //Adds a user to the account table in the DB
	{
		//Escape the values
		$username = mysql_real_escape_string($username);
		$isAdmin = mysql_real_escape_string($isAdmin);
		$password = mysql_real_escape_string($password);
		$email = mysql_real_escape_string($email);
		
		//Encrypt password
		$password = crypt($password, "");
		
		//Ensure that the username is not taken
		if ($this->usernameExists($username)) return -1;

		//Ensure that the email address is not taken
		if ($this->emailExists($email)) return -2;
		
		//Generate other information
		$userID = $this->newID(0); //UserID
		date_default_timezone_set("America/Chicago");
		$currentDate = date("Y-m-d H:i:s"); //Current time
		
		//Insert the user into the database
		mysql_query("INSERT INTO `account` VALUES ('{$userID}', {$isAdmin}, '{$username}', '{$password}', '{$email}', '0000-00-00 00:00:00', 0, 0, '', '{$currentDate}');");
		
		//Ensure that the user was created
		if ($this->usernameExists($username)) return new AU($userID, $username, $isAdmin, $email);
		else return 0;	
	}
	
	private function updateFaultyLoginAttempts($username) //Updates the faultyLoginAttempts for a user (if exists)
	{
		//If username doesn't exist, return false
		if (!usernameExists($username))
			return false;
			
		$query = mysql_query("UPDATE account SET faultyLoginAttempts = faultyLoginAttempts + 1 WHERE id = '{$username}';");
		if (!$query) die ("Mysql: Query Failure");
		return true;
	}
	
	private function usernameExists($username) //Returns true if username is associated with a user, false otherwise
	{
		$query = mysql_query("SELECT EXISTS(SELECT 1 FROM account WHERE username = '{$username}');");
		if (!$query) die ("Mysql: Query Failure");
		return (mysql_result($query, 0) == 0 ? false : true);
	}
	
	private function emailExists($email) //Returns true if email is associated with a user, false otherwise
	{
		$query = mysql_query("SELECT EXISTS(SELECT 1 FROM account WHERE email = '{$email}');");
		if (!$query) die ("Mysql: Query Failure");
		
		return (mysql_result($query, 0) == 0 ? false : true);
	}

	private function idExists($type, $id) //Returns true if id is associated with something, false otherwise
	{
		if ($type == 0)
			$table = "account";
		elseif ($type == 1)
			$table = "set";
		elseif ($type ==2)
			$table = "flashcards";
			
		$query = mysql_query("SELECT EXISTS(SELECT 1 FROM {$table} WHERE id = '{$id}');");
		if (!$query) die ("Mysql: Query Failure");
		
		return (mysql_result($query, 0) == 0 ? false : true);
	}
	
	function newID($type) //Generates a random ID and ensures its unique
	{
		$newID = "";
		$length = 0;
		
		if ($type == 0) //If User ID
			$length = 10;
		elseif ($type == 1) //If Set ID
			$length = 15;
		elseif ($type == 2) //If Flashcard ID
			$length = 20;
		
		for ($i = 0; $i < $length; $i++)
			$newID .= substr(self::alphabet, rand(0, 15), 1);
		
		//Check to see if the ID already exists. If it has a match, 
		//call newID() again and set $newID equal to its return. Otherwise, $newID ramains the same
		return ($this->idExists($type, $newID) ? $this->newID($type) : $newID);
	}
	
	function salt() //Generates a SHA-256 (16 digit) salt
	{
		
	}
	
}

class Account {
	//Variables
	protected $userID;	
	protected $username;
	protected $isAdmin;
	protected $email;
}

class AU extends Account {
	
	//Constructors
	function AU($userID, $username, $isAdmin, $email)
	{
		$this->userID = $userID;
		$this->username = $username;
		$this->isAdmin = $isAdmin;
		$this->email = $email;
	}
	
	//Getters
	function getUsername(){
		return $this->username;
	}
}

$sql = new SQL();
$au = $sql->createAccount("test", 0, "pwd", "email@addr.com");
if ($au instanceof AU) echo $au->getUsername(); else echo "not an AU";
?>