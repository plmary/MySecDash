<?php 
$Resultat = [];

$Resultat['test1']['fr'] = 'test1';
$Resultat['test1']['en'] = 'testing1';
$Resultat['test2']['fr'] = 'test2';
$Resultat['test1']['sp'] = 'testinges';

print_r($Resultat);print "<hr>";

print $Resultat['test1']['fr'].'<hr>';
?>
