<?php
require 'db.php';

if(isset($_POST['text'])){
    $msg = mysqli_real_escape_string($conn, $_POST['text']);

    $query = "SELECT response FROM bot_questions WHERE keyword LIKE '%$msg%'";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
        echo $row['response'];
    } else {
        mysqli_query($conn, "INSERT INTO chat_history (sender, message) VALUES ('user', '$msg')");
        echo "يا هلا بك، حالياً ما عندي إجابة دقيقة.. بس أبشري، حولت سؤالك للموظفة وبترد عليك الحين! 👩‍💻";
    }
}
?>