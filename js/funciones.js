var cursor;
if (document.all) {
    // Está utilizando EXPLORER
    cursor='hand';
} else {
    // Está utilizando MOZILLA/NETSCAPE
    cursor='pointer';
}

function eliminaEspacios(cadena)
{
    var x=0, y=cadena.length-1;
    while(cadena.charAt(x)==" ") x++;
    while(cadena.charAt(y)==" ") y--;
    return cadena.substr(x, y-x+1);
}



