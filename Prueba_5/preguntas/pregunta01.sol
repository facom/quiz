1) Linea 1.  Esta invertido import y from. Correcto "from numpy import pi"

2) Línea 3. No se usa ":" para la asignacion. Correcto: "N=0"

3) Línea 4. Falta un ":" al final de la linea.  Correcto "while N>1:"

4) Línea 5. Faltan paréntesis. Correcto: input("...")

5) Línea 12. Es xrange.  Correcto: "for i in xrange(1,N+1):"

6) Línea 13. La multiplicación no puede quedar indicada.  Correcto: "factor=1/(2*i-1)"

7) Línea 14. La multipicación no es con "x" sino con "*".  Correcto:
   "suma=suma+signo*factor"

8) Línea 15. La variable sign nunca se ha usado.  Correcto: "signo=signo*(-1)"

9) Línea 17. La variable "sumar" no se ha usado.  Correcto: "valor = suma*4"

10) Línea 18. La rutina "abs" no existe.  Correcto: Colocar al
    principio: from numpy import abs

11) Línea 20. En la salida con formato se usa el "%" al final de la cadena.
    Correcto: print "..."%(N,valor)

12) Línea 21. Se usa incorrectamente el %.  Correcto: print "...",diferencia,"..."
