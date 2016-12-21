<?php
/**
 * The page is intended to be used in an <iframe> within the CKEditor image
 * upload plugin in order to support user uploads. This allows an upload to work
 * while the editor and dialog need not refresh. Ie it is an attempted solution
 * to file uploads that avoid a full page refresh.
 */

@session_start();
require_once('db.php');

$conx = mysql_connect($host,$user,$pass);
mysql_select_db($main_db, $conx);

function db_quote($s) {
    return "'".mysql_real_escape_string($s)."'";
}

function get_logged_in() {
	if (isset($_SESSION['logged'])) {
        $logd = db_quote($_SESSION['logged']);
		$sql  = "SELECT * FROM users
                 WHERE username = $logd";
		$res  = mysql_query($sql);
        if (mysql_num_rows($res) > 0)
            return mysql_fetch_assoc($res);
	}
    return null;
}

$user = get_logged_in();
if ($user == null)
    die('You must log in to upload images');


function file_type_ok($file) {
    $type = $file['type'];
    $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    return
        ($type == 'image/jpeg' and ($ext == 'jpg' or $ext == 'jpeg')) or
        ($type == 'image/gif' and $ext == 'gif') or
        ($type == 'image/png' and $ext == 'png');
}

function make_path($filename) {
    $path = 'files/images/'.$filename[0].'/'.$filename[1].'/'.$filename;
    return $path;
}

function generate_path($orig_name) {
    $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
    do {
        $fn   = sha1(time().$orig_name).'.'.$ext;
        $path = make_path($fn);
    } while (is_file($path));
    return $path;
}

function ensure_path_to($path) {
    $hier = explode('/', $path);
    array_pop($hier);
    $cur  = '';
    foreach ($hier as $part) {
        $cur .= $part.'/';
        if (!is_dir('./'.$cur))
            mkdir('./'.$cur);
    }
}

function create_file_entry($file, $path) {
    global $user;
    switch ($file['type']) {
    case 'image/jpeg':
        $img = imagecreatefromjpeg($path); break;
    case 'image/gif':
        $img = imagecreatefromgif($path); break;
    case 'image/png':
        $img = imagecreatefrompng($path); break;
    default:
        return null;
    }
    if (!$img)
        return null;
    $width = imagesx($img);
    $height = imagesy($img);

    $userid = intval($user['id']);
    $dname  = db_quote($file['name']);
    $fname  = db_quote(basename($path));
    $fsize  = (int) filesize($path);
    $sql    = "INSERT INTO file_uploads (userId, display_name, filename, size, width, height)
               VALUES ($userid, $dname, $fname, $fsize, $width, $height)";
    if (mysql_query($sql)) {
        $sql = "SELECT id FROM file_uploads
                WHERE userId   = $userid
                  AND filename = $fname
                ORDER BY whence DESC
                LIMIT 1";
        $res = mysql_query($sql);
        if (mysql_num_rows($res) > 0) {
            $row = mysql_fetch_assoc($res);
            return intval($row['id']);
        }
    }
    return null;
}


if (isset($_FILES['image'])) {
    $file  = $_FILES['image'];
    if (file_type_ok($file)) {
        $path  = generate_path($file['name']);
        ensure_path_to($path);
        if (move_uploaded_file($file['tmp_name'], $path)) {
            $id = create_file_entry($file, $path);
            if ($id !== null) {
                header('Location: image_upload_frame.php?path='.$url.$path.
                       '&width='.$width.'&height='.$height);
                die();
            }
            else
                $error = 'Could not save the image';
        }
        else
            $error = 'Could not save the image';
    }
    else
        $error = 'Only image files are allowed';
    unset($_GET['path']);
}


// Get the uploaded images thus far
$sql = 'SELECT * FROM `file_uploads`
        WHERE userId = '.intval($user['id']).'
        ORDER BY whence DESC';
$res = mysql_query($sql);
$uploads = array();
while ($row = mysql_fetch_assoc($res))
    $uploads[] = $row;
?>
<html>
<head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
        var form = $('form').first();
        var img = form.find('[name="image"]');
        img.change(function() {
            var exts = /(.jpg|.jpeg|.png|.gif)$/i;
            if (exts.test(img.val())) {
                $('#error').fadeOut(100);
                img.hide();
                $('#max').hide();
                $('#upl').fadeIn();
                form[0].submit();
            }
            else {
                $('#max').fadeIn(150);
                $('#error').fadeIn(150);
            }
        });
        form.show();
    });
    function toggleView() {
      var s = $('#uploader').css('display') == 'none';
      $('#uploader')[s ? 'show' : 'hide']();
      $('#browser')[s ? 'hide' : 'show']();
    }
  </script>
  <style type="text/css">
    body {
      font-family: sans-serif;
      margin: 0;
      padding: 0;
      width: 100%:
    }
    #max {
      color: #666;
      font-size: 12px;
      margin-bottom: 5px;;
    }
    #upl {
      display: none;
      font-size: 14px;
    }
    #max {
      margin-top: 3px;
    }
    #error {
      color: #c00;
      display: none;
      font-size: 14px;
    }
    #browser {
      display: none;
    }
    #browser h4 {
      margin-bottom: 5px;
    }
    #browser ul {
      list-style-type: none;
      margin: 0;
      padding: 0;
      text-indent: 0;
    }
    #browser li {
      display: inline;
    }
    #browser li a {
      text-decoration: none;
      vertical-align: middle;
    }
    #browser li a img {
      border: none;
    }
    #browser li a span {
      vertical-align: middle;
    }
  </style>
</head>
<body>
  <?php
  if (isset($_GET['path'])) {
      echo '<div style="display: none;" id="path">'.$_GET['path'].'</div>';
      echo '<div style="display: none;" id="width">'.$_GET['width'].'</div>';
      echo '<div style="display: none;" id="height">'.$_GET['height'].'</div>';
  }
  ?>
  <div id="uploader">
    <h4>New Image</h4>
    <form action="" method="post" enctype="multipart/form-data">
      <input type="file" name="image"/>
      <noscript>
        <br/>
        <input type="submit" value="Upload"/>
      </noscript>
      <div id="max">
        <?php
           $max_upload = (int)(ini_get('upload_max_filesize'));
           $max_post = (int)(ini_get('post_max_size'));
           $memory_limit = (int)(ini_get('memory_limit'));
           $upload_mb = min($max_upload, $max_post, $memory_limit);
           echo "(Maximum $upload_mb MB)";
        ?>
      </div>
    </form>
    <div id="upl"><b>Uploading... please wait</b></div>
    <b id="error"><?php echo (isset($error) ? $error : 'Only images are allowed'); ?></b>
    <hr/>
    <?php if (count($uploads) > 0) { ?>
       <button type="button" onclick="toggleView();">See Gallery</button>
    <?php } ?>
  </div>
  <div id="browser">
    <h4>Your Uploads</h4>
    <button type="button" onclick="toggleView();">or Upload Image</button>
    <hr/>
    <ul>
      <?php
         foreach ($uploads as $img) {
             $path = $url.make_path($img['filename']);
             echo '<li><a href="?path='.$path.'&width='.$img['width'].'&height='.$img['height'].'">
                     <img src="'.$path.'" title="'.$img['display_name'].'"
                       width="'.min(intval($img['width']), 125).'"/>
                   </a></li>';
         }
      ?>
    </ul>
  </div>
</body>
</html>
