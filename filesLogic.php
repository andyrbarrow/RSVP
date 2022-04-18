<?php
require_once 'config.php';

date_default_timezone_set(TIME_ZONE);

// Link image type to correct image loader and saver
const IMAGE_HANDLERS = [
    IMAGETYPE_JPEG => [
        'load' => 'imagecreatefromjpeg',
        'save' => 'imagejpeg',
        'quality' => 100
    ],
    IMAGETYPE_PNG => [
        'load' => 'imagecreatefrompng',
        'save' => 'imagepng',
        'quality' => 0
    ],
    IMAGETYPE_GIF => [
        'load' => 'imagecreatefromgif',
        'save' => 'imagegif'
    ]
];

error_reporting(E_ALL ^ E_NOTICE);
// connect to the database
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
  }

// create table if it doesn't already exist
$sql = "CREATE TABLE IF NOT EXISTS `files` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `Events` varchar(255) COLLATE utf8_bin NOT NULL,
    `Classes` varchar(255) COLLATE utf8_bin NOT NULL,
    `Description` text COLLATE utf8_bin NOT NULL,
    `Date` date NOT NULL,
    `Time` time NOT NULL,
    `name` varchar(255) COLLATE utf8_bin NOT NULL,
    `size` int(11) NOT NULL,
    `downloads` int(11) NOT NULL,
    `Thumbnail` varchar(255) COLLATE utf8_bin NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `id` (`id`)
  ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";

if ($conn->query($sql) == TRUE) {
    echo '';
  } else {
    echo "Error creating files table: " . $conn->error;
  }

// create user table if it doesn't exist and pre-load it with one admin ID
$sql = "CREATE TABLE IF NOT EXISTS `users` (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `firstName` varchar(50) NOT NULL,
    `lastName` varchar(50) NOT NULL,
    `Email` varchar(255) NOT NULL,
    `Phone` varchar(255) NOT NULL,
    `userType` varchar(30) NOT NULL,
    `username` varchar(50) NOT NULL UNIQUE,
    `password` varchar(255) NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
  );";
if ($conn->query($sql) == TRUE) {
    // put code to insert first ADMIN id here
    $adminpasshash = password_hash('Admin', PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (firstName, lastName, Email, Phone, userType, username, password) VALUES ('Admin', 'Admin', 'admin@admin.adm', '123456789', 'admin', 'Admin', '$adminpasshash')";
    $result = mysqli_query($conn, $sql);
  } else {
    echo "Error creating users table: " . $conn->error;
  }

// if the uploads folder doesn't exist, create it
if (!is_dir(UPLOAD_FOLDER)){
    if (!(mkdir(UPLOAD_FOLDER, 0777))) {
        echo "Image folder could not be created";
    }
} 

// this dumps the entire database
$sql = "SELECT * FROM files";
// this creates a unique list of events in the database
$sqlevents = "SELECT DISTINCT Events FROM files ORDER BY Events ASC";
// this creates a unique list of classes in the database
$sqlclasses = "SELECT DISTINCT Classes FROM files ORDER BY Classes ASC";

$eventselection = '';
$classselection = '';

// if called from the downloads page, preset event and/or class
if (isset($_POST['eventselect'])) {
    $eventselection = $_POST['whichevent'];
    $classselection = $_POST['whichclass'];
}
// if called from a URL, preset event and class
if (isset($_GET['eventchoice'])) {
    $eventselection = $_GET['eventchoice'];
}
if (isset($_GET['classchoice'])) {
    $classselection = $_GET['classchoice'];
}

if ($eventselection === "" && $classselection === "") {
    $sql = "SELECT * FROM files";
    if (isset($_SESSION['eventsession'])) {
        unset($_SESSION['eventsession']);
    }
    if (isset($_SESSION['classsession'])) {
        unset($_SESSION['classsession']);
    }
} elseif ($eventselection ==! "" && $classselection === "") {
    $sql = "SELECT * FROM files WHERE Events = '$eventselection'";
    $_SESSION['eventsession'] = $eventselection;
    if (isset($_SESSION['classsession'])) {
        unset($_SESSION['classsession']);
    }
} elseif ($eventselection === "" && $classselection ==! "") {
    $sql = "SELECT * FROM files WHERE Classes = '$classselection'";
    $_SESSION['classsession'] = $classselection;
    if (isset($_SESSION['eventsession'])) {
        unset($_SESSION['eventsession']);
    }
} else {
    $sql = "SELECT * FROM files WHERE Events = '$eventselection' AND Classes = '$classselection'";
    $_SESSION['eventsession'] = $eventselection;
    $_SESSION['classsession'] = $classselection;
}

// build a permalink
$queryarray = array('eventchoice' => $eventselection, 'classchoice' => $classselection);
$permalink = '?'.http_build_query($queryarray);

$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
$files = mysqli_fetch_all(($result), MYSQLI_ASSOC);

if (isset($_GET['deleteitem'])) {
    $deleteid = $_GET['deleteitem'];
    $deletesql = "DELETE FROM files WHERE id = $deleteid";
    $result = mysqli_query($conn, $deletesql) or die(mysqli_error($conn));
}

// Uploads files
if (isset($_POST['save'])) { // if save button on the form is clicked
    // name of the uploaded file
    $filename = $_FILES['myfile']['name'];
    //we need to do something when apostrophies are included
    $description = str_replace("'","", $_POST["description"]);
    $events = str_replace("'","", $_POST['events']);
    $classes = str_replace("'","", $_POST['classes']);
    $date = $_POST['date'];
    $time = $_POST['time'];

    // get the file extension
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    
    // create a unique filename by adding EPOCH seconds, and create a name for the thumbnail
    $fOld  = pathinfo($filename);
    $filename = $fOld['filename'] . idate('U') . '.' . $fOld['extension'];
    $fNew = pathinfo($filename);
    $thumbname = $fNew['filename'] . '_thumb.' . $fNew['extension'];
    $thumbdest = UPLOAD_FOLDER . $thumbname;

    // destination of the file on the server
    $destination = UPLOAD_FOLDER . $filename;
    
    // the physical file on a temporary uploads directory on the server
    $file = $_FILES['myfile']['tmp_name'];
    $size = $_FILES['myfile']['size'];

    // check to make sure it is a valid image extension
    if (!in_array($extension, ['jpg', 'jpeg', 'gif', 'png'])) {
        $file_upload_err = "Your file extension must be .jpg, .jpeg, .gif, or .png";
        // check to make sure the file isn't too big
    } elseif ($_FILES['myfile']['size'] > 10000000) { # file shouldn't be larger than 10 Megabytes
        $file_upload_err = "File too large. Must be less than 10MB.";
    } else {
        // handle edge cases. If nothing is entered, enter "none"
        if ($events === NULL) {
            $events = "None";
        }
        if ($classes === NULL) {
            $classes = "None";
        }

        // move the uploaded (temporary) file to the specified destination
        if (move_uploaded_file($file, $destination)) {
            createThumbnail($destination, $thumbdest, THUMB_WIDTH, $targetHeight = null);
            $sql = "INSERT INTO files (name, size, downloads, Description, Events, Classes, Date, Time, Thumbnail) VALUES ('$filename', $size, 0, '$description', '$events', '$classes', '$date', '$time', '$thumbdest')";
            $result = mysqli_query($conn, $sql);
            if ($result) {
            ?>
                <div class="alert alert-info">
                  <strong>File Uploaded Successfully</strong>
                </div>
            <?php
            }
        } else {
            ?>
                <div class="alert alert-warning">
                    <strong>File Upload Failed</strong>
                </div>
            <?php
        }
    }
}

// Downloads files
if (isset($_GET['file_id'])) {
    $id = $_GET['file_id'];

    // fetch file to download from database
    $sql = "SELECT * FROM files WHERE id=$id";
    $result = mysqli_query($conn, $sql);

    $file = mysqli_fetch_assoc($result);
    $filepath = UPLOAD_FOLDER . $file['name'];

    if (file_exists($filepath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($filepath));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize(UPLOAD_FOLDER . $file['name']));
        readfile(UPLOAD_FOLDER . $file['name']);

        // Now update downloads count
        $newCount = $file['downloads'] + 1;
        $updateQuery = "UPDATE files SET downloads=$newCount WHERE id=$id";
        mysqli_query($conn, $updateQuery);
        exit;
    }
}

