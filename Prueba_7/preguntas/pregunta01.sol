#-*-coding:utf-8-*-
"""
Este programa calcula la suma de los cuadrados y el cuadrado de la
suma de los primeros N números naturales.
Elaborado por: Jorge Zuluaga
Última actualización: Agosto 5 de 2014
"""

#Entrada
N=input("Entre un número natural: ")

#Verifica la condicion sobre N
if N<1:
    print "El número debe ser mayor que 1"
    exit(0)

#Suma de los primeros N cuadrados
suma=0
#Como empiezo en 1 el xrange debe ir hasta N+1
for i in xrange(1,N+1):
    #Uso "suma+=..." es equivalente a "suma=suma+..."
    suma+=i**2
#Salida con formato
print "La suma de los primeros %d cuadrados es: %d"%(N,suma)

#Suma de los primeros N números
suma2=0
for i in xrange(1,N+1):
    suma2+=i
#Reciclo la variable suma2
suma2=suma2**2
print "El cuadrado de la suma de los primeros %d numeros es: %d"%(N,suma2)
print "La diferencia es: ",suma2-suma

