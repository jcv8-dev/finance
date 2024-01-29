<?php
require_once "assets/scripts/helper.php";

header("Cache-Control: no-cache, must-revalidate");

if(isset($_POST["submit"])) {
    if(!file_exists("assets/scripts/upload/secrets.php")){
        if(move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], "assets/scripts/upload/secrets.php")){
            echo "moved";
        } else {
            echo "failed";
        }

    }
}

if(file_exists("assets/scripts/upload/secrets.php")){
    header("Location: login");
    echo " setup redir login";
}

?>

<html>
<head>
    <?php head("Finance - Setup"); ?>
</head>
<body>
<div class="container">
<h1>Setup</h1>
    <form action="setup.php" method="post" enctype="multipart/form-data">
        Select file to upload:
        <input type="file" name="fileToUpload" id="fileToUpload">
        <input type="submit" value="Upload secrets.php" name="submit">
    </form>

</div>
</body>
</html>
