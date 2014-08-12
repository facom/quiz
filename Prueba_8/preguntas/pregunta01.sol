#-*- coding:utf-8 -*-
"""
Programa que gráfica una aproximación de pi usando la fórmula de
Euler:

   pi^2 / 6 = 1/1^2 + 1/2^2 + 1/3^2 + ...

Autor: Jorge I. Zuluaga
Última actualización: Agosto 12 de 2014
"""
from matplotlib import pyplot
from numpy import sqrt

#Arreglos para la gráfica: uno para los Ns y el otro para los valores
#de pi
Ns=[]
pis=[]

suma=0
for N in xrange(1,21):
    #Caclculo pi hasta el término N-esimo
    suma+=1.0/N**2
    pi=sqrt(6*suma)

    #Guardo el valor calculado en el arreglo
    Ns+=[N]
    pis+=[pi]

#Creo una figura
figura=pyplot.figure()

#Creo un gráfico dentro de la figura
grafico=figura.add_axes([0.1,0.1,0.8,0.8])

#Gráfico
grafico.plot(Ns,pis,marker='o',linewidth=0)

#Decoración
grafico.set_title(u"$\pi$ calculado con la fórmula de Euler")
grafico.set_xlabel("N")
grafico.set_ylabel(u"Aproximación de $\pi$")

#Grabo la figura
figura.savefig("pi.png")
