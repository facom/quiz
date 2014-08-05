#-*- coding: utf-8 -*-
from numpy import *

N=input("Entre el numero de terminos: ")
suma=0
for i in xrange(1,N+1):
    suma+=1./i**2
pibas=(6*suma)**0.5

print "Para ", N, "terminos, con la aproximacion de Basilea, pi es", pibas
diff=abs(pi-pibas/pi)
print "la diferencia porcentual entre pi de numpy y el de Basilea es", diff, "porciento"

