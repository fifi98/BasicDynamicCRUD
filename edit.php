<?php

require_once './db.php';

if($_SERVER['REQUEST_METHOD']==='POST') {

  //Get table name
  $table=$_POST["table"];
  $pk=$_POST["pk"];
  $id=$_POST["id"];

  //Remove that part from the POST array
  unset($_POST["table"]);
  unset($_POST["pk"]);
  unset($_POST["id"]);

  //Check which which attribute is auto_increment (the one that's empty) and prepare the UPDATE statement
  $values=array();
  foreach ($_POST as $column => $value){
    array_push($values, $column."='".$value."'");
  }
  $new_values=implode(",", $values);

  //Execute UPDATE statement
  $stmt=$conn->prepare("UPDATE {$table} SET {$new_values} WHERE {$pk}='{$id}'");
  if($stmt->execute()){
    header("Location: ./index.php?table={$table}&edited");
  }else{
    header("Location: ./index.php?table={$table}&error={$stmt->error}");
  }
  $conn->close();
  die();
}

//Get selected table name
$selected_table=$_GET["table"];

//Get table display name
$stmt=$conn->prepare("SHOW TABLE STATUS FROM {$database}");
$stmt->execute();
$tables=$stmt->get_result();

//Get table columns
$stmt=$conn->prepare("SHOW FULL COLUMNS FROM {$selected_table}");
$stmt->execute();
$columns=$stmt->get_result();

//Get data from the table
$pk_column=$_GET["pk"];
$id_record=$_GET["id"];
$stmt=$conn->prepare("SELECT * FROM {$selected_table} WHERE {$pk_column}='{$id_record}'");
$stmt->execute();
$rows=$stmt->get_result();
$data=$rows->fetch_assoc();

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

          <form method="post" action="./edit.php">

            <div class="card" style="margin-bottom: 20px">
              <div class="card-header">
                <div class="d-flex align-items-center">
                  <div class="mr-auto">

                    <?php
                    while($table=$tables->fetch_assoc()){
                      echo ($_GET["table"]==$table['Name']) ? $table['Comment'] . " - edit record" : "";
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
                        <input type='text' class='form-control' value='{$data[$input['Field']]}' name='{$input['Field']}' ".(($input["Extra"]=="auto_increment") ? "readonly" : "required").">
                      </div>
                    </div>
                    ";

                  }
                  ?>

                </div>
              </div>
            </div>
            <input type="hidden" name="table" value="<?= $selected_table ?>">
            <input type="hidden" name="pk" value="<?= $pk_column ?>">
            <input type="hidden" name="id" value="<?= $id_record ?>">
            <button type="submit" class="btn btn-success float-right">Save Changes</button>
          </form>
        </div>
      </div>
    </div>
  </body>
</html>
