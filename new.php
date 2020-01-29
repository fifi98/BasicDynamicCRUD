<?php

require_once './db.php';

if($_SERVER['REQUEST_METHOD']==='POST') {

  //Get table name
  $table=$_POST["table"];

  //Remove it from the POST array
  unset($_POST["table"]);

  //Check which which attribute is auto_increment (the one that's empty)
  foreach($_POST as &$var){
    if(empty($var)) $var="DEFAULT";
    else $var="'".$var."'";
  }

  //Separate all values with comma to prepare the SQL statement
  $values = implode(",", $_POST);

  $stmt=$conn->prepare("INSERT INTO {$table} VALUES({$values})");
  if($stmt->execute()){
    header("Location: ./index.php?table={$table}&success");
  }else{
    header("Location: ./index.php?table={$table}&error={$stmt->error}");
  }
  $conn->close();

}

//Get table name
$selected_table=$_GET["table"];

//Get table display name
$stmt=$conn->prepare("SHOW TABLE STATUS FROM {$database}");
$stmt->execute();
$tables=$stmt->get_result();

//Get columns
$stmt=$conn->prepare("SHOW FULL COLUMNS FROM {$selected_table}");
$stmt->execute();
$columns=$stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <? include './templates/head.php' ?>
  </head>
  <body>
    <? include './templates/navbar.php' ?>

    <div class="container-fluid">
      <div class="row">

        <div class="col-md-2" style="padding-bottom: 25px">
          <div class="list-group">
            <? include './templates/table_list.php' ?>
          </div>
        </div>

        <div class="col-md-10">

          <form method="post" action="./new.php">

            <div class="card" style="margin-bottom: 20px">
              <div class="card-header">
                <div class="d-flex align-items-center">
                  <div class="mr-auto">

                    <?php
                    while($table=$tables->fetch_assoc()){
                      echo ($_GET["table"]==$table['Name']) ? $table['Comment'] . " - new record" : "";
                    }
                    ?>

                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="row">

                  <?php
                  while($input=$columns->fetch_assoc()){

                    echo "
                    <div class='col-6'>
                      <div class='form-group'>
                        <label>${input['Comment']}:</label>
                        <input type='text' class='form-control' name='${input['Field']}' ".(($input["Extra"]=="auto_increment")?"readonly":"required").">
                      </div>
                    </div>
                    ";

                  }
                  ?>

                </div>
              </div>
            </div>
            <input type="hidden" name="table" value="<?= $selected_table ?>">
            <button type="submit" class="btn btn-success float-right">Add Record</button>
          </form>
        </div>
      </div>
    </div>
  </body>
</html>
