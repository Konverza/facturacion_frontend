let s=null,l=[],i=null,n=null,d=[],p=0,v=0;$(function(){$.ajax({url:"/departamentos/all",success:function(o){const t=$("#departamentoContribuyente");t.empty(),o.forEach(e=>{t.append(new Option(e.valores,e.codigo))}),t.on("change",function(){const e=$(this).val(),a=$("#municipioContribuyente");a.empty(),e&&o.find(r=>r.codigo===e).municipios.forEach(r=>{a.append(new Option(r.valores,r.codigo))})}),$("#departamentoContribuyente").trigger("change")}}),setInterval(()=>{const o=new Date,t=String(o.getHours()).padStart(2,"0"),e=String(o.getMinutes()).padStart(2,"0"),a=String(o.getSeconds()).padStart(2,"0"),c=`${t}:${e}:${a}`;$("#horaDTE").val(c)},1e3),$("#fechaDTE").val(new Date().toISOString().split("T")[0]),$("#descuento, #cantidad, #precio").on("change",function(){let o=$("#cantidad").val(),t=$("#precio").val(),e=$("#descuento").val(),a=f(o,t,e);$("#total").val(a),n.cantidad=o,n.precioUni=t,n.montoDescu=e;let c=$("#tipoVenta").val();c=="gravada"?n.ventaGravada=a:c=="exenta"?n.ventaExenta=a:c=="noSujeta"&&(n.ventaNoSuj=a),b()}),$("#checkContribuyente").on("change",function(){$(this).prop("checked")?($("#nombreContribuyente").prop("disabled",!0),$("#telefonoContribuyente").prop("disabled",!0),$("#correoContribuyente").prop("disabled",!0),$("#departamentoContribuyente").prop("disabled",!0),$("#municipioContribuyente").prop("disabled",!0),$("#complementoContribuyente").prop("disabled",!0),$("#tipoDoc").prop("disabled",!0),$("#nitContribuyente").prop("disabled",!0)):($("#nombreContribuyente").prop("disabled",!1),$("#telefonoContribuyente").prop("disabled",!1),$("#correoContribuyente").prop("disabled",!1),$("#departamentoContribuyente").prop("disabled",!1),$("#municipioContribuyente").prop("disabled",!1),$("#complementoContribuyente").prop("disabled",!1),$("#tipoDoc").prop("disabled",!1),$("#nitContribuyente").prop("disabled",!1))}),$("#agregar_item").on("click",function(){let o=$("#unidad").val(),t=$("#cantidad").val(),e=$("#descuento").val()||0,a=$("#producto").val();n.tipoItem=$("#tipoItem").val(),n.uniMedida=o,n.montoDescu=e,n.descripcion=a,n.cantidad=t,l.push(n),m(l),$("#cantidad").val(""),$("#precio").val(""),$("#descuento").val(""),$("#total").val(""),$("#producto").val(""),u()}),$("#items").on("click",".eliminar",function(){let o=$(this).data("id");l=l.filter(t=>t.id!==o),$(this).closest("tr").remove(),u()}),$("#guardarDescuento").on("click",function(){let o=$("#descVentasGravadas").val();v=parseFloat(o),u(),$("#descuentosTotal").text("$"+v.toFixed(2))}),$("#generarDocumento").on("click",function(){g()}),$("#prodExistenteModal").on("show.bs.modal",function(o){i=null,$("#prodSeleccionado").addClass("d-none"),$.ajaxSetup({Headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")}}),s?s.ajax.reload():(s=$("#tablaProds").DataTable({processing:!0,serverSide:!0,ajax:{url:"/business/obtener_productos",type:"POST",data:function(t){t._token=$('meta[name="csrf-token"]').attr("content"),t.search=$('input[type="search"]').val()}},order:["1","DESC"],pageLength:10,searching:!0,aoColumns:[{data:"codigo"},{data:"descripcion"},{data:"precioUni",render:function(t,e,a){return`$${parseFloat(t).toFixed(2)}`}},{data:"id",width:"20%",render:function(t,e,a){return`
                                <button type="button" class="btn btn-primary btn-sm btnSeleccionarProd" data-id="${a.id}">Seleccionar</button>
                            `}}],buttons:[],language:{url:"https://cdn.datatables.net/plug-ins/2.1.5/i18n/es-ES.json"}}),s.on("click",".btnSeleccionarProd",function(){const t=$(this).data("id");$.ajax({url:`/business/obtener_producto/${t}`,success:function(e){$("#prodSeleccionado").removeClass("d-none"),i=e,console.log(i),$("#prodDesc").text(i.descripcion)}})}))}),$("#aggitem").on("show.bs.modal",function(o){n={id:l.length+1,tipoItem:null,cantidad:null,codigo:null,uniMedida:null,descripcion:null,precioUni:0,montoDescu:0,ventaNoSuj:0,ventaExenta:0,ventaGravada:0,tributos:[],psv:0,ivaItem:0}}),$("#aggitem .form-check-input").on("change",function(){b()}),$("#cantidadExistente, #descuentoExistente").on("change",function(){let o=$(this).val()||0,t=i.precioUni,e=$("#descuentoExistente").val()||0,a=f(o,t,e);$("#totalExistente").val(parseFloat(a).toFixed(4))}),$("#btnAgregarProd").on("click",function(){let o=$("#cantidadExistente").val()||0,t=$("#descuentoExistente").val()||0;switch(i.cantidad=o,i.montoDescu=t,i.ventaGravada=0,i.ventaExenta=0,i.ventaNoSuj=0,i.tributos.forEach(e=>{e.codigo=="20"?e.calculado=i.precioUni/1.13*e.valor*i.cantidad:e.es_porcentaje?e.calculado=i.precioUni*e.valor*i.cantidad:e.calculado=e.valor*i.cantidad}),$("#tipoVentaExistente").val()){case"gravada":i.ventaGravada=$("#totalExistente").val();break;case"exenta":i.ventaExenta=$("#totalExistente").val();break;case"noSujeta":i.ventaNoSuj=$("#totalExistente").val();break}i.ivaItem=($("#totalExistente").val()/1.13*.13).toFixed(4),l.push(i),m(l),$("#cantidadExistente").val(""),$("#descuentoExistente").val(""),$("#totalExistente").val(""),u()})});function f(o=0,t=0,e=0){return(o*t-e).toFixed(4)}function u(){h();let o=0,t=0,e=0,a=0,c=0;l.forEach(r=>{e+=parseFloat(r.ventaGravada)+parseFloat(r.ventaExenta)+parseFloat(r.ventaNoSuj)}),a=e-v+p,c=a+o+t,$("#reteIVA").text("$"+o.toFixed(2)),$("#reteRenta").text("$"+t.toFixed(2)),$("#subTotalGeneral").text("$"+e.toFixed(2)),$("#montoTotalOperacion").text("$"+a.toFixed(2)),$("#totalPagar").text("$"+c.toFixed(2)),$("#monto").val(c)}function g(){$("#loadingOverlay").removeClass("d-none");let o={};$("#checkContribuyente").prop("checked")?o={nombre:"Consumidor Final",telefono:null,correo:null,direccion:null,tipoDocumento:null,numDocumento:null}:o={nombre:$("#nombreContribuyente").val(),telefono:$("#telefonoContribuyente").val(),correo:$("#correoContribuyente").val(),direccion:{departamento:$("#departamentoContribuyente").val(),municipio:$("#municipioContribuyente").val(),complemento:$("#complementoContribuyente").val()},tipoDocumento:$("#tipoDoc").val(),numDocumento:$("#nitContribuyente").val()};let t={nit:$("#nit").val(),receptor:o,cuerpoDocumento:[],documentoRelacionado:null,ventaTercero:null,resumen:{descuNoSuj:0,descuExtenta:0,descuGravada:0,porcentajeDescuento:0,ivaRete1:0,reteRenta:0,saldoFavor:0,condicionOperacion:1},extension:null,apendice:null,pagos:null,numPagoElectronico:null};l.forEach(e=>{let a=[];e.tributos.forEach(c=>{c.codigo!=="20"&&a.push(c.codigo)}),a.length==0&&(a=null),t.cuerpoDocumento.push({tipoItem:e.tipoItem,numeroDocumento:null,cantidad:e.cantidad,codigo:e.codigo||null,codTributo:null,uniMedida:e.uniMedida,descripcion:e.descripcion,precioUni:e.precioUni,montoDescu:e.montoDescu,ventaNoSuj:e.ventaNoSuj,ventaExenta:e.ventaExenta,ventaGravada:e.ventaGravada,tributos:a,psv:e.precioUni,ivaItem:e.ivaItem,noGravado:0})}),t.resumen.tributos=d,console.log(t),$.ajax({url:"/business/factura",method:"POST",data:JSON.stringify(t),contentType:"application/json",success:function(e){e.status==201&&Swal.fire({icon:"success",title:"Factura generada",text:"La factura ha sido generada exitosamente",showConfirmButton:!1,timer:2e3}).then(()=>{$("#loadingOverlay").addClass("d-none"),window.location.href="/business/dtes"})},error:function(e){console.error(e),Swal.fire({icon:"error",title:"Error",text:"Ha ocurrido un error al generar la factura",showConfirmButton:!1,timer:2e3})}})}function m(o){let t=$("#items");t.empty(),o.forEach(e=>{t.append(`
            <tr>
                <td>${e.uniMedida}</td>
                <td>${e.descripcion}</td>
                <td>${e.cantidad}</td>
                <td>$${parseFloat(e.precioUni).toFixed(2)}</td>
                <td>$${parseFloat(e.montoDescu).toFixed(2)}</td>
                <td>
                    $${parseFloat(e.ventaGravada).toFixed(4)} (Gravada) <br>
                    $${parseFloat(e.ventaExenta).toFixed(4)} (Exenta) <br>
                    $${parseFloat(e.ventaNoSuj).toFixed(4)} (No Sujeta)
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm eliminar" data-id="${e.id}">Eliminar</button>
                </td>
            </tr>
        `)}),console.log(o)}function b(){$(".form-check-input").each(function(){if($(this).prop("checked")){let t={codigo:$(this).val(),descripcion:$(this).next("label").text().trim(),valor:$(this).data("valor"),es_porcentaje:$(this).data("porcentaje"),calculado:0};n.tributos.some(e=>e.codigo===t.codigo)||n.tributos.push(t)}else n.tributos=n.tributos.filter(t=>t.codigo!==$(this).val())});let o="";n.tributos.forEach(t=>{let e=0;t.codigo=="20"?(e=n.precioUni/1.13*t.valor*n.cantidad,o+=`
                <div class="alert alert-info" role="alert">
                    ${t.descripcion}: $${e.toFixed(4)}
                </div>
            `,t.calculado=e):(t.es_porcentaje?e=n.precioUni*t.valor*n.cantidad:e=t.valor*n.cantidad,o+=`
                <div class="alert alert-info" role="alert">
                    ${t.descripcion}: $${e.toFixed(4)}
                </div>
            `,t.calculado=e)}),$("#tributosAplicados").html(o)}function x(){p=0,l.forEach(o=>{o.tributos.forEach(t=>{t.codigo!=="20"&&(d.some(e=>e.codigo===t.codigo)?d.find(e=>e.codigo===t.codigo).valor+=t.calculado:d.push({codigo:t.codigo,descripcion:t.descripcion,valor:t.calculado}),p+=t.calculado)})})}function h(){x();let o=$("#tributos");o.empty(),d.forEach(t=>{o.append(`
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-end fw-bold">${t.descripcion}</td>
                <td>$${t.valor.toFixed(2)}</td>
            </tr>
        `)})}
