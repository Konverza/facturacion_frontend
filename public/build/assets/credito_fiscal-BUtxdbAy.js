import{A as F}from"./autocomplete-i5sh6eXG.js";import"./autocomplete-BhlH6aML.js";let h=null,S=null,i=null,c=null,x=null,f=[],E=0,C=0,r=[],u=0,v=0,m=0;localStorage.getItem("items_ccf")&&(r=JSON.parse(localStorage.getItem("items_ccf")),p(),g(),s());localStorage.getItem("reteIva1_ccf")&&(u=parseFloat(localStorage.getItem("reteIva1_ccf")),$("#checkIvaRete1").prop("checked",!0),p(),g(),s());localStorage.getItem("perciIva1_ccf")&&(m=parseFloat(localStorage.getItem("perciIva1_ccf")),$("#checkIvaPerci1").prop("checked",!0),p(),g(),s());localStorage.getItem("reteRenta_ccf")&&(v=parseFloat(localStorage.getItem("reteRenta_ccf")),$("#checkReteRenta").prop("checked",!0),p(),g(),s());$(function(){$.ajax({url:"/catalogo/cat_019",success:function(t){const e=document.getElementById("codActividad");new F(e,{data:t,maximumItems:5,threshold:1,fullWidth:!0}),x=t}}),$.ajax({url:"/departamentos/all",success:function(t){const e=$("#departamentoContribuyente");e.empty(),t.forEach(a=>{e.append(new Option(a.valores,a.codigo))}),e.on("change",function(){const a=$(this).val(),o=$("#municipioContribuyente");o.empty(),a&&t.find(n=>n.codigo===a).municipios.forEach(n=>{o.append(new Option(n.valores,n.codigo))})}),$("#departamentoContribuyente").trigger("change")}}),setInterval(()=>{const t=new Date,e=String(t.getHours()).padStart(2,"0"),a=String(t.getMinutes()).padStart(2,"0"),o=String(t.getSeconds()).padStart(2,"0"),l=`${e}:${a}:${o}`;$("#horaDTE").val(l)},1e3),$("#fechaDTE").val(new Date().toISOString().split("T")[0]),$("#descuento, #cantidad, #precio").on("change",function(){let t=$("#cantidad").val(),e=$("#precio").val(),a=$("#descuento").val(),o=I(t,e,a);console.log(o),$("#total").val(o),c.cantidad=t,c.precioUni=e,c.montoDescu=a;let l=$("#tipoVenta").val();l=="gravada"?c.ventaGravada=o:l=="exenta"?c.ventaExenta=o:l=="noSujeta"&&(c.ventaNoSuj=o),_(l,a)}),$("#agregar_item").on("click",function(){let t=$("#unidad").val(),e=$("#cantidad").val(),a=$("#descuento").val()||0,o=$("#producto").val();switch(c.id=r.length+1,c.tipoItem=$("#tipoItem").val(),c.uniMedida=t,c.montoDescu=a,c.descripcion=o,c.cantidad=e,$("#tipoVenta").val()){case"gravada":c.ventaGravada=c.precioUni*e;break;case"exenta":c.ventaExenta=c.precioUni*e;break;case"noSujeta":c.ventaNoSuj=c.precioUni*e;break}r.push(c),y(),p(),localStorage.setItem("items_ccf",JSON.stringify(r)),$("#cantidad").val(""),$("#precio").val(""),$("#descuento").val(""),$("#total").val(""),$("#producto").val(""),s()}),$("#items").on("click",".eliminar",function(){let t=$(this).data("id");r=r.filter(e=>e.id!==t),p(),s(),localStorage.setItem("items_ccf",JSON.stringify(r))}),$("#guardarDescuento").on("click",function(){let t=$("#descVentasGravadas").val();C=parseFloat(t),s(),$("#descuentosTotal").text("$"+C.toFixed(2))}),$("#generarDocumento").on("click",function(){k()}),$("#prodExistenteModal").on("show.bs.modal",function(t){i=null,$("#prodSeleccionado").addClass("d-none"),$.ajaxSetup({Headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")}}),h?h.ajax.reload():(h=$("#tablaProds").DataTable({processing:!0,serverSide:!0,ajax:{url:"/business/obtener_productos",type:"POST",data:function(e){e._token=$('meta[name="csrf-token"]').attr("content"),e.search=$('input[type="search"]').val()}},order:["1","DESC"],pageLength:10,searching:!0,aoColumns:[{data:"codigo"},{data:"descripcion"},{data:"precioSinTributos",render:function(e,a,o){return`$${parseFloat(e).toFixed(2)}`}},{data:"id",width:"20%",render:function(e,a,o){return`
                                <button type="button" class="btn btn-primary btn-sm btnSeleccionarProd" data-id="${o.id}">Seleccionar</button>
                            `}}],buttons:[],language:{url:"https://cdn.datatables.net/plug-ins/2.1.5/i18n/es-ES.json"}}),h.on("click",".btnSeleccionarProd",function(){const e=$(this).data("id");$.ajax({url:`/business/obtener_producto/${e}`,success:function(a){$("#prodSeleccionado").removeClass("d-none"),i=a,i.precioUni=i.precioSinTributos,$("#prodDesc").text(i.descripcion)}})}))}),$("#clienteExistenteModal").on("show.bs.modal",function(t){$.ajaxSetup({Headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")}}),S?S.ajax.reload():(S=$("#tablaClientes").DataTable({processing:!0,serverSide:!0,ajax:{url:"/business/obtener_clientes",type:"POST",data:function(e){e._token=$('meta[name="csrf-token"]').attr("content"),e.search=$('input[type="search"]').val()}},order:["1","DESC"],pageLength:10,searching:!0,aoColumns:[{data:"numDocumento",render:function(e,a,o){return o.tipoDocumento=="13"?o.numDocumento.slice(0,-1)+"-"+o.numDocumento.slice(-1):o.numDocumento}},{data:"nombre"},{data:"id",width:"20%",render:function(e,a,o){return`
                                <button type="button" class="btn btn-primary btn-sm btnSeleccionarCliente" data-id="${o.id}">Seleccionar</button>
                            `}}],buttons:[],language:{url:"https://cdn.datatables.net/plug-ins/2.1.5/i18n/es-ES.json"}}),S.on("click",".btnSeleccionarCliente",function(){const e=$(this).data("id");$.ajax({url:`/business/obtener_cliente/${e}`,success:function(a){$("#tipoDoc").val(a.tipoDocumento),$("#nitContribuyente").val(a.numDocumento),$("#nrcContribuyente").val(a.nrc.replace(/-/g,"")),$("#nombre").val(a.nombre),$("#nombreComercial").val(a.nombreComercial),$("#departamentoContribuyente").val(a.departamento),$("#departamentoContribuyente").trigger("change"),$("#municipioContribuyente").val(a.municipio),$("#complementoContribuyente").val(a.complemento),$("#correoContribuyente").val(a.correo),$("#telefonoContribuyente").val(a.telefono),$("#cerrarModalCliente").trigger("click");const o=x.find(l=>l.value===a.codActividad);o&&$("#codActividad").val(o.label)}})}))}),$("#aggitem").on("show.bs.modal",function(t){y()}),$("#aggitem .form-check-input").on("change",function(){_($("#tipoVenta").val(),$("#descuento").val())}),$("#cantidadExistente, #descuentoExistente").on("change",function(){let t=$("#cantidadExistente").val()||0,e=$("#descuentoExistente").val()||0,a=I(t,i.precioUni,e);$("#totalExistente").val(parseFloat(a).toFixed(4))}),$("#btnAgregarProd").on("click",function(){let t=$("#cantidadExistente").val()||0,e=$("#descuentoExistente").val()||0,a=$("#tipoVentaExistente").val();switch(i.id=r.length+1,i.cantidad=t,i.montoDescu=e,i.ventaGravada=0,i.ventaExenta=0,i.ventaNoSuj=0,i.tributos.forEach(o=>{if(o.codigo=="20"&&a!="gravada"){i.tributos=i.tributos.filter(l=>l.codigo!=="20");return}o.es_porcentaje?o.calculado=i.precioUni*o.valor*i.cantidad:o.calculado=o.valor*i.cantidad}),a){case"gravada":i.ventaGravada=$("#totalExistente").val();break;case"exenta":i.ventaExenta=$("#totalExistente").val();break;case"noSujeta":i.ventaNoSuj=$("#totalExistente").val();break}r.push(i),p(),localStorage.setItem("items_ccf",JSON.stringify(r)),$("#cantidadExistente").val(""),$("#descuentoExistente").val(""),$("#totalExistente").val(""),s()}),$("#checkIvaRete1").on("change",function(){if($(this).prop("checked")){let t=0;r.forEach(e=>{t+=parseFloat(e.ventaGravada)}),u=t*.01,localStorage.setItem("reteIva1",u)}else u=0,localStorage.removeItem("reteIva1_ccf");s()}),$("#checkReteRenta").on("change",function(){if($(this).prop("checked")){let t=0;r.forEach(e=>{t+=parseFloat(e.ventaGravada)}),v=t*.1,localStorage.setItem("reteRenta_ccf",v)}else v=0,localStorage.removeItem("reteRenta_ccf");s()}),$("#checkIvaPerci1").on("change",function(){if($(this).prop("checked")){let t=0;r.forEach(e=>{t+=parseFloat(e.ventaGravada)}),m=t*.01,localStorage.setItem("perciIva1_ccf",m)}else m=0,localStorage.removeItem("perciIva1_ccf");s()}),$("#cancelarDTE").on("click",function(){Swal.fire({title:"¿Cancelar generación de DTE?",text:"Se perderá toda la información ingresada",icon:"warning",showCancelButton:!0,confirmButtonColor:"#3085d6",cancelButtonColor:"#d33",confirmButtonText:"Sí, Cancelar",cancelButtonText:"No"}).then(t=>{t.isConfirmed&&(localStorage.removeItem("items_ccf"),localStorage.removeItem("reteIva1_ccf"),localStorage.removeItem("reteRenta_ccf"),window.location="/business/dashboard")})})});function I(t=0,e=0,a=0){return(t*e-a).toFixed(4)}function s(){D();let t=0,e=0,a=0;r.forEach(o=>{t+=parseFloat(o.ventaGravada)+parseFloat(o.ventaExenta)+parseFloat(o.ventaNoSuj)}),e=t-C+E,a=e-u-v+m,$("#reteIVA").text("$"+u.toFixed(2)),$("#reteRenta").text("$"+v.toFixed(2)),$("#perciIVA").text("$"+m.toFixed(2)),$("#subTotalGeneral").text("$"+t.toFixed(2)),$("#montoTotalOperacion").text("$"+e.toFixed(2)),$("#totalPagar").text("$"+a.toFixed(2)),$("#monto").val(a)}function k(){var l;$("#loadingOverlay").removeClass("d-none");const t=(l=x.find(n=>n.label===$("#codActividad").val()))==null?void 0:l.value,e=$("#codActividad").val().split("-").pop().trim();let a={nombre:$("#nombre").val(),nombreComercial:$("#nombreComercial").val(),codActividad:t,descActividad:e,telefono:$("#telefonoContribuyente").val(),correo:$("#correoContribuyente").val(),direccion:{departamento:$("#departamentoContribuyente").val(),municipio:$("#municipioContribuyente").val(),complemento:$("#complementoContribuyente").val()},nit:$("#nitContribuyente").val(),nrc:$("#nrcContribuyente").val()},o={nit:$("#nit").val(),receptor:a,cuerpoDocumento:[],documentoRelacionado:null,ventaTercero:null,resumen:{descuNoSuj:0,descuExtenta:0,descuGravada:0,porcentajeDescuento:0,ivaRete1:u.toFixed(2),ivaPerci1:m.toFixed(2),reteRenta_ccf:v.toFixed(2),saldoFavor:0,condicionOperacion:1},extension:null,apendice:null,pagos:null,numPagoElectronico:null};$("#nitVentaTerceros").val()!=""&&$("#nombreVentaTerceros").val()!=""&&(o.ventaTercero={nit:$("#nitVentaTerceros").val(),nombre:$("#nombreVentaTerceros").val()}),$("#docuEntrega").val()!=""&&$("#nombEntrega").val()!=""&&$("#docuRecibe").val()!=""&&$("#nombRecibe").val()!=""&&(o.extension={docuEntrega:$("#docuEntrega").val(),nombEntrega:$("#nombEntrega").val(),docuRecibe:$("#docuRecibe").val(),nombRecibe:$("#nombRecibe").val()}),r.forEach(n=>{let d=[];n.tributos.forEach(b=>{b.codigo!=="20"&&d.push(b.codigo)}),d.length==0&&(d=null),o.cuerpoDocumento.push({tipoItem:n.tipoItem,numeroDocumento:null,cantidad:n.cantidad,codigo:n.codigo||null,codTributo:null,uniMedida:n.uniMedida,descripcion:n.descripcion,precioUni:n.precioUni,montoDescu:n.montoDescu,ventaNoSuj:n.ventaNoSuj,ventaExenta:n.ventaExenta,ventaGravada:n.ventaGravada,tributos:d,psv:n.precioUni,noGravado:0})}),o.resumen.tributos=f,$.ajax({url:"/business/factura?dte=credito_fiscal",method:"POST",data:JSON.stringify(o),contentType:"application/json",success:function(n){n.status==201?n.message.estado=="PROCESADO"?Swal.fire({icon:"success",title:"Crédito Fiscal generado",text:"El Crédito Fiscal ha sido generado exitosamente",showConfirmButton:!1,timer:2e3}).then(()=>{$("#loadingOverlay").addClass("d-none"),localStorage.removeItem("items_ccf"),localStorage.removeItem("reteIva1_ccf"),localStorage.removeItem("reteRenta_ccf"),window.location.href="/business/dtes"}):n.message.estado=="CONTINGENCIA"?Swal.fire({icon:"warning",title:"Crédito Fiscal generado en CONTINGENCIA",text:"Se generó el Crédito Fiscal, pero no se envió a MH",showConfirmButton:!1,timer:2e3}).then(()=>{$("#loadingOverlay").addClass("d-none"),window.location.href="/business/dtes"}):Swal.fire({icon:"error",title:"Crédito Fiscal Rechazado",text:`Motivo: ${n.message.observaciones}`}).then(()=>{$("#loadingOverlay").addClass("d-none")}):Swal.fire({icon:"error",title:"El Crédito Fiscal no se envió",text:"Ha ocurrido un error, verifica los datos e intenta de nuevo",showConfirmButton:!1,timer:2e3}).then(()=>{$("#loadingOverlay").addClass("d-none")})},error:function(n){console.error(n),Swal.fire({icon:"error",title:"Error",text:"Ha ocurrido un error al generar el Crédito Fiscal",showConfirmButton:!1,timer:2e3})}})}function p(){let t=$("#items");t.empty(),r.forEach(e=>{t.append(`
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
        `)})}function _(t="gravada",e=0){$(".form-check-input").each(function(){if($(this).prop("checked")){let n={codigo:$(this).val(),descripcion:$(this).next("label").text().trim(),valor:$(this).data("valor"),es_porcentaje:$(this).data("porcentaje"),calculado:0};c.tributos.some(d=>d.codigo===n.codigo)||c.tributos.push(n)}else c.tributos=c.tributos.filter(n=>n.codigo!==$(this).val())});let a="",o=0;c.tributos.forEach(n=>{if(n.codigo=="20"&&t!="gravada"){c.tributos=c.tributos.filter(b=>b.codigo!=="20");return}let d=0;n.es_porcentaje?d=(c.precioUni*c.cantidad-e)*n.valor:d=n.valor*c.cantidad,a+=`
            <div class="alert alert-info" role="alert">
                ${n.descripcion}: $${d.toFixed(4)}
            </div>
        `,n.calculado=d,o+=d}),$("#tributosAplicados").html(a);const l=c.precioUni*c.cantidad-e+o;$("#total").val(l.toFixed(4))}function g(){f=[],E=0,r.forEach(t=>{t.tributos.forEach(e=>{f.some(a=>a.codigo===e.codigo)?f.find(a=>a.codigo===e.codigo).valor+=e.calculado:f.push({codigo:e.codigo,descripcion:e.descripcion,valor:e.calculado}),E+=e.calculado})})}function D(){g();let t=$("#tributos");t.empty(),f.forEach(e=>{t.append(`
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
        `)})}function y(){c={id:1,tipoItem:null,cantidad:null,codigo:null,uniMedida:null,descripcion:null,precioUni:0,montoDescu:0,ventaNoSuj:0,ventaExenta:0,ventaGravada:0,tributos:[],psv:0}}
