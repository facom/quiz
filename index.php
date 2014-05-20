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
echo<<<CONTENIDO
<input type='hidden' name='password' value='$password'>
<h3>Profesor</h3>
$button
CONTENIDO;
 echo "</form>";
  return;
}

////////////////////////////////////////////////////////////
//PRESENTA
////////////////////////////////////////////////////////////
if($_GET["accion"]=="presenta"){

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //CONTROL DE CEDULA
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  $fresultado="$DIRPRUEBA/respuestas/$cedula.txt";
  if(file_exists($fresultado)){
    $nota=rtrim(shell_exec("tail -n 1 $fresultado"));
    $palabra=rtrim(shell_exec("head -n 1 $fresultado"));
    errorMsg("$cedula: tu unica oportunidad de presentar el examen ya paso (palabra
clave '$palabra').  Obtuviste una nota de: <b>$nota</b>");
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

echo<<<CONTENIDO
  <p>
  <input type='submit' name='accion' value='enviar'>
  </p>
  </form>
CONTENIDO;
  homeLink();
}else if($accion=="enviar"){

  //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
  //CALIFICAR
  //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
echo<<<CONTENIDO
Su repuesta ha sido recibida.  Preguntas $numpreguntas.
<p></p>
CONTENIDO;
 $s=0;
 $fl=fopen("$DIRPRUEBA/respuestas/$cedula.txt","w");
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
  echo "<p style='font-size:20px'><b>Nota</b>: $nota</p><br/>";
  
  homeLink();
}else{
////////////////////////////////////////////////////////////
//PRINCIPAL
////////////////////////////////////////////////////////////
  if(file_exists("$DIRPRUEBA/.block")){
      echo "<p style='color:red'>La prueba esta deshabilitada.</a>";
      return 0;
  }

if(isset($_GET['profesor']) and !isset($_POST['password'])){
echo "<form method='post'>";
echo<<<CONTENIDO
Password:<input type="password" name="password"><br/>
<input type="submit" name="accion" value="accede">
CONTENIDO;
}else{
echo "<form>";
echo<<<CONTENIDO
Documento de Identidad:<input type="text" name="cedula"><br/>
Palabra secreta:<input type="password5B" name="palabra"><br/>
<i style="font-size:12px">Escoge una palabra corta de facil recordacion</i><br/>
<input type="submit" name="accion" value="presenta">
CONTENIDO;
}
echo "</form>";
}
?>
