<?php
    /*
        Name: Anjali Patel
        Course: CS-174
        Assignment: Online Virus Check(Final)
		Description: This is the main page of the program I have implemented client-side validation
		using the lecture 17 slides. Which will validate user credentials and secure them in database
		when storing them in userInfo table.
    */
	require_once 'login.php';
	sql(); // calls the sql() function
	

	if(isset($_POST['username']) && $_POST['password'])
	{
		$username = get_post($conn, $_POST['username']);
		$password = get_post($conn, $_POST['password']);
	}
	/*
		javascript Client-side Validation (Used from Lecture 17)
	*/
	
	$fail = validate_username($username);
	$fail .= validate_password($password);

	echo "<!DOCTYPE html>\n<html><head><title>Online Virus Check</title";
	if($fail == ""){
		echo "</head><body>Form data successfully validate: $username, $password.</body></html>";
		passwordHash(); //enters the info in database with using hash encryption for password
		exit;
	}

	echo <<<_END
	<html><head>
		<style>
 			.signup {
   				border: 1px solid #999999;
   				font: normal 14px helvetica;
   				color:#444444;
 			}
	 	</style>
	 	<script>
			function validate(form)
			{
				fail = validateUsername(form.username.value)
				fail += validatePassword(form.password.value)
			
				if(fail == "")
				{
					return true
				}else{
					return false
				}
			}
			function validateUsername(field) 
			{
				if (field == "") {
					return "No Username was entered.\n"
				}else if (field.length < 5){
					return "Usernames must be at least 5 characters.\n"
				}else if (/[^a-zA-Z0-9_-]/.test(field)) {
					return "Only a-z, A-Z, 0-9, - and _ allowed in Usernames.\n"
				}
			   	return ""
			}
			function validatePassword(field) 
			{
				if (field == "") {
					return "No Password was entered"
				}else if (field.length < 6) {
					return "Passwords must be at least 6 characters"
				}else if (!/[a-z]/.test(field) || ! /[A-Z]/.test(field) || !/[0-9]/.test(field)){
			  	return "Passwords require one each of given char"
				}
				return ""
			}
		</script>
	</head>
	<body>
		<table border="0" cellpadding="2" cellspacing="5" bgcolor="#eeeeee">
			<th colspan="2" align="center">Signup Form</th>
				<tr><td colspan="2"> The following errors were found!<br>in your form: 
					<p><font color=red size=1><i>$fail</i></font></p>
				</td></tr>
			<form method="post" action="userFinal.php" onSubmit="return validate(this)">
				<tr>
					<td>Username</td>
					<td><input type="text" maxlength="16" name="username" value="$username"></td>
				</tr>
				<tr>
					<td>Password</td>
					<td><input type="text" maxlength="12" name="password" value="$password"› </td>
				</tr>
				<tr>
 					<td colspan="2" align="center"><input type="submit" value="Create account"></td>
				</tr>
			</form>
		</table>
_END;
	echo "</body></html>";
	//validate the username
	function validate_username($field){
		if($field == ""){
			return "No Username was entered.<br>";
		}
		else if($field.length < 5){
			return "Username must have at least 5 characters.<br>";
		}
		else if(preg_match('/[^a-zA-Z0-9_-]/', $field)){
			return "Only a-z, A-Z, 0-9, - and _ allowed in Usernames.<br>";
		}
		return "";
	}
	//validates the password
	function validate_password($field){
		if($field == ""){
			return "No password was entered.<br>";
		}
		else if($field.length < 6){
			return "Username must have at least 6 characters.<br>";
		}
		else if(!preg_match('/[a-z]/', $field) || !preg_match('/[A-Z]/', $field) ||
				!preg_match('/[0-9]/', $field)){
			return "Password requires one each of a-z, A-Z, and 0-9.<br>";
		}
		return "";
	}
	//enters the posted fields into database using the hash encryption
	function passwordHash()
	{
		$stmt = $conn->prepare('INSERT INTO userInfo VALUES'.'(?, ?, ?)');
		$salt = random();
		$hash = hash('ripemd128', "$salt$password");
		$stmt->bind_param('sss', $username, $hash, $salt);
		$stmt->execute();

		if($stmt->affected_rows == 0)die("Invalid input");
    }
	//admin login information
    echo <<<_END
	<html><body>
		<form action="userFinal.php" method="post"><pre>
		Admin Here!
		<input type="submit" value="Go Directly to Log in!" name="submit">
		</pre></form>
_END;
echo "</body></html>";

//access the MySQL database using hostname, username, password, database name
    function sql(){
        $conn = new mysqli($hn, $un, $pw, $db);
        if ($conn->connect_error) die($conn->connect_error);
    }
    function random()
	{
		$char = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@!';
		$randomString = '';

		for ($i = 0; $i < 8; $i++) 
		{
			$index = rand(0, strlen($char) - 1);
			$randomString .= $char[$index];
		}
		return $randomString;
    }
    //sanitizing strings and super global variables
    function sanitizeString($var) 
	{
		$var = stripslashes($var);
		$var = strip_tags($var);
		$var = htmlentities($var, ENT_QUOTES);
		return $var;
	}

	function get_post($conn, $var) 
	{
		$var = $conn->real_escape_string($var);
		$var = sanitizeString($var);
		return $var;
	}
	
    
    $result->close();
	$stmt->close();
	$conn->close();

?>