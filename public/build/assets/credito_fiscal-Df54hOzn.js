import{A as k}from"./autocomplete-i5sh6eXG.js";import"./autocomplete-BhlH6aML.js";let b=null,h=null,c=null,n=null,S=null,p=[],x=0,E=0,l=[],s=0,u=0,v=0;localStorage.getItem("items")&&(l=JSON.parse(localStorage.getItem("items")),m(),f(),d());localStorage.getItem("reteIva1")&&(s=parseFloat(localStorage.getItem("reteIva1")),$("#checkIvaRete1").prop("checked",!0),m(),f(),d());localStorage.getItem("perciIva1")&&(v=parseFloat(localStorage.getItem("perciIva1")),$("#checkIvaPerci1").prop("checked",!0),m(),f(),d());localStorage.getItem("reteRenta")&&(u=parseFloat(localStorage.getItem("reteRenta")),$("#checkReteRenta").prop("checked",!0),m(),f(),d());$(function(){$.ajax({url:"/catalogo/cat_019",success:function(t){const e=document.getElementById("codActividad");new k(e,{data:t,maximumItems:5,threshold:1,fullWidth:!0}),S=t}}),$.ajax({url:"/departamentos/all",success:function(t){const e=$("#departamentoContribuyente");e.empty(),t.forEach(a=>{e.append(new Option(a.valores,a.codigo))}),e.on("change",function(){const a=$(this).val(),o=$("#municipioContribuyente");o.empty(),a&&t.find(i=>i.codigo===a).municipios.forEach(i=>{o.append(new Option(i.valores,i.codigo))})}),$("#departamentoContribuyente").trigger("change")}}),setInterval(()=>{const t=new Date,e=String(t.getHours()).padStart(2,"0"),a=String(t.getMinutes()).padStart(2,"0"),o=String(t.getSeconds()).padStart(2,"0"),r=`${e}:${a}:${o}`;$("#horaDTE").val(r)},1e3),$("#fechaDTE").val(new Date().toISOString().split("T")[0]),$("#descuento, #cantidad, #precio").on("change",function(){let t=$("#cantidad").val(),e=$("#precio").val(),a=$("#descuento").val(),o=I(t,e,a);$("#total").val(o),n.cantidad=t,n.precioUni=e,n.montoDescu=a;let r=$("#tipoVenta").val();r=="gravada"?n.ventaGravada=o:r=="exenta"?n.ventaExenta=o:r=="noSujeta"&&(n.ventaNoSuj=o),y()}),$("#agregar_item").on("click",function(){let t=$("#unidad").val(),e=$("#cantidad").val(),a=$("#descuento").val()||0,o=$("#producto").val();switch(n.id=l.length+1,n.tipoItem=$("#tipoItem").val(),n.uniMedida=t,n.montoDescu=a,n.descripcion=o,n.cantidad=e,$("#tipoVenta").val()){case"gravada":n.ventaGravada=n.precioUni*e;break;case"exenta":n.ventaExenta=n.precioUni*e;break;case"noSujeta":n.ventaNoSuj=n.precioUni*e;break}l.push(n),F(),m(),localStorage.setItem("items",JSON.stringify(l)),$("#cantidad").val(""),$("#precio").val(""),$("#descuento").val(""),$("#total").val(""),$("#producto").val(""),d()}),$("#items").on("click",".eliminar",function(){let t=$(this).data("id");l=l.filter(e=>e.id!==t),m(),d(),localStorage.setItem("items",JSON.stringify(l))}),$("#guardarDescuento").on("click",function(){let t=$("#descVentasGravadas").val();E=parseFloat(t),d(),$("#descuentosTotal").text("$"+E.toFixed(2))}),$("#generarDocumento").on("click",function(){D()}),$("#prodExistenteModal").on("show.bs.modal",function(t){c=null,$("#prodSeleccionado").addClass("d-none"),$.ajaxSetup({Headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")}}),b?b.ajax.reload():(b=$("#tablaProds").DataTable({processing:!0,serverSide:!0,ajax:{url:"/business/obtener_productos",type:"POST",data:function(e){e._token=$('meta[name="csrf-token"]').attr("content"),e.search=$('input[type="search"]').val()}},order:["1","DESC"],pageLength:10,searching:!0,aoColumns:[{data:"codigo"},{data:"descripcion"},{data:"precioSinTributos",render:function(e,a,o){return`$${parseFloat(e).toFixed(2)}`}},{data:"id",width:"20%",render:function(e,a,o){return`
                                <button type="button" class="btn btn-primary btn-sm btnSeleccionarProd" data-id="${o.id}">Seleccionar</button>
                            `}}],buttons:[],language:{url:"https://cdn.datatables.net/plug-ins/2.1.5/i18n/es-ES.json"}}),b.on("click",".btnSeleccionarProd",function(){const e=$(this).data("id");$.ajax({url:`/business/obtener_producto/${e}`,success:function(a){$("#prodSeleccionado").removeClass("d-none"),c=a,c.precioUni=c.precioSinTributos,$("#prodDesc").text(c.descripcion)}})}))}),$("#clienteExistenteModal").on("show.bs.modal",function(t){$.ajaxSetup({Headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")}}),h?h.ajax.reload():(h=$("#tablaClientes").DataTable({processing:!0,serverSide:!0,ajax:{url:"/business/obtener_clientes",type:"POST",data:function(e){e._token=$('meta[name="csrf-token"]').attr("content"),e.search=$('input[type="search"]').val()}},order:["1","DESC"],pageLength:10,searching:!0,aoColumns:[{data:"numDocumento",render:function(e,a,o){return o.tipoDocumento=="13"?o.numDocumento.slice(0,-1)+"-"+o.numDocumento.slice(-1):o.numDocumento}},{data:"nombre"},{data:"id",width:"20%",render:function(e,a,o){return`
                                <button type="button" class="btn btn-primary btn-sm btnSeleccionarCliente" data-id="${o.id}">Seleccionar</button>
                            `}}],buttons:[],language:{url:"https://cdn.datatables.net/plug-ins/2.1.5/i18n/es-ES.json"}}),h.on("click",".btnSeleccionarCliente",function(){const e=$(this).data("id");$.ajax({url:`/business/obtener_cliente/${e}`,success:function(a){$("#tipoDoc").val(a.tipoDocumento),$("#nitContribuyente").val(a.numDocumento),$("#nrcContribuyente").val(a.nrc.replace(/-/g,"")),$("#nombre").val(a.nombre),$("#nombreComercial").val(a.nombreComercial),$("#departamentoContribuyente").val(a.departamento),$("#departamentoContribuyente").trigger("change"),$("#municipioContribuyente").val(a.municipio),$("#complementoContribuyente").val(a.complemento),$("#correoContribuyente").val(a.correo),$("#telefonoContribuyente").val(a.telefono),$("#cerrarModalCliente").trigger("click");const o=S.find(r=>r.value===a.codActividad);o&&$("#codActividad").val(o.label)}})}))}),$("#aggitem").on("show.bs.modal",function(t){F()}),$("#aggitem .form-check-input").on("change",function(){y()}),$("#cantidadExistente, #descuentoExistente").on("change",function(){let t=$("#cantidadExistente").val()||0,e=$("#descuentoExistente").val()||0,a=I(t,c.precioUni,e);$("#totalExistente").val(parseFloat(a).toFixed(4))}),$("#btnAgregarProd").on("click",function(){let t=$("#cantidadExistente").val()||0,e=$("#descuentoExistente").val()||0;switch(c.id=l.length+1,c.cantidad=t,c.montoDescu=e,c.ventaGravada=0,c.ventaExenta=0,c.ventaNoSuj=0,c.tributos.forEach(a=>{a.es_porcentaje?a.calculado=c.precioUni*a.valor*c.cantidad:a.calculado=a.valor*c.cantidad}),$("#tipoVentaExistente").val()){case"gravada":c.ventaGravada=$("#totalExistente").val();break;case"exenta":c.ventaExenta=$("#totalExistente").val();break;case"noSujeta":c.ventaNoSuj=$("#totalExistente").val();break}l.push(c),m(),localStorage.setItem("items",JSON.stringify(l)),$("#cantidadExistente").val(""),$("#descuentoExistente").val(""),$("#totalExistente").val(""),d()}),$("#checkIvaRete1").on("change",function(){if($(this).prop("checked")){let t=0;l.forEach(e=>{t+=parseFloat(e.ventaGravada)}),s=t*.01,localStorage.setItem("reteIva1",s)}else s=0,localStorage.removeItem("reteIva1");d()}),$("#checkReteRenta").on("change",function(){if($(this).prop("checked")){let t=0;l.forEach(e=>{t+=parseFloat(e.ventaGravada)}),u=t*.1,localStorage.setItem("reteRenta",u)}else u=0,localStorage.removeItem("reteRenta");d()}),$("#checkIvaPerci1").on("change",function(){if($(this).prop("checked")){let t=0;l.forEach(e=>{t+=parseFloat(e.ventaGravada)}),v=t*.01,localStorage.setItem("perciIva1",v)}else v=0,localStorage.removeItem("perciIva1");d()}),$("#cancelarDTE").on("click",function(){Swal.fire({title:"¿Cancelar generación de DTE?",text:"Se perderá toda la información ingresada",icon:"warning",showCancelButton:!0,confirmButtonColor:"#3085d6",cancelButtonColor:"#d33",confirmButtonText:"Sí, Cancelar",cancelButtonText:"No"}).then(t=>{t.isConfirmed&&(localStorage.removeItem("items"),localStorage.removeItem("reteIva1"),localStorage.removeItem("reteRenta"),window.location="/business/dashboard")})})});function I(t=0,e=0,a=0){return(t*e-a).toFixed(4)}function d(){T();let t=0,e=0,a=0;l.forEach(o=>{t+=parseFloat(o.ventaGravada)+parseFloat(o.ventaExenta)+parseFloat(o.ventaNoSuj)}),e=t-E+x,a=e-s-u+v,$("#reteIVA").text("$"+s.toFixed(2)),$("#reteRenta").text("$"+u.toFixed(2)),$("#perciIVA").text("$"+v.toFixed(2)),$("#subTotalGeneral").text("$"+t.toFixed(2)),$("#montoTotalOperacion").text("$"+e.toFixed(2)),$("#totalPagar").text("$"+a.toFixed(2)),$("#monto").val(a)}function D(){var r;$("#loadingOverlay").removeClass("d-none");const t=(r=S.find(i=>i.label===$("#codActividad").val()))==null?void 0:r.value,e=$("#codActividad").val().split("-").pop().trim();let a={nombre:$("#nombre").val(),nombreComercial:$("#nombreComercial").val(),codActividad:t,descActividad:e,telefono:$("#telefonoContribuyente").val(),correo:$("#correoContribuyente").val(),direccion:{departamento:$("#departamentoContribuyente").val(),municipio:$("#municipioContribuyente").val(),complemento:$("#complementoContribuyente").val()},nit:$("#nitContribuyente").val(),nrc:$("#nrcContribuyente").val()},o={nit:$("#nit").val(),receptor:a,cuerpoDocumento:[],documentoRelacionado:null,ventaTercero:null,resumen:{descuNoSuj:0,descuExtenta:0,descuGravada:0,porcentajeDescuento:0,ivaRete1:s.toFixed(2),ivaPerci1:v.toFixed(2),reteRenta:u.toFixed(2),saldoFavor:0,condicionOperacion:1},extension:null,apendice:null,pagos:null,numPagoElectronico:null};$("#nitVentaTerceros").val()!=""&&$("#nombreVentaTerceros").val()!=""&&(o.ventaTercero={nit:$("#nitVentaTerceros").val(),nombre:$("#nombreVentaTerceros").val()}),l.forEach(i=>{let g=[];i.tributos.forEach(C=>{C.codigo!=="20"&&g.push(C.codigo)}),g.length==0&&(g=null),o.cuerpoDocumento.push({tipoItem:i.tipoItem,numeroDocumento:null,cantidad:i.cantidad,codigo:i.codigo||null,codTributo:null,uniMedida:i.uniMedida,descripcion:i.descripcion,precioUni:i.precioUni,montoDescu:i.montoDescu,ventaNoSuj:i.ventaNoSuj,ventaExenta:i.ventaExenta,ventaGravada:i.ventaGravada,tributos:g,psv:i.precioUni,noGravado:0})}),o.resumen.tributos=p,$.ajax({url:"/business/factura?dte=credito_fiscal",method:"POST",data:JSON.stringify(o),contentType:"application/json",success:function(i){i.status==201?i.message.estado=="PROCESADO"?Swal.fire({icon:"success",title:"Factura generada",text:"La factura ha sido generada exitosamente",showConfirmButton:!1,timer:2e3}).then(()=>{$("#loadingOverlay").addClass("d-none"),localStorage.removeItem("items"),localStorage.removeItem("reteIva1"),localStorage.removeItem("reteRenta"),window.location.href="/business/dtes"}):i.message.estado=="CONTINGENCIA"?Swal.fire({icon:"warning",title:"Factura generada en CONTINGENCIA",text:"Se generó la factura, pero no se envió a MH",showConfirmButton:!1,timer:2e3}).then(()=>{$("#loadingOverlay").addClass("d-none"),window.location.href="/business/dtes"}):Swal.fire({icon:"error",title:"Factura Rechazada",text:`Motivo: ${i.message.observaciones}`}).then(()=>{$("#loadingOverlay").addClass("d-none")}):Swal.fire({icon:"error",title:"La factura no se envió",text:"Ha ocurrido un error, verifica los datos e intenta de nuevo",showConfirmButton:!1,timer:2e3}).then(()=>{$("#loadingOverlay").addClass("d-none")})},error:function(i){console.error(i),Swal.fire({icon:"error",title:"Error",text:"Ha ocurrido un error al generar la factura",showConfirmButton:!1,timer:2e3})}})}function m(){let t=$("#items");t.empty(),l.forEach(e=>{t.append(`
            <tr>
                <td>${e.uniMedida}</td>
                <td>${e.descripcion}</td>
                <td>${e.cantidad}</td>
                <td>$${parseFloat(e.precioUni).toFixed(2)}</td>
                <td>$${parseFloat(e.montoDescu).toFixed(2)}</td>
                <td>
                    $${parseFloat(e.ventaGravada).toFixed(4)}
                </td>
                <td>
                    $${parseFloat(e.ventaExenta).toFixed(4)}
                </td>
                <td>
                    $${parseFloat(e.ventaNoSuj).toFixed(4)}
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm eliminar" data-id="${e.id}">Eliminar</button>
                </td>
            </tr>
        `)})}function y(){$(".form-check-input").each(function(){if($(this).prop("checked")){let o={codigo:$(this).val(),descripcion:$(this).next("label").text().trim(),valor:$(this).data("valor"),es_porcentaje:$(this).data("porcentaje"),calculado:0};n.tributos.some(r=>r.codigo===o.codigo)||n.tributos.push(o)}else n.tributos=n.tributos.filter(o=>o.codigo!==$(this).val())});let t="",e=0;n.tributos.forEach(o=>{let r=0;o.es_porcentaje?r=n.precioUni*o.valor*n.cantidad:r=o.valor*n.cantidad,t+=`
            <div class="alert alert-info" role="alert">
                ${o.descripcion}: $${r.toFixed(4)}
            </div>
        `,o.calculado=r,e+=r}),$("#tributosAplicados").html(t);const a=n.precioUni*n.cantidad+e;$("#total").val(a.toFixed(4))}function f(){p=[],x=0,l.forEach(t=>{t.tributos.forEach(e=>{p.some(a=>a.codigo===e.codigo)?p.find(a=>a.codigo===e.codigo).valor+=e.calculado:p.push({codigo:e.codigo,descripcion:e.descripcion,valor:e.calculado}),x+=e.calculado})})}function T(){f();let t=$("#tributos");t.empty(),p.forEach(e=>{t.append(`
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-end fw-bold">${e.descripcion}</td>
                <td>$${e.valor.toFixed(2)}</td>
            </tr>
        `)})}function F(){n={id:1,tipoItem:null,cantidad:null,codigo:null,uniMedida:null,descripcion:null,precioUni:0,montoDescu:0,ventaNoSuj:0,ventaExenta:0,ventaGravada:0,tributos:[],psv:0}}
