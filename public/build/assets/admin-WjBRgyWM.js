import{A as n}from"./autocomplete-i5sh6eXG.js";import"./autocomplete-BhlH6aML.js";$(function(){$.ajax({url:"/catalogo/cat_019",success:function(c){const o=document.getElementById("actividad_economica");new n(o,{data:c,maximumItems:5,threshold:1})}}),$.ajax({url:"/departamentos/all",success:function(c){const o=$("#departamentoSelect");o.empty(),c.forEach(t=>{o.append(new Option(t.valores,t.codigo))}),o.on("change",function(){const t=$(this).val(),i=$("#municipioSelect");i.empty(),t&&c.find(e=>e.codigo===t).municipios.forEach(e=>{i.append(new Option(e.valores,e.codigo))})})}})});
