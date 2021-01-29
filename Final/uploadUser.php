<?php
    /*
        Name: Anjali Patel
        Course: CS-174
        Assignment: Online Virus Check(Final)
        Description: In this program it allows the user to upload the file and
        check the first 20 bytes to see if the file is infected or not. But first it checks
        to see if the user has logged in correctly else the session will send the user back 
        to login page. Even when the session is destroyed it will send the user back to login
        page. For this program I have used slides shown in class as well as few online 
        php tutorial resources for functions and byte reading which is mainly used in uploading file
        for user and admin:
            https://stackoverflow.com/questions/6529405/get-part-of-a-file-by-byte-number-in-php
    */
    require_once 'login.php';
    sql();
    //start session for the user uploading page
    session_start();
    //session security
    if(isset($_SESSION['initiated'])){
        session_regenerate_id();
        $_SESSION['initiated'] = 1;
    }
    if (isset($_SESSION['username'])){
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
        <form action="uploadUser.php" method="post" enctype="multipart/form-data"><pre>
        Putative Infected File: <input type="file" name="userupload" id="userupload">
        <input type="submit" value="Add File" name="submit">
        </pre></form>
_END;
    echo "</body></html>";
        //allowing the user to upload anytype of file
        if (isset($_FILES['userupload'])){
            $file = get_post($conn, $_POST['userupload']);
            $fileName = $_FILES['userupload']['tmp_name'];
            searchPutative($conn, $fileName);
        }
    }
    //searches for possible putative infected file that the user uploaded
    function searchPutative($conn, $fileName){
        $fp = fopen($fileName, 'r');
        $count = 0;
        for ($i = 0; $i < SEEK_END; $i++){
            fseek($fp, $i);
            $checkMalware = fread($fp, 20); //checks the first 20 bytes for uploaded file from user
            $stmt = $conn->prepare("SELECT * FROM malwareStorage WHERE malware=?");
            $stmt->bind_param('s', $checkMalware);
            $stmt->execute();
            $result = $stmt->get_result();
            if(!$result) die("Error! Query didn't work");
            if ($result->num_rows > 0){
                $count++;
                echo "The file is possibly infected!";
                break;
            }
        }
        if ($count == 0){
            echo "Passed! The file uploaded is not infected!";
        }
        fclose($fp);
    }

    //sanitizing variables
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
    //destorys/ends the running session and sends user back to login page
    function destroy_session_and_data(){
        $_SESSION = array();
        setcookie(session_name(), '', time() - 2592000, '/');
        session_destroy();
    }

    //access the MySQL database using hostname, username, password, database name
    function sql(){
        $conn = new mysqli($hn, $un, $pw, $db);
        if ($conn->connect_error) die($conn->connect_error);
    }
    $result->close();
    $stmt->close();
    $conn->close();
?>