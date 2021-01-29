<?php
	/*
        Name: Anjali Patel
        Course: CS-174
        Assignment: Online Virus Check(Final)
        Description: In this program it allows the user and admin to sign in using their
        respective credentials. I have also implemented Sessions in this program. For user
        their credentials will be stored in userInfo table when they sign up and can be accessed
        from that table. For admin login I pre-saved the admin username and password in adminInfo
        table. This program gets the admin login credentials from the table directly. 
    */
    require_once 'login.php';
    
    sql(); // calls the sql() function
    //user login
	echo <<<_END
	<html><body>
		<form action="userFinal.php" method="post"><pre>
		User Login
		Username <input type="text" name="loginun">
		Password <input type="text" name="loginpswd">
        <input type="submit" value="Log In" name="submit">
		</pre></form>
_END;
    echo "</body></html>";
    if(isset($_POST['loginun']) && isset($_POST['loginpswd'])){
        $username = get_post($conn,$_POST['loginun']);
        $password = get_post($conn,$_POST['loginpswd']);
        //credentials saved when signing up
        $stmt = $conn->prepare('SELECT * FROM userInfo WHERE username=?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if(!$result) die("Invalid input");
        if ($result->num_rows > 0){
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $salt = $row["salt"];
            $hash = hash('ripemd128', "$salt$password");
            if($hash == $row["password"]){
				session_start(); //start session
                $_SESSION['username'] = $username;
                $_SESSION['password'] = $password;
					userStart(); //takes user to uploading page
            }
            else{
                echo "Invalid user credentials (username or password)!";
            }
        }
        else{
            echo "Invalid user credentials (username or password)!";
        }
	}
	function userStart(){
		echo <<<_END
	<html><body>
		<form action="uploadUser.php" method="post"><pre>
        <input type="submit" value="User Upload" name="submit">
		</pre></form>
	_END;
    	echo "</body></html>"
	}

	//admin login
	echo <<<_END
	<html><body>
		<form action="userFinal.php" method="post"><pre>
		Admin Login
		Username <input type="text" name="loginun">
		Password <input type="text" name="loginpswd">
        <input type="submit" value="sign in" name="submit">
		</pre></form>
_END;
    echo "</body></html>";
    if(isset($_POST['loginun']) && isset($_POST['loginpswd'])){
        $username = get_post($conn,$_POST['loginun']);
        $password = get_post($conn,$_POST['loginpswd']);
        //pre-save the admin credentials in the adminInfo table 
        $stmt = $conn->prepare("SELECT * FROM adminInfo WHERE username=?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if(!$result) die("Invalid input");
        if ($result->num_rows > 0){
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $salt = $row["salt"];
            $hash = hash('ripemd128', "$salt$password");
            if($hash == $row[1]){
                session_start();
                $_SESSION['username'] = $username; //row[0] stores the username
                $_SESSION['password'] = $password; //row[1] stores the password in adminInfo table
                    adminStart(); //takes the admin to their uploading page
            }
            else{
                echo "Invalid Admin credentials (username or password)!";
            }
        }
        else{
            echo "Invalid Admin credentials (username or password)!";
        }
	}
    
    function adminStart(){
		echo <<<_END
	<html><body>
		<form action="adminUpload.php" method="post"><pre>
        <input type="submit" value="Admin Upload" name="submit">
		</pre></form>
	_END;
    	echo "</body></html>"
    }
    
	function sql(){
        $conn = new mysqli($hn, $un, $pw, $db);
        if ($conn->connect_error) die($conn->connect_error);
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