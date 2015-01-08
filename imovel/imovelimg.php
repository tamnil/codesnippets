<?php
// Create connection
$con=mysqli_connect("localhost","root","","imovel");

// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}


$result = mysqli_query($con,"SELECT imoveis.id,imoveis_fotos.*   FROM (imoveis_fotos) 
right join imoveis on  imoveis_fotos.`imovel` = imoveis.id 
order by imoveis.id asc");

$v1=0;

while($row = mysqli_fetch_array($result)) {
    if ($v1==0){$v1=$row["imovel"];
    //echo $row['imovel']."[";
    echo $row['imovel'].";[";
    }
    if ($v1==$row["imovel"]){
     
    //echo $row['arquivo'].'.jpg ' . $row['id'] ;
      echo ',{"name":"'.$row['arquivo'].'.jpg" '. ',"title":"'.$row['alt'].'","description":"'.$row['alt'].'"}' ;
    }else{
      $v1=$row["imovel"];   
     
     echo "]<br>".$row['imovel'].";[";
            echo '{"name":"'.$row['arquivo'].'.jpg","title":"'.$row['alt']. '","description":"'.$row['alt'].'"}'  ;
       // echo "<br>";
    }
    
    

}

mysqli_close($con);
?> 