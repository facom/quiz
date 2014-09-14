#-*- coding:utf-8 -*-
from numpy import *
from matplotlib.pyplot import *

x=linspace(0,2*pi,1000)

figura=figure()
grafico=figura.add_axes([0.1,0.1,0.8,0.8])
grafico.plot(x,sin(x),label="sin(x)")
grafico.plot(x,cos(x),label="cos(x)")
grafico.plot(x,tan(x),'.',markersize=3,label="tan(x)")
grafico.set_xlabel(r"$\theta$ (rad)")
grafico.set_ylabel(r"f(x)")
grafico.set_title(u"Funciones Trigonométricas")
grafico.set_xlim((0.0,2*pi))
grafico.set_ylim((-1.1,1.1))
grafico.legend()
grafico.grid()
figura.savefig("pregunta01.ens.png")
