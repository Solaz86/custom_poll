
zingchart.MODULESDIR = "https://cdn.zingchart.com/modules/";
ZC.LICENSE = ["569d52cefae586f634c54f86dc99e6a9","ee6b7db5b51705a13dc2339db3edaf6d"];
var tecnologia = drupalSettings.poll_graph.stats.tecnologia;
var innovacion = drupalSettings.poll_graph.stats.innovacion;
var habilidades = drupalSettings.poll_graph.stats.habilidades;
var promedio = drupalSettings.poll_graph.stats.;
// alert(tecnologia);

    var myConfig = {
      type : 'radar',
      plot : {
        aspect : 'area',
        animation: {
          effect:3,
          sequence:1,
          speed:700
        }
      },
      scaleV : {
        visible : false
      },
      scaleK : {
        values : '3:1',
        labels : ['Tecnologia','Habilidades blandas','Innovación'],
        item : {
          fontColor : '#607D8B',
          backgroundColor : "white",
          borderColor : "#aeaeae",
          borderWidth : 1,
          padding : '5 10',
          borderRadius : 10
        },
        refLine : {
          lineColor : '#c10000'
        },
        tick : {
          lineColor : '#59869c',
          lineWidth : 2,
          lineStyle : 'dotted',
          size : 20
        },
        guide : {
          lineColor : "#607D8B",
          lineStyle : 'solid',
          alpha : 0.3,
          backgroundColor : "#c5c5c5 #718eb4"
        }
      },
      series : [
        {
          values : [promedio, promedio, promedio],
          lineColor : 'red',
          backgroundColor : 'white'
        },
        {
          values : [tecnologia, habilidades, innovacion],
          lineColor : 'blue',
          backgroundColor : 'white'
        },
      ]
    };
    zingchart.render({
      id : 'myChart',
      data : myConfig,
      height: '100%',
      width: '100%'
    });

