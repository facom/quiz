#include <math.h>
#include <stdio.h>

int main(void)
{
  float h,vo,a,b,c,discriminante,t1,t2,g,tiempo;
  
  printf("Entre la altura (metros):");
  scanf("%f",&h);
  printf("Entre la velocidad (m/s):");
  scanf("%f",&vo);
  
  g=9.8;
  a=-0.5*g;
  b=vo;
  c=h;

  discriminante=b*b-4*a*c;
  if(discriminante<0){
    printf("El problema no tiene solución\n");
    return 1;
  }
  t1=(-b+sqrt(discriminante))/(2*a);
  t2=(-b-sqrt(discriminante))/(2*a);
  
  if(t1>0){
    tiempo=t1;
  }else{
    tiempo=t2;
  }
  printf("El tiempo de caída del cuerpo es: %f segundos\n",tiempo);

  return 0;
}
