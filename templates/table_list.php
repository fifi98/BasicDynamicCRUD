<?php

while($table=$tables->fetch_assoc()){
  $active = ($selected_table==$table['Name']) ? "active" : "";
  echo "<a href='./index.php?table=${table['Name']}' class='list-group-item py-2 list-group-item-action ${active}'>{$table['Comment']}</a>";
}
$tables->data_seek(0);

?>
