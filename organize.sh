cd respuestas
for estudiante in *.txt
do
    cedula=$(echo $estudiante | cut -f 1 -d '.')
    mkdir -p $cedula
    mv $estudiante $cedula/respuestas.txt
done
cd -
