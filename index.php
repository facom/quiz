<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<script src="etc/jquery.js"></script>
<script>
   function confirmar(trueel,fakeel) {
   var x;
   if (confirm("¿Esta seguro de enviar esta solución?") == true) {
     document.getElementById(trueel).style.display="block";
     document.getElementById(fakeel).style.display="none";
     alert("Para enviar presione nuevamente el botón que ahora esta verde");
   }
 }
</script>
</head>
<?php
////////////////////////////////////////////////////////////
//CONFIGURACION
////////////////////////////////////////////////////////////
require_once("quiz.conf");

////////////////////////////////////////////////////////////
//FUNCIONES
////////////////////////////////////////////////////////////
function isBlank($str){
  if(preg_match("/[\w\d]+/",$str)){return 0;}
  else{return 1;}
}
function errorMsg($msg){
  echo "<BLOCKQUOTE STYLE='color:red'>$msg</BLOCKQUOTE>";
}
function homeLink(){
  echo "<p><a href=?>Volver</a></p>";
}

////////////////////////////////////////////////////////////
//CABECERA
////////////////////////////////////////////////////////////
echo<<<CONTENIDO
<H1><a href=index.php>Primer Parcial de Fundamentaci&oacute;n en Computaci&oacute;n</a></H1>
<H2>$PRUEBA</H2>
CONTENIDO;

////////////////////////////////////////////////////////////
//ENTRADAS
////////////////////////////////////////////////////////////
foreach(array_keys($_GET) as $field){
    $$field=$_GET[$field];
}
foreach(array_keys($_POST) as $field){
    $$field=$_POST[$field];
}

