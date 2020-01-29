<?php

require_once './db.php';

//Get all tables
$stmt=$conn->prepare("SHOW TABLE STATUS FROM {$database}");
$stmt->execute();
$tables=$stmt->get_result();

//Get selected table name, default is the first table
$selected_table=$tables->fetch_assoc()["Name"];
if(isset($_GET["table"])){
  $selected_table=$_GET["table"];
}
$tables->data_seek(0);

//Get table column names
$stmt=$conn->prepare("SHOW FULL COLUMNS FROM {$selected_table}");
$stmt->execute();
$columns=$stmt->get_result();

//Clicked on delete record
if(isset($_GET["delete"])){
  $pk_column=$_GET["pk"];
  $id_record=$_GET["delete"];

  $stmt=$conn->prepare("DELETE  FROM {$selected_table} WHERE {$pk_column}='{$id_record}'");
  $stmt->execute();
}

//Get data from the selected table
$stmt=$conn->prepare("SELECT * FROM {$selected_table}");
$stmt->execute();
$rows=$stmt->get_result();
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

          <?php
          if(isset($_GET["success"])){
            echo '<div class="alert alert-success" role="alert">New record has been successfully added!</div>';
          }
          if(isset($_GET["error"])){
            if(empty($_GET["error"])) $_GET["error"]="Error: cannot insert the new record!";
            echo "<div class='alert alert-danger' role='alert'>{$_GET["error"]}</div>";
          }
          if(isset($_GET["edited"])){
            echo "<div class='alert alert-success' role='alert'>Record has been successfully modified!</div>";
          }
          ?>

          <div class="card">
            <div class="card-header">

              <div class="d-flex align-items-center">
                <div class="mr-auto">

                  <?php
                  while($table=$tables->fetch_assoc()){
                    echo ($selected_table==$table['Name']) ? $table['Comment'] : "";
                  }
                  ?>

                </div>
                <a class="btn btn-success float-right" href="./new.php?table=<?= $_GET["table"] ?>" role="button"><i class="fa fa-plus"></i> New Record</a>
              </div>

            </div>
            <div class="card_data-body">
              <table class="table table-bordered table-hover">
                  <thead>
                    <tr>

                      <?php
                      while($column=$columns->fetch_assoc()){
                        echo "<th><small><b>${column['Comment']}</b></small></th>";
                        if($column["Key"]=="PRI") $primary_key=$column['Field'];
                      }
                      ?>

                      <th style="width:10%"><small><b></b></small></th>

                    </tr>
                  </thead>
                  <tbody>

                      <?php
                      while($row=$rows->fetch_assoc()){
                        echo '<tr>';
                        foreach($row as $row_column){
                          echo "<td>{$row_column}</td>";
                        }

                        $id=reset($row);
                        echo "<td class='text-center'><a href='./edit.php?table={$selected_table}&pk={$primary_key}&id={$id}' style='color:black'><i class='fa fa-edit'></i></a> &nbsp;&nbsp;&nbsp; <a href='./?table={$selected_table}&pk={$primary_key}&delete={$id}' style='color:black'><i class='fa fa-trash fluid'></i></a></td>";
                        echo '</tr>';
                      }
                      ?>

                  </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
