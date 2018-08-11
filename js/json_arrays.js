addEvent(window,'load',inicializarEventos,false);

function inicializarEventos()
{
  var ob=document.getElementById('boton1');
  addEvent(ob,'click',presionBoton,false);
}

var conexion1;
function presionBoton(e)
{
  conexion1=crearXMLHttpRequest();
  conexion1.onreadystatechange = procesarEventos;
  conexion1.open('GET','devuelve_json_array.php', true);
  conexion1.send(null);
}

function procesarEventos()
{
  var resultados = document.getElementById("resultados");
  if(conexion1.readyState == 4)
  {
    alert('Cadena en formato JSON:  '+conexion1.responseText);

    var datos=eval("(" + conexion1.responseText + ")");
    var salida='';
    for(f=0;f<datos.length;f++)
    {
      salida += 'id_escala_calificaciones:'+datos[f].id_escala_calificaciones+"<br>";
      salida += 'ec_nota_minima:'+datos[f].ec_nota_minima+"<br>";
      salida += 'ec_nota_maxima:'+datos[f].ec_nota_maxima+"<br>";
	  salida += 'ec_equivalencia:'+datos[f].ec_equivalencia+"<br><br>";
    }
    resultados.innerHTML = salida;
  } 
  else 
  {
    resultados.innerHTML = "Cargando...";
  }
}

//***************************************
//Funciones comunes a todos los problemas
//***************************************
function addEvent(elemento,nomevento,funcion,captura)
{
  if (elemento.attachEvent)
  {
    elemento.attachEvent('on'+nomevento,funcion);
    return true;
  }
  else  
    if (elemento.addEventListener)
    {
      elemento.addEventListener(nomevento,funcion,captura);
      return true;
    }
    else
      return false;
}

function crearXMLHttpRequest() 
{
  var xmlHttp=null;
  if (window.ActiveXObject) 
    xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
  else 
    if (window.XMLHttpRequest) 
      xmlHttp = new XMLHttpRequest();
  return xmlHttp;
}