////////////////////////////////////////////////////////////
//PROFESOR
////////////////////////////////////////////////////////////
if(isset($_GET['profesor']) and $_POST['password']=='1qazxsw2'){

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //ACCIONES
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if($accion=="Califica"){
    shell_exec("touch $DIRPRUEBA/.califica");
    echo "<i style='color:red'>Calificacion Activada.</i>";
  }
  if($accion=="Presenta"){
    shell_exec("rm -rf $DIRPRUEBA/.califica");
    echo "<i style='color:red'>Bloqueada la Calificacion.</i>";
  }
  if($accion=="Bloquea"){
    shell_exec("touch $DIRPRUEBA/.block");
    echo "<i style='color:red'>Bloqueado.</i>";
  }
  if($accion=="Mantenimiento"){
    shell_exec("touch .maintainance");
    echo "<i style='color:red'>En Mantenimiento.</i>";
  }
  if($accion=="Abre"){
    shell_exec("rm -rf .maintainance");
    echo "<i style='color:red'>Abierto.</i>";
  }
  if($accion=="Esconde"){
    shell_exec("touch $DIRPRUEBA/.noconsulta");
    echo "<i style='color:red'>Solución a prueba escondida.</i>";
  }
  if($accion=="Desbloquea"){
    shell_exec("rm -rf $DIRPRUEBA/.block");
    echo "<i style='color:red'>Desbloqueado.</i>";
  }
  if($accion=="Consulta"){
    shell_exec("rm -rf $DIRPRUEBA/.noconsulta");
    echo "<i style='color:red'>La solución a la prueba se puede consultar.</i>";
  }

  echo "<form method='post'>";
  
  if(file_exists("$DIRPRUEBA/.block")){
    $button="<input type='submit' name='accion' value='Desbloquea'>";
  }else{
    $button="<input type='submit' name='accion' value='Bloquea'>";
  }

  if(file_exists("$DIRPRUEBA/.noconsulta")){
    $button3="<input type='submit' name='accion' value='Consulta'>";
  }else{
    $button3="<input type='submit' name='accion' value='Esconde'>";
  }

  if(file_exists("$DIRPRUEBA/.califica")){
    $button2="<input type='submit' name='accion' value='Presenta'>";
  }else{
    $button2="<input type='submit' name='accion' value='Califica'>";
  }

echo<<<CONTENIDO
<input type='hidden' name='password' value='$password'>
<input type='hidden' name='group' value='$group'>
<h3>Profesor (Grupo $group)</h3>
$button
$button2
$button3
<input type='submit' name='accion' value='Solucion'>
<input type='submit' name='accion' value='Resultados'>
<input type='submit' name='accion' value='Pruebas'>
CONTENIDO;
 echo "</form>";

  if($accion=="Resultados"){
    require_once("$DIRPRUEBA/prueba.conf");
    $out=shell_exec("ls -md $DIRPRUEBA/respuestas/*");
    $estudiantes=preg_split("/\s*,\s*/",$out);
    $numestudiantes=count($estudiantes);
    echo "<table border=1><tr><td>#</td><td>Grupo</td><td>Cedula</td><td>Test</td><td>Ensayo</td><td>Definitiva</td></tr>";
    $iest=1;
    foreach($estudiantes as $estudiante){
      $estudiante=rtrim($estudiante);
      preg_match("/respuestas\/(\d+)/",$estudiante,$matches);
      $estudiante_cedula=$matches[1];
      $out=shell_exec("grep -H '^$estudiante_cedula\$' Grupos/*.txt");
      if(preg_match("/\d/",$out)){
	preg_match("/grupo(\d+)\.txt/",$out,$matches);
	$grupo=$matches[1];
      }else{
	$grupo="(No Id.)";
      }
      if($group==$grupo or $group==0){
	$ftest="$estudiante/respuestas.txt";
	$fensayo="$estudiante/ensayo.txt";
	$fprofesor="$estudiante/.profesor";
	if(!file_exists($fprofesor)){
	  $urlestudiante="<a href='?accion=califica&qestudiante=${estudiante_cedula}&cedula=0000&palabra=manual' target='_blank'>$estudiante_cedula</a>";
	}else{
	  $urlestudiante="$estudiante_cedula";
	}
	
	$nota_test=rtrim(shell_exec("tail -n 1 $ftest"));
	$nota_ensayo=rtrim(shell_exec("tail -n 1 $fensayo"));
	$totcrit=rtrim(shell_exec("tail -n 2 $fensayo | head -n 1"));
	$totpreguntas=$NUMTEST+$totcrit;
	$definitiva=($nota_test*$NUMTEST+$nota_ensayo*$totcrit)/$totpreguntas;
	$definitiva=sprintf("%.1f",$definitiva);
	
	$nota_test=sprintf("%.1f",$nota_test);
	$nota_ensayo=sprintf("%.1f",$nota_ensayo);
	$definitiva=sprintf("%.1f",$definitiva);

	echo "<tr><td>$iest</td><td>$grupo</td><td>$urlestudiante</td><td>$nota_test</td><td>$nota_ensayo</td><td>$definitiva</td></tr>";
	$iest+=1;
      }
    }
    echo "</table>";
  }
  if($accion=="Pruebas"){
    $pruebas=shell_exec("ls -md Prueba_[a-zA-Z0-9]*");
    $pruebas=preg_split("/\s*,\s*/",$pruebas);
    echo "<H3>Resultados de las pruebas</H3>";
    foreach($pruebas as $prueba){
      //echo "<H3>Prueba '$prueba'</H3>";
      $prueba=rtrim($prueba);
      $DIRPRUEBA="$prueba";
      require_once("$DIRPRUEBA/prueba.conf");
      $out=shell_exec("ls -md $DIRPRUEBA/respuestas/*");
      $estudiantes=preg_split("/\s*,\s*/",$out);
      $numestudiantes=count($estudiantes);
      $csvfile="tmp/grupo-$group-$prueba.csv";
      $fl=fopen($csvfile,"w");
      fwrite($fl,"'Grupo','Cedula','Test','Ensayo','Definitiva'\n");
      $estudiantes=file("Grupos/grupo$group.txt");
      foreach($estudiantes as $estudiante){
	$estudiante_cedula=rtrim($estudiante);
	$estdir="$DIRPRUEBA/respuestas/$estudiante_cedula";
	$out=shell_exec("grep -H '^$estudiante_cedula\$' Grupos/*.txt");
	if(preg_match("/\d/",$out)){
	  preg_match("/grupo(\d+)\.txt/",$out,$matches);
	  $grupo=$matches[1];
	}else{
	  $grupo="(No Id.)";
	}
	if($group==$grupo or $group==0){
	  $ftest="$estdir/respuestas.txt";
	  $fensayo="$estdir/ensayo.txt";
	  $nota_test=rtrim(shell_exec("tail -n 1 $ftest"));
	  $nota_ensayo=rtrim(shell_exec("tail -n 1 $fensayo"));
	  $totpreguntas=$NUMTEST+$NUMESSAY;
	  $definitiva=($nota_test*$NUMTEST+$nota_ensayo*$NUMESSAY)/$totpreguntas;
	  $resultado=sprintf("'$grupo','$estudiante_cedula','%.1f','%.1f','%.1f'\n",$nota_test,$nota_ensayo,$definitiva);
	  fwrite($fl,$resultado);
	}
      }
      echo "<a href='$csvfile'>$prueba</a><br/>";
    }
  }
  if($accion=="Solucion"){
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //EXAMEN
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  require_once("$DIRPRUEBA/prueba.conf");

echo<<<CONTENIDO
  <H4>Prueba</H4>
  <H4>Preguntas tipo test</H4>
CONTENIDO;

  //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
  //SEARCH FOR PREGUNTAS
  //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
  $out=shell_exec("ls -m $DIRPRUEBA/preguntas/pregunta*.txt");
  $preguntas=preg_split("/\s*,\s*/",$out);
  $numpreguntas=count($preguntas);
  $NUMTEST=$numpreguntas;

  //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
  //CREATE INDICE ARRAY
  //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
  $indices=array();
  for($i=0;$i<$numpreguntas;$i++){
    array_push($indices,$i);
  }

  //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
  //SHOW QUESTIONS
  //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
  for($i=0;$i<$NUMTEST;$i++){
    $indice=$indices[$i];
    $pregunta=$preguntas[$indice];
    $out=shell_exec("grep -v '#R#' $pregunta");

    $parts=preg_split("/\./",$pregunta);
    $imagen=sprintf("%s.png",$parts[0]);
    if(file_exists($imagen)){
      $img="<a href='$imagen'><img src='$imagen' width='600px'></a>";
    }else{$img="";}
    $respuesta=rtrim(shell_exec("grep '#R#' $pregunta"));
    $respuesta=preg_replace("/[\s\n\r]*\#R\#[\s\n\r]*/","",$respuesta);
    $n=$i+1;
    $original=$indice+1;
echo<<<CONTENIDO
  <H5>PREGUNTA $n:</H5>
      $img
      <pre>$out</pre>
      <p style=color:red>La respuesta es: $respuesta.</p>
CONTENIDO;
  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //TIPO ENSAYO
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

  //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
  //SEARCH FOR PREGUNTAS
  //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
  $out=shell_exec("ls -m $DIRPRUEBA/preguntas/pregunta*.ens");
  if(!preg_match("/\w/",$out)){
    return;
  }
  echo "<HR/><H4>Preguntas tipo ensayo</H4>";
  $preguntas=preg_split("/\s*,\s*/",$out);
  $numpreguntas=count($preguntas);
  $NUMTEST=$numpreguntas;

  //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
  //CREATE INDICE ARRAY
  //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
  $indices=array();
  for($i=0;$i<$numpreguntas;$i++){
    array_push($indices,$i);
  }

  //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
  //SHOW QUESTIONS
  //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
  for($i=0;$i<$NUMTEST;$i++){
    $indice=$indices[$i];
    $pregunta=$preguntas[$indice];
    $out=shell_exec("cat $pregunta");
    $parts=preg_split("/\./",$pregunta);
    $imagen=sprintf("%s.png",$parts[0]);
    $solucion=sprintf("%s.sol",$parts[0]);
    if(file_exists($imagen)){
      $img="<a href='$imagen'><img src='$imagen' width='600px'></a>";
    }else{$img="";}
    $respuesta=shell_exec("cat $solucion");
    $n++;
    $original=$indice+1;
echo<<<CONTENIDO
  <H5>PREGUNTA $n:</H5>
      $img
      <pre>$out</pre>
      <p style=color:red>La respuesta es:<br/><pre style=color:red>$respuesta</pre></p>
CONTENIDO;
  }
  
  }
  return;
}

////////////////////////////////////////////////////////////
//CALIFICA
////////////////////////////////////////////////////////////
if($_GET["accion"]=="califica"){

  echo "<b>Calificando</b><br/>";
  
  //SEARCH FOR STDUDENTS
  if(isset($qestudiante)){
    $estudiantes=array("$DIRPRUEBA/respuestas/$qestudiante");
  }else{
    $out=shell_exec("ls -md $DIRPRUEBA/respuestas/*");
    $estudiantes=preg_split("/\s*,\s*/",$out);
    $numestudiantes=count($estudiantes);
  }

  $id=0;
  foreach($estudiantes as $estudiante){
    $estudiante=rtrim($estudiante);
    preg_match("/respuestas\/(\d+)/",$estudiante,$matches);
    $estudiante_cedula=$matches[1];
    //echo "Checking estudiante $estudiante...<br/>";
    if($cedula==$estudiante_cedula){
      //echo "El estudiante no puede calificar su propia evaluación<br/>";
      continue;
    }
    if(!file_exists("$estudiante/.block") or
       isset($qestudiante)){
      echo "<form method='get'>";
      echo "<input type='hidden' name='estudiante' value='$estudiante'>";
      //echo "Calificando $estudiante<br/>";
      shell_exec("date > $estudiante/.block");
      $out=shell_exec("ls -m $estudiante/respuesta??.txt");
      $respuestas=preg_split("/\s*,\s*/",$out);
      $numrespuestas=count($respuestas);
      foreach($respuestas as $respuesta){
	$respuesta=rtrim($respuesta);
	preg_match("/respuesta(\d+)\.txt/",$respuesta,$matching);
	$n=$matching[1];
	echo "<H4>Pregunta $n</H4>";
	$pregunta=shell_exec("cat $DIRPRUEBA/preguntas/pregunta$n.ens");
	echo "<pre>$pregunta</pre>";
	$respuesta_esperada=shell_exec("cat $DIRPRUEBA/preguntas/pregunta$n.sol");
	$imagen_esperada="$DIRPRUEBA/preguntas/pregunta$n.sol.png";
	echo "<b>Respuesta esperada</b>:<br/><pre style='background:yellow;color:red;padding:10px'>$respuesta_esperada</pre>";
	if(file_exists($imagen_esperada)){
	  echo "<b>Imagen esperada</b>:<br/><img src=$imagen_esperada width=600px><br/>";
	}	
	$respuesta=shell_exec("cat $respuesta");
	$respuesta=preg_replace("/</","&lt;",$respuesta);
	$respuesta=preg_replace("/>/","&gt;",$respuesta);
	echo "<b>Respuesta estudiante</b>:<br/><pre style='background:lightgray;padding:10px'>$respuesta</pre>";

	$out=trim(shell_exec("ls $estudiante/respuesta${n}_archivo.*"));
	if(preg_match("/\w/",$out)){
	  $file=trim(shell_exec("basename $out"));
	  echo "<b>Arhcivos enviados por estudiante</b>: <a href='$out' target='_blank'>$file</a><br/>";
	  if(is_array(getimagesize($out))){
	    echo "<img src=$out width=600px><br/>";
	  }
	}

	echo "<b>Evaluación</b>:<br/><br/>";
	$out=shell_exec("grep '^-' $DIRPRUEBA/preguntas/pregunta$n.mat | cut -f 2 -d ':'");
	$criterios=preg_split("/\n/",$out);
	$numcrit=count($criterios)-1;
	echo "<input type='hidden' name='pregunta${n}_numcrit' value='$numcrit'>";
	//print_r($criterios);
	$out=shell_exec("grep '^\*' $DIRPRUEBA/preguntas/pregunta$n.mat | cut -f 2 -d ':'");
	$puntajes=preg_split("/\n/",$out);
	//print_r($puntajes);
	$out=shell_exec("grep '^\*' $DIRPRUEBA/preguntas/pregunta$n.mat | cut -f 3 -d ':'");
	$valores=preg_split("/\n/",$out);
	//print_r($valores);
	echo "<table border=1>";
	echo "<tr><td>Criterio</td>";
	foreach($puntajes as $puntaje){
	  if(!preg_match("/\w/",$puntaje)){continue;}
	  echo "<td>$puntaje</td>";
	}
	$ic=1;
	foreach($criterios as $criterio){
	  if(!preg_match("/\w/",$criterio)){continue;}
	  echo "<tr><td>$criterio</td>";
	  $ip=0;
	  foreach($puntajes as $puntaje){
	    if(!preg_match("/\w/",$puntaje)){continue;}
	    $value=$valores[$ip];
	    if($ip==0){$check="checked";}
	    else{$check="";}
	    echo "<td><input type='radio' name='pregunta_${n}_critertio_$ic' value='$value' $check></td>";
	    $ip++;
	  }
	  echo "</tr>";
	  $ic++;
	}
	echo "</table><br/>";
	echo "<input type='hidden' name='cedula' value='$cedula'>";
	echo "<input type='hidden' name='palabra' value='$palabra'>";
      }
echo<<<BUTTONS
  <button id="fake_evalua" onclick="confirmar('true_evalua','fake_evalua')" form="JavaScript:void(null)" style="background-color:yellow">evalua</button>
  <input style="display:none;background-color:green" id="true_evalua" type='submit' name='accion' value='evalua'>
BUTTONS;
      $id++;
      break;
    }
  }
  
  if($id==0){
    echo "<i style='color:red'>No hay estudiantes para calificar</i>";
  }
  homeLink();

}

////////////////////////////////////////////////////////////
//PRESENTA
////////////////////////////////////////////////////////////
else if($_GET["accion"]=="evalua"){

  echo "<i>Gracias por tu evaluación</i>";
  //echo "Estudiante: $estudiante<br/>";
  $out=shell_exec("ls -m $estudiante/respuesta??.txt");
  $respuestas=preg_split("/\s*,\s*/",$out);
  $numrespuestas=count($respuestas);
  $fl=fopen("$estudiante/ensayo.txt","w");
  if($palabra=="manual"){
    shell_exec("touch $estudiante/.profesor");
  }
  fwrite($fl,"$cedula:$palabra\n");
  $definitiva=0;
  $ir=0;
  $totcrit=0;
  foreach($respuestas as $respuesta){
    $respuesta=rtrim($respuesta);
    preg_match("/respuesta(\d+)\.txt/",$respuesta,$matching);
    $n=$matching[1];
    echo "<H4>Pregunta $n</H4>";
    $name="pregunta${n}_numcrit";
    $numcrit=$$name;
    $totcrit+=$numcrit;
    //echo "Numero de criterios ($n,$name): $numcrit<br/>";
    $nota=0;
    fwrite($fl,"$n:$numcrit\n");
    for($ic=1;$ic<=$numcrit;$ic++){
      $name="pregunta_${n}_critertio_$ic";
      $valor=$$name;
      $nota+=$valor;
      echo "<i>Calificacion criterio $ic</i>: $valor<br/>";
      fwrite($fl,"\tC$ic:$valor\n");
    }
    $nota=$nota/$numcrit;
    fwrite($fl,"\tNOTA:$nota\n");
    $definitiva+=$nota*$numcrit;
    echo "<br/><b>Nota final pregunta $n</b>: $nota<br/>";
    $ir++;
  }
  $definitiva=$definitiva/$totcrit;
  echo "<p><b>Nota definitiva</b>: $definitiva<br/>";
  fwrite($fl,"$totcrit\n");
  fwrite($fl,"$definitiva\n");
  fclose($fl);
  homeLink();
}

////////////////////////////////////////////////////////////
//PRESENTA
////////////////////////////////////////////////////////////
else if($_GET["accion"]=="presenta"){
  require_once("$DIRPRUEBA/prueba.conf");

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //CONTROL DE CEDULA
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  $fresultado="$DIRPRUEBA/respuestas/$cedula/respuestas.txt";
  if(file_exists($fresultado)){
    $nota=rtrim(shell_exec("tail -n 1 $fresultado"));
    if($NUMESSAY>0){
      $notaensayo=rtrim(shell_exec("tail -n 1 $DIRPRUEBA/respuestas/$cedula/ensayo.txt"));
      $totcrit=rtrim(shell_exec("tail -n 2 $DIRPRUEBA/respuestas/$cedula/ensayo.txt | head -n 1"));
    }else{
      $notaensayo=0;
    }
    $totpreguntas=$NUMTEST+$totcrit;
    $definitiva=($nota*$NUMTEST+$notaensayo*$totcrit)/$totpreguntas;
    $definitiva=sprintf("%.1f",$definitiva);
    $palabra=rtrim(shell_exec("head -n 1 $fresultado"));
    errorMsg("$cedula: tu unica oportunidad de presentar el examen ya paso (palabra
clave '$palabra').  Obtuviste una nota de: <b>$nota ($NUMTEST)+$notaensayo ($NUMESSAY) = $definitiva</b>");
    homeLink();
    return;
  }
  if(isBlank($cedula)){
    errorMsg("Debes proveer un numero de identificaci&oacute;n.");
    homeLink();
    return;
  }
  if(isBlank($palabra)){
    errorMsg("Debes proveer una palabra clave.");
    homeLink();
    return;
  }
  if(strlen($cedula)<6){
    errorMsg("Tu cedula es demasiado corta.");
    homeLink();
    return;
  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //EXAMEN
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  require_once("$DIRPRUEBA/prueba.conf");

echo<<<CONTENIDO
  <H3>Estudiante: $cedula ($palabra)</H3>
  <H4>Prueba</H4>
  <form method="post" action="?" enctype="multipart/form-data">
CONTENIDO;

  //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
  //SEARCH FOR PREGUNTAS
  //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
  $out=shell_exec("ls -m $DIRPRUEBA/preguntas/pregunta*.txt");
  $preguntas=preg_split("/\s*,\s*/",$out);
  $numpreguntas=count($preguntas);
  if($numpreguntas<$NUMTEST){
    $NUMTEST=$numpreguntas;
  }

  //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
  //CREATE INDICE ARRAY
  //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
  $indices=array();
  for($i=0;$i<$numpreguntas;$i++){
    array_push($indices,$i);
  }
  //shuffle($indices);

  //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
  //SHOW QUESTIONS
  //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
  echo "<input type='hidden' name='numpreguntas' value='$NUMTEST'>";
  echo "<input type='hidden' name='cedula' value='$cedula'>";
  echo "<input type='hidden' name='palabra' value='$palabra'>";
  for($i=0;$i<$NUMTEST;$i++){
    $indice=$indices[$i];
    $pregunta=$preguntas[$indice];
    $out=shell_exec("grep -v '#R#' $pregunta");

    $parts=preg_split("/\./",$pregunta);
    $imagen=sprintf("%s.png",$parts[0]);
    if(file_exists($imagen)){
      $img="<a href='$imagen'><img src='$imagen' width='600px'></a>";
    }else{$img="";}
    $respuesta=rtrim(shell_exec("grep '#R#' $pregunta"));
    $respuesta=preg_replace("/[\s\n\r]*\#R\#[\s\n\r]*/","",$respuesta);
    $n=$i+1;
    $original=$indice+1;
echo<<<CONTENIDO
  <H5>PREGUNTA $n:</H5>
      $img
      <pre>$out</pre>
      <p>Su repuesta:<input type="text" size=1 name="respuesta_estudiante_$i" value=""></p>
      <input type="hidden" name="respuesta_$i" value="$respuesta">
      <input type="hidden" name="original_$i" value="$original">
CONTENIDO;
    /*
    if(!isBlank($respuesta)){
      echo "<p style=font-size:10px>La respuesta esta disponible: $respuesta.</p>";
    }
    */
  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //TIPO ENSAYO
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

  //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
  //SEARCH FOR PREGUNTAS
  //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
  $out=shell_exec("ls -m $DIRPRUEBA/preguntas/pregunta*.ens");
  $preguntas=preg_split("/\s*,\s*/",$out);
  $numpreguntas=count($preguntas);
  if($numpreguntas<$NUMESSAY){
    $NUMESSAY=$numpreguntas;
  }

  //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
  //CREATE INDICE ARRAY
  //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
  $indices=array();
  for($i=0;$i<$numpreguntas;$i++){
    array_push($indices,$i);
  }
  shuffle($indices);

  //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
  //SHOW QUESTIONS
  //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
  echo "<input type='hidden' name='numensayo' value='$NUMESSAY'>";
  for($i=0;$i<$NUMESSAY;$i++){
    $indice=$indices[$i];
    $pregunta=$preguntas[$indice];
    $out=shell_exec("cat $pregunta");
    $parts=preg_split("/\./",$pregunta);
    $imagen=sprintf("%s.ens.png",$parts[0]);
    $template=sprintf("%s.tem",$parts[0]);
    if(file_exists($imagen)){
      $img="<a href='$imagen'><img src='$imagen' width='600px'></a>";
    }else{$img="";}
    $n++;
    $original=$indice+1;
    $temptext=shell_exec("cat $template");
echo<<<CONTENIDO
  <H5>PREGUNTA $n:</H5>
      $img
      <pre>$out</pre>
      Su repuesta:<br/>
      <textarea rows="30" cols="80" name="respuesta_ensayo_$original">
$temptext
      </textarea><br/>
      <p>Archivo para subir:</p><input type="file" name="archivo_ensayo_$original">
      <br/>
      <input type="hidden" name="ensayo_$i" value="$original">
CONTENIDO;
  }

echo<<<CONTENIDO
  <p>
  <button id="fake_enviar" onclick="confirmar('true_enviar','fake_enviar')" form="JavaScript:void(null)" style="background-color:yellow">enviar</button>
  <input style="display:none;background-color:green" id="true_enviar" type="submit" name="accion" value="enviar">
  </p>
  </form>
CONTENIDO;
  homeLink();
}
////////////////////////////////////////////////////////////
//PRESENTA
////////////////////////////////////////////////////////////
else if($_GET["accion"]=="consulta"){
  if(!$prueba){
    $out=shell_exec("ls -dm Prueba_*");
    $out=preg_replace("/Prueba_/","",$out);
    $out=preg_replace("/, T/","",$out);
echo<<<PRUEBA
<form method="get">
<input type='hidden' name='cedula' value='$cedula'>
Estudiante: $cedula<br/>
Prueba:<input type='text' name='prueba' size="3"><br/>
<i>Disponibles: $out</i><br/>
<input type='submit' name='accion' value='consulta'>
PRUEBA;
    homeLink();
    return;
  }
  $DIRPRUEBA="Prueba_$prueba";
  require_once("$DIRPRUEBA/prueba.conf");
  echo "<H2>Resultado Prueba $prueba</H2>";

  if(file_exists("$DIRPRUEBA/.noconsulta")){
    errorMsg("La prueba esta bloqueada porque todavia la están presentando otros grupos.");
    homeLink();
    return;
  }
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //CONTROL DE CEDULA
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  $fresultado="Prueba_$prueba/respuestas/$cedula/respuestas.txt";
  if(!file_exists($fresultado)){
    errorMsg("Usted no presento esta prueba.");
    homeLink();
    return;
  }

  $nota=rtrim(shell_exec("tail -n 1 $fresultado"));
  if($NUMESSAY>0){
    $notaensayo=rtrim(shell_exec("tail -n 1 $DIRPRUEBA/respuestas/$cedula/ensayo.txt"));
    $totcrit=rtrim(shell_exec("tail -n 2 $DIRPRUEBA/respuestas/$cedula/ensayo.txt | head -n 1"));
  }else{
    $notaensayo=0;
    $totcrit=0;
  }
  $notaensayo=sprintf("%.1f",$notaensayo);
  $totpreguntas=$NUMTEST+$totcrit;
  $definitiva=($nota*$NUMTEST+$notaensayo*$totcrit)/$totpreguntas;
  $definitiva=sprintf("%.1f",$definitiva);
  
  if(isBlank($cedula)){
    errorMsg("Debes proveer un numero de identificaci&oacute;n.");
    homeLink();
    return;
  }
  if(strlen($cedula)<6){
    errorMsg("Tu cedula es demasiado corta.");
    homeLink();
    return;
  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //RESULTADO
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  print "<b>Cédula</b>: $cedula<br/>";
  print "<b>Tu palabra clave fue</b>: $respuestas[0]<br/>";

  //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
  //PREGUNTAS TIPO TEST
  //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
  $respuestas=file("$DIRPRUEBA/respuestas/$cedula/respuestas.txt");
  $right=0;
  echo "<HR/><H4>Preguntas Tipo Test</H4>";
  for($i=1;$i<=$NUMTEST;$i++){
    $respuesta=$respuestas[$i];
    //echo "Respuesta: $respuesta<br/>";
    $parts=preg_split("/:/",$respuesta);
    $porder=$parts[0];
    $n=sprintf("%02d",$parts[1]);
    $resp=$parts[2];
    preg_match("/(.+)\(/",$resp,$matches);
    $resp=$matches[1];
    //echo "Pregunta $porder: $n<br/>";
    //echo "<H5>Pregunta $porder</H5>";
    $pregunta="$DIRPRUEBA/preguntas/pregunta$n.txt";
    $out=shell_exec("grep -v '#R#' $pregunta");
    $parts=preg_split("/\./",$pregunta);
    $imagen=sprintf("%s.png",$parts[0]);
    if(file_exists($imagen)){
      $img="<a href='$imagen'><img src='$imagen' width='600px'></a>";
    }else{$img="";}
    $respuesta=rtrim(shell_exec("grep '#R#' $pregunta"));
    $respuesta=preg_replace("/[\s\n\r]*\#R\#[\s\n\r]*/","",$respuesta);
echo<<<CONTENIDO
  <H5>PREGUNTA $i:</H5>
      $img
      <pre>$out</pre>
CONTENIDO;
    if($respuesta==$resp){
      $resultado="<i style=color:blue>Su respuesta fue correcta.</i>";
      $right++;
    }else{
      $resultado="<i style=color:red>Debes tener más cuidado la próxima.</i>";
    }
    echo "Respuesta correcta: <b style=color:blue>$respuesta</b><br/>";
    echo "Su respuesta: <b style=color:green>$resp</b><br/>";
    echo "Resultado: $resultado<br/>";
  }
  
  //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
  //PREGUNTAS TIPO ENSAYO
  //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
  if($NUMESSAY>0){
    $ensayo="$DIRPRUEBA/respuestas/$cedula/ensayo.txt";
    if(file_exists($ensayo)){
      $respuestas=file($ensayo);
      echo "<HR/><H4>Preguntas Tipo Ensayo</H4>";
      $calificador=$respuestas[0];
      $nl=1;
      for($i=1;$i<=$NUMESSAY;$i++){
	$caract=$respuestas[$nl];
	$parts=preg_split("/:/",$caract);
	$n=$parts[0];
	$numcrit=$parts[1];
	$pregunta="$DIRPRUEBA/preguntas/pregunta$n.ens";
	$out=shell_exec("cat $pregunta");
	$parts=preg_split("/\./",$pregunta);
	$imagen=sprintf("%s.png",$parts[0]);
	$solucion=sprintf("%s.sol",$parts[0]);
	$matrix=sprintf("%s.mat",$parts[0]);
	if(file_exists($imagen)){
	  $img="<a href='$imagen'><img src='$imagen' width='600px'></a>";
	}else{$img="";}
	$respuesta=shell_exec("cat $DIRPRUEBA/respuestas/$cedula/respuesta$n.txt");
	$respuesta=preg_replace("/</","&lt;",$respuesta);
	$respuesta=preg_replace("/>/","&gt;",$respuesta);
	$solucion=shell_exec("cat $solucion");
	echo "<H4>Pregunta $n (Número de criterios $numcrit)</H4>";
	
echo<<<CONTENIDO
	 <H5>PREGUNTA $n:</H5>
	 $img
	 <pre>$out</pre>
	 <b>Respuesta esperada</b>:<br/>
	 <pre style='background:yellow;color:red;padding:10px'>$solucion</pre>
	 <b>Respuesta estudiante</b>:<br/>
	 <pre style='background:lightgray;padding:10px'>$respuesta</pre>
	 <b>Evaluacion</b>:<br/>
CONTENIDO;
	$out=shell_exec("grep '^-' $DIRPRUEBA/preguntas/pregunta$n.mat | cut -f 2 -d ':'");
	$criterios=preg_split("/\n/",$out);
	$ic=1;
	echo "<table border=1>";
	$val=0;
	foreach($criterios as $criterio){
	   if(!preg_match("/\w/",$criterio)){continue;}
	   echo "<tr><td>$criterio</td>";
	   $critresp=$respuestas[$nl+$ic];
	   $parts=preg_split("/:/",$critresp);
	   $val+=$parts[1];
	   echo "<td>$parts[1]</td></tr>";
	   $ic++;
	}
	$val/=$numcrit;
	echo "<tr><td colspan=2>Resultado: $val</td></tr>";
	echo "</table>";
	$nl+=($numcrit+2);
      }
    }else{
      errorMsg("No hay resultados para la parte de ensayo.");
    }
  }

  //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
  //BALANCE
  //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$

echo<<<RESULTADO
<hr/>
<H4>Resultado</H4>
  <b>Test (Peso $NUMTEST):</b> $right correctas de $NUMTEST, Nota: $nota<br/>
  <b>Ensayo (Peso $totcrit):</b> Nota: $notaensayo<br/>
  <b>Definitiva:</b> $definitiva<br/>
RESULTADO;

  homeLink();

}else if($accion=="enviar"){
  require_once("$DIRPRUEBA/prueba.conf");
  //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
  //CALIFICAR
  //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
  if(file_exists("$DIRPRUEBA/respuestas/$cedula/respuestas.txt")){
    echo "Usted ya envio sus respuestas.";
    shell_exec("echo 'Rebaja' $DIRPRUEBA/respuestas/$cedula/rebaja.txt");
    return;
  } 
  if(file_exists("$DIRPRUEBA/.time")){
    echo "<i>El tiempo de entregar paso. Su prueba será recibida pero se le pondrá una sanción.</i>";
    shell_exec("echo 'Tiempo' $DIRPRUEBA/respuestas/$cedula/tiempo.txt");
  }
  
echo<<<CONTENIDO
Su repuesta ha sido recibida.  Preguntas $numpreguntas.
<p></p>
CONTENIDO;
 $s=0;
 shell_exec("mkdir $DIRPRUEBA/respuestas/$cedula");
 $fl=fopen("$DIRPRUEBA/respuestas/$cedula/respuestas.txt","w");
 fwrite($fl,"$palabra\n");
  for($i=0;$i<$numpreguntas;$i++){
    $n=$i+1;
    $nombre="original_$i";
    $original=$$nombre;
    $nombre="respuesta_estudiante_$i";
    $respest=$$nombre;
    $nombre="respuesta_$i";
    $respreal=$$nombre;
    fwrite($fl,sprintf("%d:%d:$respest($respreal)\n",$n,$original));
    echo "<b>Pregunta $n</b>:<br/>";
    echo "... Respuesta Estudiante: -$respest-<br/>";
    echo "... Respuesta Esperada: -$respreal-<br/>";
    echo "... Resultado: ";
    if($respest==$respreal){
      echo "<i style=color:green>Aprobado</i><br/>";
      $s+=1;
    }else{
      echo "<i style=color:red>Reprobado</i><br/>";
    }
  }
  $nota=($s/$numpreguntas)*5;
  $nota=sprintf("%.1f",$nota);
  fwrite($fl,"$nota\n");
  fclose($fl);
  echo "<br/>";
  echo "<b>Total score</b>: $s<br/>";
  echo "<p style='font-size:20px'><b>Nota</b>: $nota</p>";

  if($NUMESSAY>0){
    echo "<H3>Respuestas Ensayo</H3>";
    for($i=0;$i<$NUMESSAY;$i++){
      $n++;
      echo "<b>Pregunta $n:</b><br/>";
      $name="ensayo_$i";
      $original=$$name;
      $m=sprintf("%02d",$original);
      $name="respuesta_ensayo_$original";
      $respuesta=$$name;
      echo "<pre>$respuesta</pre><br/>";
      $fl=fopen("$DIRPRUEBA/respuestas/$cedula/respuesta$m.txt","w");
      fwrite($fl,"$respuesta");
      fclose($fl);
      //print_r($_FILES);echo "<br/>";
      $tmpfile=$_FILES["archivo_ensayo_$original"]["tmp_name"];
      if(file_exists($tmpfile)){
	$filename=$_FILES["archivo_ensayo_$original"]["name"];
	$parts=preg_split("/\./",$filename);
	$ext=$parts[1];
	$filesize=$_FILES["archivo_ensayo_$original"]["size"];
	$fileup="$DIRPRUEBA/respuestas/$cedula/respuesta${m}_archivo.$ext";
	shell_exec("cp -rf $tmpfile $fileup");
	echo "<b>Archivo subido para pregunta $n</b>: <a href=$fileup target='_blank'>$filename</a> ($filesize bytes)<br/>";
      }
    }
  }
  homeLink();
}else{
////////////////////////////////////////////////////////////
//PRINCIPAL
////////////////////////////////////////////////////////////
if(isset($_GET['profesor']) and !isset($_POST['password'])){
echo "<form method='post'>";
echo<<<CONTENIDO
Password:<input type="password" name="password"><br/>
Grupo:<input type="text" name="group"><br/>
<input type="submit" name="accion" value="accede">
CONTENIDO;
}else{
  /*
  if(file_exists("$DIRPRUEBA/.block")){
      echo "<p style='color:red'>La prueba esta deshabilitada.</a>";
      return 0;
      }*/

echo "<form>";
echo<<<CONTENIDO
Documento de Identidad:<input type="text" name="cedula"><br/>
Palabra secreta:<input type="password" name="palabra"><br/>
<i style="font-size:12px">Escoge una palabra corta de facil recordacion</i><br/>
<input type="submit" name="accion" value="consulta">
CONTENIDO;
 
 if(!file_exists("$DIRPRUEBA/.block")){
   echo "<input type='submit' name='accion' value='presenta'>";
 }
 if(file_exists("$DIRPRUEBA/.califica")){
   echo "<input type='submit' name='accion' value='califica'>";
 }

}
echo "</form>";
}
?>
