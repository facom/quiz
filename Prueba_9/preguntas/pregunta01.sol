#-*- coding:utf-8 -*-
"""
Movimiento con aceleración constante

Autor: Jorge I. Zuluaga
Última actualización: Agosto 19 de 2014
"""
from matplotlib import pyplot
from numpy import sqrt

a=2
t=0
dt=0.1
x=0
v=0

ts=[]
xs=[]
vs=[]
while v<20:
      x+=v*dt+0.5*a*dt**2
      v+=a*dt
      t+=dt
      ts+=[t]
      xs+=[x]
      vs+=[v]

#Creo una figura
figura=pyplot.figure()

#Creo un gráfico dentro de la figura
grafico_pos=figura.add_axes([0.1,0.1,0.85,0.35])
grafico_vel=figura.add_axes([0.1,0.55,0.85,0.35])

#Gráfico
grafico_pos.plot(ts,xs,marker='o',linewidth=1)
grafico_vel.plot(ts,vs,marker='o',linewidth=1)

#Decoración
grafico_pos.set_xlabel(u"t(s)")
grafico_pos.set_ylabel(u"x(m)")

#Decoración
grafico_vel.set_title(u"Posición y Velocidad")
grafico_vel.set_xlabel(u"t(s)")
grafico_vel.set_ylabel(u"v(m/s)")

#Grabo la figura
figura.savefig("posicion-velocidad.png")
