<?php 
session_start();
require 'filesLogic.php';?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"/>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="style.css">
  <title>View Images</title>
</head>
<body>
<h2 style="margin-left: 20px;">Display Raw Scoring Images</h2>
<form class = "download-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" >
  <?php
    //if there is a session cookie for the event, use that
    if (isset($_SESSION['eventsession'])) {
      $allevents = mysqli_fetch_all(mysqli_query($conn, $sqlevents), MYSQLI_ASSOC);?>
      <div>
      <label for="whichevent"><strong>Event:</strong></label>
      <select id="whichevent" name="whichevent">
      <?php 
      $eventprechoice = $_SESSION['eventsession'];
      echo '<option value = "'.$eventprechoice.'">'.$eventprechoice.'</option>';?>
      <option value = "">--All Events--</options>
        <?php
          foreach ($allevents as $eventitem): 
            {
            $value = $eventitem['Events'];
            echo '<option value="'.$value.'">'.$value.'</option>';
            }
          endforeach;
      ?> </select>
      </div>
      <?php } else {
      // otherwise, set for all events
      $allevents = mysqli_fetch_all(mysqli_query($conn, $sqlevents), MYSQLI_ASSOC);?>
      <div>
      <label for="whichevent"><strong>Event:</strong></label>
      <select id="whichevent" name="whichevent">
      <option value = "">--All Events--</options>
        <?php
          foreach ($allevents as $eventitem): 
            {
            $value = $eventitem['Events'];
            echo "<option value=\"$value\">$value</option>";
            }
          endforeach;
          ?>
      </select>
        </div>
      <?php }
      // if there is a session cookie for the class, use it
      if (isset($_SESSION['classsession'])) {
      $allclasses = mysqli_fetch_all(mysqli_query($conn, $sqlclasses), MYSQLI_ASSOC);?>
      <div>
      <label for="Classevent"><strong>Class:</strong></label>
      <select id="whichclass" name="whichclass">
      <?php
      $classprechoice = $_SESSION['classsession'];
      echo '<option value = "'.$classprechoice.'">'.$classprechoice.'</option>';?>
      <option value = "">--All Classes--</options>
        <?php
          foreach ($allclasses as $classes): 
            {
            $value = $classes['Classes'];
            echo '<option value="'.$value.'">'.$value.'</option>';
            }
          endforeach;
        echo "</select> </div>";
      } else {
      // otherwise, set for all classes
      $allclasses = mysqli_fetch_all(mysqli_query($conn, $sqlclasses), MYSQLI_ASSOC);?>
      <div>
      <label for="Classevent"><strong>Class:</strong></label>
      <select id="whichclass" name="whichclass">?>
      <option value = "">--All Classes--</options>
        <?php
          foreach ($allclasses as $classes): 
            {
            $value = $classes['Classes'];
            echo '<option value="'.$value.'">'.$value.'</option>';
            }
          endforeach;
        echo "</select>";
    } ?>
    </div>
    <div style="margin-left: 70px; margin-bottom: 20px;">
      <input type="submit" name="eventselect">
    </div>
  <p><a href="index.php">Return to image upload</a></p><p> <a href="<?php echo $permalink;?>">Permalink</a></p>
</form>
<table class = "download-form">
<thead>
  <?php if(isset($_SESSION["usertype"]) && ($_SESSION["usertype"] === "admin")) {
    echo "<th> </th>";
  }
  ?>
    <th>ID</th>
    <th>Event</th>
    <th>Class</th>
    <th>Description</th>
    <th>Date<br/>Time</th>
    <th>Download</th>
</thead>
<tbody>
  <?php
    foreach ($files as $file): ?>
    <tr>
      <?php if(isset($_SESSION["usertype"]) && ($_SESSION["usertype"] === "admin")) {?>
        <td><a href="<?php echo '?deleteitem='.$file['id'];?>">Delete</a>
      <?php } ?>
      <td><?php echo $file['id']; ?></td>
      <td class = "textcolumn" ><?php echo $file['Events']; ?></td>
      <td class = "textcolumn" ><?php echo $file['Classes']; ?></td>
      <td class = "textcolumn" ><?php echo $file['Description']; ?></td>
      <?php
        $datestring = $file['Date']." ".$file['Time'];
        $datetimeobj = date_create($datestring);
        $Date = date_format($datetimeobj, 'd-M-Y');
        $Time = date_format($datetimeobj, 'H:i:s');
      ?>
      <td class = "datecolumn"><?php echo $Date."<br/>".$Time; ?></td>
      <td style="text-align: center;" ><a <?php echo 'href="'. UPLOAD_FOLDER . $file['name']; ?>" target="_blank"><?php echo '<img src="'. $file['Thumbnail'].'"/>';?></a></td>
    </tr>
  <?php endforeach;?>

</tbody>
</table>
</div>
</body>
</html>
