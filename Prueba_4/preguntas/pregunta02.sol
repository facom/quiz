#Descarga el archivo
wget http://astronomia.udea.edu.co/tmp/moon.tar
#Desempaca los archivos
tar -xf moon.tar
#Crea la animacion
convert *.jpg moon.gif
#Limpia
rm moon.tar *.jpg