/**
 * this function creates thumbnail images
 * @param $src - a valid file location
 * @param $dest - a valid file target
 * @param $targetwidth - desired output width (set in config.php)
 * @param $targetHeight - desired output height or null
 */
function createThumbnail($src, $dest, $targetwidth, $targetHeight = null) {

    // 1. Load the image from the given $src
    // - see if the file actually exists
    // - check if it's of a valid image type
    // - load the image resource

    // get the type of the image
    // we need the type to determine the correct loader
    $type = exif_imagetype($src);

    // if no valid type or no handler found -> exit
    if (!$type || !IMAGE_HANDLERS[$type]) {
        return null;
    }

    // load the image with the correct loader
    $image = call_user_func(IMAGE_HANDLERS[$type]['load'], $src);

    // no image found at supplied location -> exit
    if (!$image) {
        return null;
    }


    // 2. Create a thumbnail and resize the loaded $image
    // - get the image dimensions
    // - define the output size appropriately
    // - create a thumbnail based on that size
    // - set alpha transparency for GIFs and PNGs
    // - draw the final thumbnail

    // get original image width and height
    $width = imagesx($image);
    $height = imagesy($image);

    // maintain aspect ratio when no height set
    if ($targetHeight == null) {

        // get width to height ratio
        $ratio = $width / $height;

        // if is portrait
        // use ratio to scale height to fit in square
        if ($width > $height) {
            $targetHeight = floor($targetwidth / $ratio);
        }
        // if is landscape
        // use ratio to scale width to fit in square
        else {
            $targetHeight = $targetwidth;
            $targetwidth = floor($targetwidth * $ratio);
        }
    }

    // create duplicate image based on calculated target size
    $thumbnail = imagecreatetruecolor($targetwidth, $targetHeight);

    // set transparency options for GIFs and PNGs
    if ($type == IMAGETYPE_GIF || $type == IMAGETYPE_PNG) {

        // make image transparent
        imagecolortransparent(
            $thumbnail,
            imagecolorallocate($thumbnail, 0, 0, 0)
        );

        // additional settings for PNGs
        if ($type == IMAGETYPE_PNG) {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
        }
    }

    // copy entire source image to duplicate image and resize
    imagecopyresampled(
        $thumbnail,
        $image,
        0, 0, 0, 0,
        $targetwidth, $targetHeight,
        $width, $height
    );

    // 3. Save the $thumbnail to disk
    // - call the correct save method
    // - set the correct quality level

    // save the duplicate version of the image to disk
    return call_user_func(
        IMAGE_HANDLERS[$type]['save'],
        $thumbnail,
        $dest,
        IMAGE_HANDLERS[$type]['quality']
    );
}
?>
