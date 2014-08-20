prueba=$1
dirprueba="Prueba_$prueba"
if [ ! -d $dirprueba ];then
    echo "El directorio '$dirprueba' no ha sido creado..."
    exit 0
fi
echo "Adding $dirprueba to git..."
make clean
rm -rf $dirprueba/respuestas/*
find $dirprueba -type d -exec cp -rf tmp/.htaccess {} \;
git add -f $dirprueba/prueba.conf
git add -f $dirprueba/preguntas/*
git add -f $dirprueba/respuestas/.htaccess
