<?php
$url="http://www.url.com/exemplo";
$transacoes=["venda","locacao"];

$bairros=
["todos_bairros","Açores","Armação","Barra_da_Lagoa","Beira-Mar_Norte","Cacupé","Campeche","Canasjurê","Canasvieiras","Centro","Ingleses","Jardim_Anchieta","João_Paulo","Jurerê_Internacional","Jurerê_Tradicional","Lagoa_da_Conceição","Morro_das_Pedras","Pântano_do_Sul","Praia_Mole","Ribeirão_da_Ilha","Rio_Tavares","Rio_Vermelho","Saco_Grande","Sambaqui","Santinho","Solidão","Trindade","Vargem_Pequena"];
$tipos=[
"todos_tipos",
"Apartamento",
"Casa",
"Terreno",
"Mansão",
"Frente_à_Lagoa",
"Pousada",
"Empreendimento",
"Loteamento",
"Frente_ao_Mar",
"Condomínio_Náutico",
"Cobertura",
"Beira_mar",
"Condominio_Fechado",
"Ilha"
];
foreach($transacoes as $transacao){
    

foreach($bairros as $bairro){
    foreach($tipos as $tipo){
        echo '<url><br><loc>';
        echo $url.$transacao.'/'.stripAccents($tipo).'/florianopolis/'.stripAccents($bairro);
        echo '</loc><br></url><br>';
    }
}
}
function stripAccents($string){ 

    $from = "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ";
    $to = "aaaaeeiooouucAAAAEEIOOOUUC";
    $keys = array();
    $values = array();
    preg_match_all('/./u', $from, $keys);
    preg_match_all('/./u', $to, $values);
    $mapping = array_combine($keys[0], $values[0]);
    $string= strtr($string, $mapping);

 
    
    
    return $string;
}
