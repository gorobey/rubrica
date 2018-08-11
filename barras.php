<div id="graficaBarras"></div>

<script>

	$.ajax({
        url: "getNumberOfStudents.php",
        type: "POST",
        data: {},
        dataType: "json",
        success: function(data){
            console.log(data);
            var paralelos = new Array();
            var cuantos = new Array();
            $.each(data,function(key,value){
                paralelos.push(value.paralelo);
                numero = Number(value.numero);
                cuantos.push(numero);
            });
            graficar(paralelos, cuantos, "graficaBarras");
        }
    });

    function graficar(paralelos, cuantos, idDiv)
    {
        var xValue = paralelos;
        var yValue = cuantos;

        var trace1 = {
            x: xValue,
            y: yValue,
            type: 'bar',
            text: yValue,
            textposition: 'auto',
            hoverinfo: 'none',
            marker: {
                color: 'rgb(158,202,225)',
                opacity: 0.6,
                line: {
                color: 'rbg(8,48,107)',
                width: 1.5
                }
            }
        };

        var data = [trace1];

        var layout = {
            title: 'Número de estudiantes por paralelo'
        };

        Plotly.newPlot(idDiv, data, layout);
    }
</script>