<?php

    /*
        Name: Anjali Patel
        Course: CS-174
        Assignment: Online Virus Check(Final)
        Description: In this program i am allowing the admin to upload infected(malware) file.
        Once they upload the file and the name of file in database then the program will read
        the first 20 bytes in the file for the malware. After that the result gets printed to
        admin the number of malware bytes and content name they wrote while uploading the file.
    */
    require_once 'login.php';
    
	sql(); // calls the sql() function
    session_start(); //starts the session
    //session security
    if(isset($_SESSION['initiated'])){
        session_regenerate_id();
        $_SESSION['initiated'] = 1;
    }
    if(isset($_SESSION['username']))
    {
        $username = $_SESSION['username'];
        destroy_session_and_data();
        upload();
    }
    else{
        echo "Please press the button to log in. <a href='userFinal.php'>click here</a>";
    }

    function upload(){
        echo <<<_END
    <html><body>
        <form action="adminUpload.php" method="post" enctype="multipart/form-data"><pre>
        Content Name <input type="text" name="content">
        Infected File <input type="file" name="malware" id="malware">
        <input type="submit" value="Add File" name="submit">
    </pre></form>
_END;
        if (isset($_POST['content']) && isset($_FILES['malware'])){
            $content = get_post($conn, $_POST['content']);
            $malware = get_post($conn, $_POST['malware']);
            $infectedFile = $_FILES['malware']['tmp_name'];
            $fp = fopen($infectedFile, 'r');
            $checkInfected = fread($fp, 20); //reads the first 20 bytes from the file uploaded
            fclose($fp);
            addFile($conn, $checkInfected, $content);
        }
        echo "</body></html>";
    }
    
    $query = "SELECT * FROM malwareStorage";
    $result = $conn->query($query);
    if(!$result) die("Error! Query didn't work");
    $rows = $result->num_rows;
    for ($i = 0; $i < $rows; $i++){
        $result->data_seek($i);
        $row = $result->fetch_array(MYSQLI_NUM);
        //printing the result to admin
        echo <<<_END
        <pre>
            Malware Bytes $row[0]
            Content Name $row[1]
        </pre>
_END;
    }
    
 //santizing variables and strings
    function sanitizeString($var){
        $var = stripslashes($var);
        $var = strip_tags($var);
        $var = htmlentities($var, ENT_QUOTES);
        return $var;
    }
   
    function get_post($conn, $var){
        $var = $conn->real_escape_string($var);
        $var = sanitizeString($var);
        return $var;
    }
    function get_file($conn, $var){
        return $conn->real_escape_string($_FILES[$var]);
    }
    //adds the infected file to database
    function addFile($conn, $checkInfected, $content){
        $stmt = $conn->prepare('INSERT INTO malwareStorage VALUES'.'(?,?)');
        $stmt->bind_param('ss', $checkInfected, $content);
        $stmt->execute();
        if($stmt->affected_rows == 0) die("Error! wasn't able to add");
    }

    //access the MySQL database using hostname, username, password, database name
    function sql(){
        $conn = new mysqli($hn, $un, $pw, $db);
        if ($conn->connect_error) die($conn->connect_error);
    }
    //to destroy session and earse data after use
    function destroy_session_and_data(){
        $_SESSION = array();
        setcookie(session_name(), '', time() - 2592000, '/');
        session_destroy();
    }
    $query->close();
    $result->close();
    $stmt->close();
    $conn->close();
?>