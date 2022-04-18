<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
// only let admins and uploaders see the upload page
if(($_SESSION["usertype"] === "admin") || ($_SESSION["usertype"] === "uploader")) {

include 'filesLogic.php';?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Files Upload and Download</title>
  </head>
  <body>
    <div class="wrapper">
    <h3>Upload File</h3>
    <p style="padding-right: 10px;">Upload Scoring File Images (jpg, jpeg, png, and gif allowed)</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" >
        <div class="form-group">
          <label>Description:</label>
          <input type="text" name="description" class="form-control"><br/>
        </div>
        <div class="form-group">
          <label>Event:</label>
          <input type="text" list="eventlist" name="events" id="eventchoice" autocomplete="on" class="form-control"><br/>
              <datalist id="eventlist">
              <?php 
              $allevents = mysqli_fetch_all(mysqli_query($conn, $sqlevents), MYSQLI_ASSOC);
              foreach ($allevents as $eventitem): 
                {
                $value = $eventitem['Events'];
                echo "<option value=\"$value\">$value</option>";
                }
              endforeach;
              ?>
              </datalist>
              </div>
        <div class="form-group">
          <label>Class:</label>
          <input type="text" list="classlist" name="classes" id="classes" autocomplete="on" class="form-control"><br/>
              <datalist id="classlist">
              <?php 
              $allclasses = mysqli_fetch_all(mysqli_query($conn, $sqlclasses), MYSQLI_ASSOC);
              foreach ($allclasses as $classes): 
                {
                $value = $classes['Classes'];
                echo "<option value=\"$value\">$value</option>";
                }
              endforeach;
              ?>
              </datalist>
        </div>
        <div class="form-group">
          <label>Date:</label>
          <input type="date" name="date" value="<?php echo date('Y-m-d');?>" class="form-control">
        </div>
        <div class="form-group">
          <label>Time:</label>
          <input type="time" name="time" value="<?php echo date('H:i:s');?>" class="form-control"> <br>
        </div>
        <div class="form-group">  
          <input type="file" name="myfile" class="form-control">
          <span class="invalid-feedback"><?php echo $file_upload_err; ?></span>
        </div>
        <div class="form-group">  
          <input type="submit" name="save"  class="btn btn-primary">
          <input type="reset" class="btn btn-secondary ml-2" >
        </div>
          <p><a href="downloads.php"><strong>View Uploaded Files</strong></a></p>
          <p><a href="reset-password.php"><strong>Change Your Password</strong></a></p>
          <p><a href="register.php"><strong>Create A New User</strong></p>
          <p><a href="logout.php"><strong>Logout</strong></p>
        </form>
    </div>
  </body>
</html>
<?php 
} else {
  // send viewers to the download page
  header("location: downloads.php");
  exit;
}
?>

