let v=null,f=null,i=null,n=null,u=[],b=0,h=0,r=[],d=0,s=0;localStorage.getItem("items")&&(r=JSON.parse(localStorage.getItem("items")),p(),g(),l());localStorage.getItem("reteIva1")&&(d=parseFloat(localStorage.getItem("reteIva1")),$("#checkIvaRete1").prop("checked",!0),p(),g(),l());localStorage.getItem("reteRenta")&&(s=parseFloat(localStorage.getItem("reteRenta")),$("#checkReteRenta").prop("checked",!0),p(),g(),l());$(function(){$.ajax({url:"/departamentos/all",success:function(a){const e=$("#departamentoContribuyente");e.empty(),a.forEach(t=>{e.append(new Option(t.valores,t.codigo))}),e.on("change",function(){const t=$(this).val(),o=$("#municipioContribuyente");o.empty(),t&&a.find(m=>m.codigo===t).municipios.forEach(m=>{o.append(new Option(m.valores,m.codigo))})}),$("#departamentoContribuyente").trigger("change")}}),setInterval(()=>{const a=new Date,e=String(a.getHours()).padStart(2,"0"),t=String(a.getMinutes()).padStart(2,"0"),o=String(a.getSeconds()).padStart(2,"0"),c=`${e}:${t}:${o}`;$("#horaDTE").val(c)},1e3),$("#fechaDTE").val(new Date().toISOString().split("T")[0]),$("#descuento, #cantidad, #precio").on("change",function(){let a=$("#cantidad").val(),e=$("#precio").val(),t=$("#descuento").val(),o=x(a,e,t);$("#total").val(o),n.cantidad=a,n.precioUni=e,n.montoDescu=t;let c=$("#tipoVenta").val();c=="gravada"?n.ventaGravada=o:c=="exenta"?n.ventaExenta=o:c=="noSujeta"&&(n.ventaNoSuj=o),S()}),$("#checkContribuyente").on("change",function(){$(this).prop("checked")?($("#nombreContribuyente").prop("disabled",!0),$("#telefonoContribuyente").prop("disabled",!0),$("#correoContribuyente").prop("disabled",!0),$("#departamentoContribuyente").prop("disabled",!0),$("#municipioContribuyente").prop("disabled",!0),$("#complementoContribuyente").prop("disabled",!0),$("#tipoDoc").prop("disabled",!0),$("#nitContribuyente").prop("disabled",!0)):($("#nombreContribuyente").prop("disabled",!1),$("#telefonoContribuyente").prop("disabled",!1),$("#correoContribuyente").prop("disabled",!1),$("#departamentoContribuyente").prop("disabled",!1),$("#municipioContribuyente").prop("disabled",!1),$("#complementoContribuyente").prop("disabled",!1),$("#tipoDoc").prop("disabled",!1),$("#nitContribuyente").prop("disabled",!1))}),$("#agregar_item").on("click",function(){let a=$("#unidad").val(),e=$("#cantidad").val(),t=$("#descuento").val()||0,o=$("#producto").val();switch(n.id=r.length+1,n.tipoItem=$("#tipoItem").val(),n.uniMedida=a,n.montoDescu=t,n.descripcion=o,n.cantidad=e,$("#tipoVenta").val()){case"gravada":n.ventaGravada=$("#total").val(),n.ivaItem=($("#total").val()/1.13*.13).toFixed(4);break;case"exenta":n.ventaExenta=$("#total").val(),n.ivaItem=0;break;case"noSujeta":n.ventaNoSuj=$("#total").val(),n.ivaItem=0;break}r.push(n),C(),p(),localStorage.setItem("items",JSON.stringify(r)),$("#cantidad").val(""),$("#precio").val(""),$("#descuento").val(""),$("#total").val(""),$("#producto").val(""),l()}),$("#items").on("click",".eliminar",function(){let a=$(this).data("id");r=r.filter(e=>e.id!==a),p(),l(),localStorage.setItem("items",JSON.stringify(r))}),$("#guardarDescuento").on("click",function(){let a=$("#descVentasGravadas").val();h=parseFloat(a),l(),$("#descuentosTotal").text("$"+h.toFixed(2))}),$("#generarDocumento").on("click",function(){y()}),$("#prodExistenteModal").on("show.bs.modal",function(a){i=null,$("#prodSeleccionado").addClass("d-none"),$.ajaxSetup({Headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")}}),v?v.ajax.reload():(v=$("#tablaProds").DataTable({processing:!0,serverSide:!0,ajax:{url:"/business/obtener_productos",type:"POST",data:function(e){e._token=$('meta[name="csrf-token"]').attr("content"),e.search=$('input[type="search"]').val()}},order:["1","DESC"],pageLength:10,searching:!0,aoColumns:[{data:"codigo"},{data:"descripcion"},{data:"precioUni",render:function(e,t,o){return`$${parseFloat(e).toFixed(2)}`}},{data:"id",width:"20%",render:function(e,t,o){return`
                                <button type="button" class="btn btn-primary btn-sm btnSeleccionarProd" data-id="${o.id}">Seleccionar</button>
                            `}}],buttons:[],language:{url:"https://cdn.datatables.net/plug-ins/2.1.5/i18n/es-ES.json"}}),v.on("click",".btnSeleccionarProd",function(){const e=$(this).data("id");$.ajax({url:`/business/obtener_producto/${e}`,success:function(t){$("#prodSeleccionado").removeClass("d-none"),i=t,$("#prodDesc").text(i.descripcion)}})}))}),$("#clienteExistenteModal").on("show.bs.modal",function(a){$.ajaxSetup({Headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")}}),f?f.ajax.reload():(f=$("#tablaClientes").DataTable({processing:!0,serverSide:!0,ajax:{url:"/business/obtener_clientes",type:"POST",data:function(e){e._token=$('meta[name="csrf-token"]').attr("content"),e.search=$('input[type="search"]').val()}},order:["1","DESC"],pageLength:10,searching:!0,aoColumns:[{data:"numDocumento",render:function(e,t,o){return o.tipoDocumento=="13"?o.numDocumento.slice(0,-1)+"-"+o.numDocumento.slice(-1):o.numDocumento}},{data:"nombre"},{data:"id",width:"20%",render:function(e,t,o){return`
                                <button type="button" class="btn btn-primary btn-sm btnSeleccionarCliente" data-id="${o.id}">Seleccionar</button>
                            `}}],buttons:[],language:{url:"https://cdn.datatables.net/plug-ins/2.1.5/i18n/es-ES.json"}}),f.on("click",".btnSeleccionarCliente",function(){const e=$(this).data("id");$.ajax({url:`/business/obtener_cliente/${e}`,success:function(t){$("#tipoDoc").val(t.tipoDocumento),t.tipoDocumento=="13"?$("#nitContribuyente").val(t.numDocumento.slice(0,-1)+"-"+t.numDocumento.slice(-1)):$("#nitContribuyente").val(t.numDocumento),$("#nombreContribuyente").val(t.nombre),$("#departamentoContribuyente").val(t.departamento),$("#departamentoContribuyente").trigger("change"),$("#municipioContribuyente").val(t.municipio),$("#complementoContribuyente").val(t.complemento),$("#correoContribuyente").val(t.correo),$("#telefonoContribuyente").val(t.telefono),$("#cerrarModalCliente").trigger("click")}})}))}),$("#aggitem").on("show.bs.modal",function(a){C()}),$("#aggitem .form-check-input").on("change",function(){S()}),$("#cantidadExistente, #descuentoExistente").on("change",function(){let a=$("#cantidadExistente").val()||0,e=$("#descuentoExistente").val()||0,t=x(a,i.precioUni,e);$("#totalExistente").val(parseFloat(t).toFixed(4))}),$("#btnAgregarProd").on("click",function(){let a=$("#cantidadExistente").val()||0,e=$("#descuentoExistente").val()||0;switch(i.id=r.length+1,i.cantidad=a,i.montoDescu=e,i.ventaGravada=0,i.ventaExenta=0,i.ventaNoSuj=0,i.tributos.forEach(t=>{t.codigo=="20"?t.calculado=i.precioUni/1.13*t.valor*i.cantidad:t.es_porcentaje?t.calculado=i.precioUni*t.valor*i.cantidad:t.calculado=t.valor*i.cantidad}),$("#tipoVentaExistente").val()){case"gravada":i.ventaGravada=$("#totalExistente").val(),i.ivaItem=($("#totalExistente").val()/1.13*.13).toFixed(4);break;case"exenta":i.ventaExenta=$("#totalExistente").val(),i.ivaItem=0;break;case"noSujeta":i.ventaNoSuj=$("#totalExistente").val(),i.ivaItem=0;break}r.push(i),p(),localStorage.setItem("items",JSON.stringify(r)),$("#cantidadExistente").val(""),$("#descuentoExistente").val(""),$("#totalExistente").val(""),l()}),$("#checkIvaRete1").on("change",function(){if($(this).prop("checked")){let a=0;r.forEach(e=>{a+=parseFloat(e.ventaGravada)}),d=a/1.13*.01,localStorage.setItem("reteIva1",d)}else d=0,localStorage.removeItem("reteIva1");l()}),$("#checkReteRenta").on("change",function(){if($(this).prop("checked")){let a=0;r.forEach(e=>{a+=parseFloat(e.ventaGravada)}),s=a/1.13*.1,localStorage.setItem("reteRenta",s)}else s=0,localStorage.removeItem("reteRenta");l()}),$("#cancelarDTE").on("click",function(){Swal.fire({title:"¿Cancelar generación de DTE?",text:"Se perderá toda la información ingresada",icon:"warning",showCancelButton:!0,confirmButtonColor:"#3085d6",cancelButtonColor:"#d33",confirmButtonText:"Sí, Cancelar",cancelButtonText:"No"}).then(a=>{a.isConfirmed&&(localStorage.removeItem("items"),localStorage.removeItem("reteIva1"),localStorage.removeItem("reteRenta"),window.location="/business/dashboard")})})});function x(a=0,e=0,t=0){return(a*e-t).toFixed(4)}function l(){E();let a=0,e=0,t=0;r.forEach(o=>{a+=parseFloat(o.ventaGravada)+parseFloat(o.ventaExenta)+parseFloat(o.ventaNoSuj)}),e=a-h+b,t=e-d-s,$("#reteIVA").text("$"+d.toFixed(2)),$("#reteRenta").text("$"+s.toFixed(2)),$("#subTotalGeneral").text("$"+a.toFixed(2)),$("#montoTotalOperacion").text("$"+e.toFixed(2)),$("#totalPagar").text("$"+t.toFixed(2)),$("#monto").val(t)}function y(){$("#loadingOverlay").removeClass("d-none");let a={};$("#checkContribuyente").prop("checked")?a={nombre:"Consumidor Final",telefono:null,correo:null,direccion:null,tipoDocumento:null,numDocumento:null}:a={nombre:$("#nombreContribuyente").val(),telefono:$("#telefonoContribuyente").val(),correo:$("#correoContribuyente").val(),direccion:{departamento:$("#departamentoContribuyente").val(),municipio:$("#municipioContribuyente").val(),complemento:$("#complementoContribuyente").val()},tipoDocumento:$("#tipoDoc").val(),numDocumento:$("#nitContribuyente").val()};let e={nit:$("#nit").val(),receptor:a,cuerpoDocumento:[],documentoRelacionado:null,ventaTercero:null,resumen:{descuNoSuj:0,descuExtenta:0,descuGravada:0,porcentajeDescuento:0,ivaRete1:d.toFixed(2),reteRenta:s.toFixed(2),saldoFavor:0,condicionOperacion:1},extension:null,apendice:null,pagos:null,numPagoElectronico:null};$("#nitVentaTerceros").val()!=""&&$("#nombreVentaTerceros").val()!=""&&(e.ventaTercero={nit:$("#nitVentaTerceros").val(),nombre:$("#nombreVentaTerceros").val()}),r.forEach(t=>{let o=[];t.tributos.forEach(c=>{c.codigo!=="20"&&o.push(c.codigo)}),o.length==0&&(o=null),e.cuerpoDocumento.push({tipoItem:t.tipoItem,numeroDocumento:null,cantidad:t.cantidad,codigo:t.codigo||null,codTributo:null,uniMedida:t.uniMedida,descripcion:t.descripcion,precioUni:t.precioUni,montoDescu:t.montoDescu,ventaNoSuj:t.ventaNoSuj,ventaExenta:t.ventaExenta,ventaGravada:t.ventaGravada,tributos:o,psv:t.precioUni,ivaItem:t.ivaItem,noGravado:0})}),e.resumen.tributos=u,$.ajax({url:"/business/factura?dte=factura",method:"POST",data:JSON.stringify(e),contentType:"application/json",success:function(t){t.status==201?t.message.estado=="PROCESADO"?Swal.fire({icon:"success",title:"Factura generada",text:"La factura ha sido generada exitosamente",showConfirmButton:!1,timer:2e3}).then(()=>{$("#loadingOverlay").addClass("d-none"),localStorage.removeItem("items"),localStorage.removeItem("reteIva1"),localStorage.removeItem("reteRenta"),window.location.href="/business/dtes"}):t.message.estado=="CONTINGENCIA"?Swal.fire({icon:"warning",title:"Factura generada en CONTINGENCIA",text:"Se generó la factura, pero no se envió a MH",showConfirmButton:!1,timer:2e3}).then(()=>{$("#loadingOverlay").addClass("d-none"),window.location.href="/business/dtes"}):Swal.fire({icon:"error",title:"Factura Rechazada",text:`Motivo: ${t.message.observaciones}`}).then(()=>{$("#loadingOverlay").addClass("d-none")}):Swal.fire({icon:"error",title:"La factura no se envió",text:"Ha ocurrido un error, verifica los datos e intenta de nuevo",showConfirmButton:!1,timer:2e3}).then(()=>{$("#loadingOverlay").addClass("d-none")})},error:function(t){console.error(t),Swal.fire({icon:"error",title:"Error",text:"Ha ocurrido un error al generar la factura",showConfirmButton:!1,timer:2e3})}})}function p(){let a=$("#items");a.empty(),r.forEach(e=>{a.append(`
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
        `)})}function S(){$(".form-check-input").each(function(){if($(this).prop("checked")){let e={codigo:$(this).val(),descripcion:$(this).next("label").text().trim(),valor:$(this).data("valor"),es_porcentaje:$(this).data("porcentaje"),calculado:0};n.tributos.some(t=>t.codigo===e.codigo)||n.tributos.push(e)}else n.tributos=n.tributos.filter(e=>e.codigo!==$(this).val())});let a="";n.tributos.forEach(e=>{let t=0;e.codigo=="20"?(t=n.precioUni/1.13*e.valor*n.cantidad,a+=`
                <div class="alert alert-info" role="alert">
                    ${e.descripcion}: $${t.toFixed(4)}
                </div>
            `,e.calculado=t):(e.es_porcentaje?t=n.precioUni*e.valor*n.cantidad:t=e.valor*n.cantidad,a+=`
                <div class="alert alert-info" role="alert">
                    ${e.descripcion}: $${t.toFixed(4)}
                </div>
            `,e.calculado=t)}),$("#tributosAplicados").html(a)}function g(){u=[],b=0,r.forEach(a=>{a.tributos.forEach(e=>{e.codigo!=="20"&&(u.some(t=>t.codigo===e.codigo)?u.find(t=>t.codigo===e.codigo).valor+=e.calculado:u.push({codigo:e.codigo,descripcion:e.descripcion,valor:e.calculado}),b+=e.calculado)})})}function E(){g();let a=$("#tributos");a.empty(),u.forEach(e=>{a.append(`
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
        `)})}function C(){n={id:1,tipoItem:null,cantidad:null,codigo:null,uniMedida:null,descripcion:null,precioUni:0,montoDescu:0,ventaNoSuj:0,ventaExenta:0,ventaGravada:0,tributos:[],psv:0,ivaItem:0}}
