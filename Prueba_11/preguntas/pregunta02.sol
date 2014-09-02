set term png
set output "edt.png"
set title "Ecuación del Tiempo"
set xlabel "D (dias desde el primero de enero)"
set ylabel "Dt (minutos)"
set grid
plot "edt.dat" notitle with linespoints
