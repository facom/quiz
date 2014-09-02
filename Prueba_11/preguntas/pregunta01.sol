#include <math.h>
#include <stdio.h>

int main(void)
{
  FILE *archivo;
  int D,Do;
  float M,Dt;

  printf("Tabulando la ecuación del tiempo...\n");
  archivo=fopen("edt.dat","w");
  for(D=1;D<=365;D++){
    M=6.24+0.017*D;
    Dt=-7.659*sin(M)+9.863*sin(2*M+3.5932);
    fprintf(archivo,"%d %f\n",D,Dt);
  }
  fclose(archivo);
  printf("Tabla guardada en 'edt.dat'...\n");
  printf("Puede graficarla con gnuplot.\n");

  return 0;
}
