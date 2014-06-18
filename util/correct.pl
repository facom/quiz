$a=`ls -m`;
@files=split /\s*,\s*/,$a;
chomp @files;

foreach $file (@files)
{
    next if($file=~/\W+/);
    print "FILE: $file\n";
    open(fl,"$file/respuestas.txt");
    @lines=<fl>;chomp @lines;
    close(fl);
    open(fn,">$file/respuestas.txt");
    $qadd=0;
    for $line (@lines){
	if($line=~s/(\w)\(\)/$1\(b\)/){
	    print "LINEA:$line, RESP:$1\n";
	    $respuesta=$1;
	    if($1=~/b/){
		print "RIGHT\n";
		$qadd=1;
	    }
	}
	if($line=~/\d\.\d/){
	    print "ANTES:$line\n";
	    $line=$line+$qadd;
	    $line=sprintf "%.1f",$line;
	    printf "DESPUES:%.1f\n",$line;
	}
	print fn "$line\n";
    }
    close(fn);
    print "\n";
}
