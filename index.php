<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
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
<H1>Prueba de Fundamentaci&oacute;n en Computaci&oacute;n</H1>
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
  if($accion=="Desbloquea"){
    shell_exec("rm -rf $DIRPRUEBA/.block");
    echo "<i style='color:red'>Desbloqueado.</i>";
  }

  echo "<form method='post'>";
  
  if(file_exists("$DIRPRUEBA/.block")){
    $button="<input type='submit' name='accion' value='Desbloquea'>";
  }else{
    $button="<input type='submit' name='accion' value='Bloquea'>";
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
    echo "<table border=1><tr><td>Grupo</td><td>Cedula</td><td>Test</td><td>Ensayo</td><td>Definitiva</td></tr>";
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

	if(!file_exists($fensayo)){
	  echo "$estudiante_cedula NO CALIFICADO<br/>";
	  $urlestudiante="<a href='?accion=califica&qestudiante=${estudiante_cedula}&cedula=0000&palabra=manual'>$estudiante_cedula</a>";
	}else{
	  $urlestudiante="$estudiante_cedula";
	}
	
	$nota_test=rtrim(shell_exec("tail -n 1 $ftest"));
	$nota_ensayo=rtrim(shell_exec("tail -n 1 $fensayo"));
	$totpreguntas=$NUMTEST+$NUMESSAY;
	$definitiva=($nota_test*$NUMTEST+$nota_ensayo*$NUMESSAY)/$totpreguntas;
	$definitiva=sprintf("%.1f",$definitiva);
	echo "<tr><td>$grupo</td><td>$urlestudiante</td><td>$nota_test</td><td>$nota_ensayo</td><td>$definitiva</td></tr>";
      }
    }
    echo "</table>";
  }
  if($accion=="Pruebas"){
    $pruebas=shell_exec("ls -md Prueba_[0-9]*");
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
      echo "<form>";
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
	$respuesta=shell_exec("cat $respuesta");
	echo "<b>Respuesta estudiante</b>:<br/><pre style='background:lightgray;padding:10px'>$respuesta</pre>";
	$respuesta_esperada=shell_exec("cat $DIRPRUEBA/preguntas/pregunta$n.sol");
	echo "<b>Respuesta esperada</b>:<br/><pre style='background:yellow;color:red;padding:10px'>$respuesta_esperada</pre>";
	echo "Evaluación:<br/><br/>";
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
	echo "<input type='submit' name='accion' value='evalua'>";
      }
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
  fwrite($fl,"$cedula:$palabra\n");
  $definitiva=0;
  $ir=0;
  foreach($respuestas as $respuesta){
    $respuesta=rtrim($respuesta);
    preg_match("/respuesta(\d+)\.txt/",$respuesta,$matching);
    $n=$matching[1];
    echo "<H4>Pregunta $n</H4>";
    $name="pregunta${n}_numcrit";
    $numcrit=$$name;
    //echo "Numero de criterios ($n,$name): $numcrit<br/>";
    $nota=0;
    for($ic=1;$ic<=$numcrit;$ic++){
      $name="pregunta_${n}_critertio_$ic";
      $valor=$$name;
      $nota+=$valor;
      echo "<i>Calificacion criterio $ic</i>: $valor<br/>";
      fwrite($fl,"\tC$ic:$valor\n");
    }
    $nota=$nota/$numcrit;
    $definitiva+=$nota;
    echo "<br/><b>Nota final pregunta $n</b>: $nota<br/>";
    fwrite($fl,"$n:$nota\n");
    $ir++;
  }
  $definitiva=$definitiva/$ir;
  echo "<p><b>Nota definitiva</b>: $definitiva<br/>";
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
    }else{
      $notaensayo=0;
    }
    $totpreguntas=$NUMTEST+$NUMESSAY;
    $definitiva=($nota*$NUMTEST+$notaensayo*$NUMESSAY)/$totpreguntas;
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
  <form method="get">
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
  shuffle($indices);

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
      <p>Su repuesta:<input type="text" size=5 name="respuesta_estudiante_$i" value=""></p>
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
    $imagen=sprintf("%s.png",$parts[0]);
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
      <p>Su repuesta:<br/>
      <textarea rows="30" cols="80" name="respuesta_ensayo_$original">
$temptext
      </textarea>
      <br/>
      <input type="hidden" name="ensayo_$i" value="$original">
CONTENIDO;
  }

echo<<<CONTENIDO
  <p>
  <input type='submit' name='accion' value='enviar'>
  </p>
  </form>
CONTENIDO;
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
  if(file_exists("$DIRPRUEBA/.block")){
      echo "<p style='color:red'>La prueba esta deshabilitada.</a>";
      return 0;
  }

echo "<form>";
echo<<<CONTENIDO
Documento de Identidad:<input type="text" name="cedula"><br/>
Palabra secreta:<input type="password" name="palabra"><br/>
<i style="font-size:12px">Escoge una palabra corta de facil recordacion</i><br/>
<input type="submit" name="accion" value="presenta">
CONTENIDO;
 if(file_exists("$DIRPRUEBA/.califica")){
   echo "<input type='submit' name='accion' value='califica'>";
 }

}
echo "</form>";
}
?>
