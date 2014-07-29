#-*- coding: utf-8 -*-
N=input("Entre el número de términos: ")
t1=1
t2=1
print t1
print t2
for i in xrange(3,N+1):
    termino=t1+t2
    t1=t2
    t2=termino
    print termino
print "Bonito, ¿No?"
