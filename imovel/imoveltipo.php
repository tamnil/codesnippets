<?php
// Create connection
$con=mysqli_connect("localhost","root","","imovel");

// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}


$result = mysqli_query($con,"SELECT imoveis.id,imoveis_x_tipos.*   FROM (imoveis_x_tipos) 
right join imoveis on  imoveis_x_tipos.`id_imovel` = imoveis.id 
order by imoveis.id , imoveis_x_tipos.id_tipo asc");

$v1=0;
$comparador='0';
$imovel='1';
$comparatipo='xx';

while($row = mysqli_fetch_array($result)) {
$imovel=$row[id_imovel];

if ($imovel != $comparador){
$comparatipo='xx';
	$comparador=$imovel;
	echo '<br>';
echo $comparador.',-';
}
if ($row[id_tipo] != $comparatipo){
echo $row[id_tipo].'-';
$comparatipo=$row[id_tipo];
}
echo '';
}

mysqli_close($con);
