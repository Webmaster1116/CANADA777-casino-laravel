<?php

//action.php
if(isset($_POST["action"]))
{
 $connect = mysqli_connect("localhost", "www", "2C0q4N5z", "www");
 if($_POST["action"] == "fetch")
 {
  $query = "SELECT * FROM tbl_images ORDER BY id DESC";
  $result = mysqli_query($connect, $query);
  $output = '
   <table class="table table-bordered table-striped">  
    <tr>
     <th width="10%">ID</th>
     <th width="40%">Image</th>
     <th width="30%">provider name</th>
      <th width="30%">provider Title</th>
     <th width="10%">Change</th>
     <th width="10%">Remove</th>
    </tr>
  ';
  while($row = mysqli_fetch_array($result))
  {
   $output .= '

    <tr>
     <td>'.$row["id"].'</td>
     <td>
      <img src="data:image/jpeg;base64,'.base64_encode($row['name']).'" height="60" width="75" class="img-thumbnail" />
     </td>
       <td>
      '.$row['provider_name'].'
     </td>
     <td>
      '.$row['provider_title'].'
     </td>
     <td><button type="button" name="update" class="btn btn-warning bt-xs update" id="'.$row["id"].'">Change</button></td>
     <td><button type="button" name="delete" class="btn btn-danger bt-xs delete" id="'.$row["id"].'">Remove</button></td>
    </tr>
   ';
  }
  $output .= '</table>';
  echo $output;
 }

 if($_POST["action"] == "insert")
 {
  $file = addslashes(file_get_contents($_FILES["image"]["tmp_name"]));
  $provider_name = $_POST["provider_name"];
 $provider_title = $_POST["provider_title"];
  
  $query = "INSERT INTO tbl_images(name, provider_name, provider_title) VALUES ('$file', '$provider_name', '$provider_title')";
  if(mysqli_query($connect, $query))
  {
   echo 'Image Inserted into Database';
  }
 }
 if($_POST["action"] == "update")
 {
  $file = addslashes(file_get_contents($_FILES["image"]["tmp_name"]));
 $provider_name = $_POST["provider_name"];
  $provider_title = $_POST["provider_title"];
  $query = "UPDATE tbl_images SET name = '$file', provider_name='$provider_name', provider_title='$provider_title' WHERE id = '".$_POST["image_id"]."'";
  if(mysqli_query($connect, $query))
  {
   echo 'Image Updated into Database';
  }
 }
 if($_POST["action"] == "delete")
 {
  $query = "DELETE FROM tbl_images WHERE id = '".$_POST["image_id"]."'";
  if(mysqli_query($connect, $query))
  {
   echo 'Image Deleted from Database';
  }
 }
}
?>