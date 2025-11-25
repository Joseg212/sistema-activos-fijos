var dataTableCPEstandar = null;
var dataTableSearchComprob = null;
var dataTableSearchCuentaC = null;

var rootDataTableCPEstandar = '';
var idDetCP ='';
var totalDebe = 0.00;
var totalHaber = 0.00;
$(document).ready(function(e){
	$("#viewState").css("visibility","hidden");
	$("#viewState").css("height","3px");

    $('#cabezera_comprob_fecha').daterangepicker({
      singleDatePicker: true,
      locale: {
      	format:'DD/MM/YYYY',
      	monthNames: arrMeses,
        daysOfWeek: arrInicialDia,
      }
    });

    $('#fechaDoc').daterangepicker({
      singleDatePicker: true,
      locale: {
      	format:'DD/MM/YYYY',
      	monthNames: arrMeses,
        daysOfWeek: arrInicialDia,
      }
    });

    $('#vencDoc').daterangepicker({
      singleDatePicker: true,
      locale: {
      	format:'DD/MM/YYYY',
      	monthNames: arrMeses,
        daysOfWeek: arrInicialDia,
      }
    });

    $('#emisionDoc').daterangepicker({
      singleDatePicker: true,
      locale: {
      	format:'DD/MM/YYYY',
      	monthNames: arrMeses,
        daysOfWeek: arrInicialDia,
      }
    });

	rootDataTableCPEstandar = returnPath('manager_comprobante_datatable01');
	rootWriteDetalle = returnPath('manager_comprobante_writedet');

	$("#prog0320-p05-crearcp").submit(function(e){
		e.preventDefault();
		var fecha = $("#cabezera_comprob_fecha").val();
		var concepto = String($("#cabezera_comprob_concepto").val());
		var tipo = $("#cabezera_comprob_tipo").val();

		if (validaFecha(fecha)){
			swal("Carga de comprobante","La fecha introduccida no es valida!!!","error");
			$("#cabezera_comprob_fecha").focus();			
			return false;
		} else {
			if (verificarMes(fecha)){
				$("#cabezera_comprob_fecha").focus();			
				return false ;
			}
		}
		if (concepto.length<0){
			swal("Carga de Comprobante","Debe indicar el concepto del comprobante!!!","error");
			$("#cabezera_comprob_concepto").focus();
			return false;
		}

		var rootNewComprob = returnPath("manager_comprobante_newreg");

		var dataComprob = $(this).serialize();


		$.ajax({
			data:dataComprob,
			url:rootNewComprob,
			type:'post',
			dataType:'json',
			error:function(err,txt,thr){
				$('body').empty();
				$('body').html(err.responseText);
			},
			success:function(result){
				if (result.ok=='01'){
					$("#viewIdComprob").html('<b>ID:</b> '+result.nroComprob);
					$("#cabezera_comprob_estado").val('Descuadrado');
					$("#cabezera_comprob_fecha").attr('disabled',true);
					$("#cabezera_comprob_concepto").attr('disabled',true);

					$("#viewButton").css("display","none");
					$("#viewState").css("visibility","visible");
					$("#viewState").css("height","auto");

					$("#cabezera_comprob_debitos").val('0,00');
					$("#cabezera_comprob_creditos").val('0,00');
					$("#cabezera_comprob_diferencia").val('0,00');

					$("#cabezera_comprob_debitos").css('text-align','right');
					$("#cabezera_comprob_creditos").css('text-align','right');
					$("#cabezera_comprob_diferencia").css('text-align','right');

					$("actionDet").val('new');
					$("#idComprob").val(result.nroComprob);
					dataTableCPEstandarLoad();
					$("#comprob-detalle").fadeIn('slow',function(){
						$("#comprob-detalle").css("display","inherit");
					});
					$("#codigo").focus();
					loadTypeDoc();
				} else {
					swal("Carga de Comprobante",result.msg,"error");
				}
			}
		});
		return false;
	});

	/* Guardar información del comprobante */
	$("#btnGuardarDetalle").click(function(e){
		e.preventDefault();
		var idComprob = $("#idComprob").val();
		var codigo = $("#codigo").val();
		var concepto = $("#concepto").val();
		var monto =$("#monto").val();
		var tipomov = $("input[name='tipoMov']:checked").val();
		var sidoc = $("#sidoc").val();
		var action =$("#actionDet").val();
		var idDocDet = $("#idDocDet").val();
		var idDetComprob = $("#idDetComprob").val();

		var numeroDoc = String($("#numeroDoc").val());
		var fechaDoc = String($("#fechaDoc").val());
		var emisionDoc = String($("#emisionDoc").val());
		var vencDoc = String($("#vencDoc").val());
		var idDoc = String($("#idDoc").val());
		var descripDoc = String($("#descripDoc").val());
		var tipoDoc = $("#tipoDoc").val();

		if (sidoc=='yes'){
			var condiTrue=false;
			$.each(arrTypeDoc,function(valor,item){
				if (item.id==idDoc){
					if (item.condiNum==true){
						if (numeroDoc<1){
							swal("Carga de Comprobante","Ingrese el numero de documento!!","error");
							$("#numeroDoc").focus();
							condiTrue=true;
							return false;
						}
					}
					if (item.condiFechaReg==true){
						if (fechaDoc<1){
							swal("Carga de Comprobante","Indique la fecha del documento!!","error");
							$("#fechaDoc").focus();
							condiTrue=true;
							return false;
						}
					}
					if (item.condiFechaEmision==true){
						if (emisionDoc<1){
							swal("Carga de Comprobante","Indique la fecha de emisión!!","error");
							$("#emisionDoc").focus();
							condiTrue=true;
							return false;
						}
					}
					if (item.condiFechaVenc==true){
						if (vencDoc<1){
							swal("Carga de Comprobante","Indique la fecha de vencimiento!!","error");
							$("#vencDoc").focus();
							condiTrue=true;
							return false;
						}
					}
					if (item.condiId==true){
						if (idDoc<1){
							swal("Carga de Comprobante","Escriba la identificación!!","error");
							$("#idDoc").focus();
							condiTrue=true;
							return false;
						}
					}
					if (item.condiDescrip==true){
						if (descripDoc<1){
							swal("Carga de Comprobante","Escriba una descripción del documento!!","error");
							$("#descripDoc").focus();
							condiTrue=true;
							return false;
						}
					}
				} // fin condicional
			});
			if (condiTrue){
				return;
			}
		} 

		if (concepto.length<1){
			swal("Carga de Comprobante","Debe ecribir el concepto del movimiento.","Error");
			$("#concepto").focus();
			return;
		}
		monto = monto.replace(/\./g,'');
		monto = monto.replace(/\,/g,'.');
		console.log("El monto:" + monto);
		valmonto = parseFloat(monto);

		if (valmonto<0){
			swal("Monto inválido o vacío, ingrese un valor correcto.","Error");
			$("#concepto").focus();
			return;
		}
		//console.log('tipomov:'+tipomov);

		/* Proceso de Grabación de Comprobante */
		$.ajax({
			data:{'action':action,'idComprob':idComprob,'codigo':codigo,
					'concepto':concepto,'sidoc':sidoc,
					'tipomov':tipomov,'monto':valmonto,
					'idDetCP':idDetCP,'numeroDoc':numeroDoc,'fechaDoc':fechaDoc,
					'vencDoc':vencDoc,'emisionDoc':emisionDoc,'idDoc':idDoc,
					'descripDoc':descripDoc,'tipoDoc':tipoDoc,'idDocDet':idDocDet,
					'idDetComprob':idDetComprob},
			url:rootWriteDetalle,
			type:'post',
			dataType:'json',
			error: function(err,txt,thr){
				$('body').empty();
				$('body').html(err.responseText);
			},
			success: function(result){
				if(result.ok=='01'){
					$("#new-datos").fadeOut("slow",function(e){
						$("#new-datos").css("display","none");
					});
					$("#cabezera_comprob_debitos").val(result.actcp.debitos);
					$("#cabezera_comprob_creditos").val(result.actcp.creditos);
					$("#cabezera_comprob_diferencia").val(result.actcp.diferencia);
					$("#cabezera_comprob_estado").val(result.actcp.estado);
					resetCampos(true);
					dataTableCPEstandarLoad();	

				} else {
					swal("Carga de Comprobante",result.msg,"error");
				}
			}

		});

	});

	$("#codigo").change(function(e){
		var codigo = $(this).val();
		console.log('Verificarcion');
		// Verificar si la cuenta existe 
		if (verificarCuenta(codigo)){
			// Existe la Cuenta  
			$("#ctaControl").html(arrDatosCta.ctaControl);
			$("#ctaGrupo").html(arrDatosCta.ctaGrupo);
			$("#ctaTipoM").html(arrDatosCta.ctaTipoM);
			$("#nombreCta").html(arrDatosCta.ctaNombre);
		} else {
			// Activa el Buscador de Cuentas Solo Imputables 
			$("#ctaControl").html('');
			$("#ctaGrupo").html('');
			$("#ctaTipoM").html('');
			$("#nombreCta").html('');
			$("#codigo").val('');
		}
	});
	$("#btnAgregarDetalle").click(function(e){
		e.preventDefault();
		var codigo = String($("#codigo").val());
		if (codigo.length<1){
			swal("Carga de Comprobante","Ingrese el código de cuenta...","error");
			return;
		}

		resetCampos(false)
		$(this).css("visibility","hidden");
		$("#codigo").attr("disabled",true);

		$("#new-datos").fadeIn("slow",function(e){
			$("#new-datos").css("display","inherit");
		});
	});
	/* Guardar información del comprobante */
	$("#btnCancelarDetalle").click(function(e){
		e.preventDefault();
		$("#new-datos").fadeOut("slow",function(e){
			$("#new-datos").css("display","none");
		});
		resetCampos(true);
		
	});

	$("#btnRegDocument").click(function(e){
		e.preventDefault();
		var sta = $(this).data('sta');
		if (sta=="R"){
			$(this).html('Remover el Documento');
			$(this).data("sta","Q");

			$("#datosDocumento").fadeIn(function(e){
				$("#datosDocumento").css("display","inherit");
			});
			$("#sidoc").val('yes');
			$("#tipoDoc").change();
		} else {
			$(this).html('Registrar un Documento');
			$(this).data("sta","R");
			$("#sidoc").val('not');
			$("#datosDocumento").fadeOut(function(e){
				$("#datosDocumento").css("display","none");
			});
		}
	});
	/* Cambiar el tipo de documento */
	$("#tipoDoc").change(function(e){
		var idDoc = $(this).val();

		$.each(arrTypeDoc,function(valor,items){
			if (items.id==idDoc){
				$("#fechaDoc").val('');	
				$("#numeroDoc").val('');	
				$("#descripDoc").val('');	
				$("#vencDoc").val('');	
				$("#emisionDoc").val('');	
				$("#idDoc").val('');	
				typeDocActive(items);				
			}
		})
	});

	$("#sm-editComprob").on('click',function(e){
		dataTableSearchComprobLoad();
		return true;
	});

	$("#btnSearchComprob").on('click',function(e){
		console.log('Filtrando datos ....');
		e.preventDefault();
		dataTableSearchComprobLoad();
	});

	$("#buscarCuentaContable").on('click',function(e){
		dataTableSearchCuentaContabLoad();
		return true;
	});

	$("#btnSearchCuentaContab").on('click',function(e){
		e.preventDefault();
		dataTableSearchCuentaContabLoad();
	});

}); // Fin de Document Ready

var rutaRemoveDetComprob = returnPath('manager_comprobante_remove_detalle_comprob');

function deleteDetCP(idDetComprob){

	swal({
	  title: '¿Esta seguro de eliminar este movimiento?',
	  text: "Remover movimiento.",
	  type: 'warning',
	  showCancelButton: true,
	  confirmButtonColor: '#d33',
	  cancelButtonColor: '#3085d6',
	  confirmButtonText: 'Aceptar',
	  cancelButtonText: 'Cancelar'
	}).then((result) => {
	  if (result.value) {
			$.ajax({
				data:{'idDetComprob':idDetComprob},
				type:'post',
				dataType:'json',
				url:rutaRemoveDetComprob,
				error:function(err,txt,thr){
					$('body').empty();
					$('body').html(err.responseText);
				},
				success:function(result){
					if (result.ok=='01'){
						$("#cabezera_comprob_debitos").val(result.actcp.debitos);
						$("#cabezera_comprob_creditos").val(result.actcp.creditos);
						$("#cabezera_comprob_diferencia").val(result.actcp.diferencia);
						$("#cabezera_comprob_estado").val(result.actcp.estado);

						dataTableCPEstandarLoad();	
					} else {
						swal("Carga de Comprobante",result.msg,"error");
					}
				}
			});	
		}
	});
	return;
}

/* Permite editar el detalle de un comprobante contable */
var rutaEditDetComprob = returnPath('manager_comprobante_edit_detalle_comprob');

function editDetalleComprob(e,object){
	e.preventDefault();

	var itemsCod = $(object).data('id');

	$.ajax({
		data:{'itemsCod':itemsCod},
		url: rutaEditDetComprob,
		type:'post',
		dataType:'json',
		error: function(err,txt,thr){
			$('body').empty();
			$('body').html(err.responseText);
		},
		success: function(result){
			if (result.ok=='01'){
				resetCampos(false)
				$("#btnAgregarDetalle").css("visibility","hidden");
				$("#codigo").val(result.detalle.codigo);
				$("#codigo").change();
				$("#codigo").attr("disabled",true);

				$("#idDetComprob").val(result.detalle.id);
				$("#actionDet").val('edit');

				$("#new-datos").fadeIn("slow",function(e){
					$("#new-datos").css("display","inherit");
				});
				$("#concepto").val(result.detalle.concepto);

				var debe = parseFloat(result.detalle.debe);
				var haber = parseFloat(result.detalle.haber);

				if (debe>0){
					$("#tipom1").prop("checked", true);
				} else {
					$("#tipom2").prop("checked", true);
				}
				$("#monto").val(result.detalle.monto);

				if (result.documento == 'Yes'){
					$("#tipoDoc").val(result.infoDoc.idTipodoc);
					$("#tipoDoc").change();

					$("#idDocDet").val(result.infoDoc.idRegDoc);
					
					$("#numeroDoc").val(result.infoDoc.numero);
					$("#fechaDoc").val(result.infoDoc.fechaReg);
					$("#emisionDoc").val(result.infoDoc.fechaEmision);
					$("#vencDoc").val(result.infoDoc.fechaVenc);
					$("#descripDoc").val(result.infoDoc.descrip);
					$("#idDoc").val(result.infoDoc.serie);


					$("#btnRegDocument").html('Remover el Documento');
					$("#btnRegDocument").data("sta","Q");

					$("#datosDocumento").fadeIn(function(e){
						$("#datosDocumento").css("display","inherit");
					});
					$("#sidoc").val('yes');
				}


			} else {
				swal("Carga de Comprobante",result.msg,"error");
			}
		}
	});
}

/* Permite Efectuar la busqueda del código contable */
function buscarCuentaContable(codigo){

	$("#codigo").val(codigo);
	$("#codigo").change();
	$("#btnRetornoCuentaCSearch").click();
}

/* Carga el Comprobante para edición  */
function selectComprobEdit(object){
	var idComprob = $(object).data('num');

	var fecha =  $(object).parent().data('fecha');
	var concepto =  $(object).parent().data('concepto');
	var debitos =  $(object).parent().data('debitos');
	var creditos =  $(object).parent().data('creditos');
	var diferencia =  $(object).parent().data('difer');
	var tipo =  $(object).parent().data('tipo');
	var estado =  $(object).parent().data('estado');

	$("#cabezera_comprob_fecha").val(fecha);	
	$("#cabezera_comprob_concepto").val(concepto);	
	$("#cabezera_comprob_debitos").val(debitos);	
	$("#cabezera_comprob_creditos").val(creditos);	
	$("#cabezera_comprob_diferencia").val(diferencia);	
	$("#cabezera_comprob_tipo").val(tipo);	
	$("#cabezera_comprob_estado").val(estado);

	$("#viewIdComprob").html('<b>ID:</b> '+idComprob);
	$("#cabezera_comprob_fecha").attr('disabled',true);
	$("#cabezera_comprob_concepto").attr('disabled',true);

	$("#viewButton").css("display","none");
	$("#viewState").css("visibility","visible");
	$("#viewState").css("height","auto");

	$("#cabezera_comprob_debitos").css('text-align','right');
	$("#cabezera_comprob_creditos").css('text-align','right');
	$("#cabezera_comprob_diferencia").css('text-align','right');

	$("actionDet").val('new');

	$("#idComprob").val(idComprob);

	dataTableCPEstandarLoad();

	$("#comprob-detalle").fadeIn('slow',function(){
		$("#comprob-detalle").css("display","inherit");
	});

	$("#codigo").focus();
	loadTypeDoc();
	$("#btnRetornoComprobSearch").click();
	dataTableCPEstandarLoad();
}

var rutaSearchCuentaContab = returnPath('manager_comprobante_search_cuenta_contab');
/* Carga la Busque de Comprobante  */
function dataTableSearchCuentaContabLoad(){
	var txtSearch = $("#txtSearchCuentaContab").val();

	dataTableSearchCuentaC = $('#dataTableSearchCuentaC').dataTable({
			destroy:true,
			serverSide: true,
			searching: false,
			lengthChange: false,
			pageLength:4,
			language:{
				info:"Mostrando  _START_ de _END_ de un total _TOTAL_ ",
				infoEmpty:"Mostrando  1 de 0 ", 
				paginate:{
					first:"Primero",
					previous:"Previo",
					next:"Próximo",
					last:"Último",
				}
			},
			ajax:{
				data:{'txtSearch':txtSearch},
				method:"POST",
				dataType:"JSON",
				url:rutaSearchCuentaContab,
				error:function(err,txt,thr){
					$('body').empty();
					$('body').html(err.responseText);
				}
			},
			columns:[
				{data:"codigo"},
				{data:"cuenta"},
				{data:"monetaria"},
				{data:"nivel"},
				{data:"opcion"}
			],
			deferRender: true
	 });	

}

var rutaSearchComprob = returnPath('manager_comprobante_search_comprob');
/* Carga la Busque de Comprobante  */
function dataTableSearchComprobLoad(){
	var txtSearch = $("#txtSearchDataComprob").val();

	dataTableSearchComprob = $('#dataTableSearchComprob').dataTable({
			destroy:true,
			serverSide: true,
			searching: false,
			lengthChange: false,
			pageLength:6,
			language:{
				info:"Mostrando  _START_ de _END_ de un total _TOTAL_ ",
				infoEmpty:"Mostrando  1 de 0 ", 
				paginate:{
					first:"Primero",
					previous:"Previo",
					next:"Próximo",
					last:"Último",
				}
			},
			ajax:{
				data:{'txtSearch':txtSearch},
				method:"POST",
				dataType:"JSON",
				url:rutaSearchComprob,
				error:function(err,txt,thr){
					$('body').empty();
					$('body').html(err.responseText);
				}
			},
			columns:[
				{data:"numero"},
				{data:"fecha"},
				{data:"concepto"},
				{data:"debitos"},
				{data:"creditos"},
				{data:"estado"},
				{data:"opcion"}
			],
			deferRender: true
	 });	

}

function resetCampos(reset_cod){
	if (reset_cod){
		$("#codigo").val('');
		$("#codigo").removeAttr('disabled');	
		$("#codigo").focus();
		$("#btnAgregarDetalle").css("visibility","visible");
		$("#nombreCta").html('');
		$("#ctaControl").html('');
		$("#ctaGrupo").html('');
		$("#ctaTipoM").html('');
	}
	$("#idDocDet").val("");
	$("#idDetComprob").val("");

	$("#btnRegDocument").html('Registrar un Documento');
	$("#btnRegDocument").data("sta","R");
	$("#sidoc").val('not');

	$("#concepto").val('');
	$("#monto").val('');
	$("#tipom1").prop("checked",true);
	//$("#btnRegDocument").html('<b>R</b>egistrar');
	$("#datosDocumento").css("display","none");

	$("#fechaDoc").parent().parent().css("display","none");	
	$("#numeroDoc").parent().parent().css("display","none");	
	$("#descripDoc").parent().parent().css("display","none");	
	$("#vencDoc").parent().parent().css("display","none");	
	$("#emisionDoc").parent().parent().css("display","none");	
	$("#idDoc").parent().parent().css("display","none");	

	$("#fechaDoc").val('');	
	$("#numeroDoc").val('');	
	$("#descripDoc").val('');	
	$("#vencDoc").val('');	
	$("#emisionDoc").val('');	
	$("#idDoc").val('');	
	$("#sidoc").val('not');
}

function verificarMes(d_fecha){

	var valor=true;

	var rootVerifMes = returnPath('manager_comprobante_monthseek');

	$.ajax({
		data:{'fecha':d_fecha},
		type:'post',
		dataType:'json',
		url:rootVerifMes,
		async:false,
		error: function(err,txt,thr){
			$('body').empty();
			$('body').html(err.responseText);
		},
		success: function(result){
			if (result.ok=='01'){
				valor = false;
			} else {
				swal("Carga de Comprobante",result.msg,"error");
				valor = true;
			}
		}
	});

	return valor;
}

function dataTableCPEstandarLoad(){

	var idComprob = $("#idComprob").val();
	//console.log("Compronate:" + idComprob);
	dataTableCPEstandar = $('#tblCPEstandar').dataTable({
			destroy:true,
			serverSide: true,
			searching: false,
			lengthChange: false,
			bProcessing: false,
			bAutoWidth: false,
			pagingType: "simple_numbers",
			info:false,
			dom:'rtp',
			pageLength:20,
			language:{
				info:"Mostrando  _START_ de _END_ de un total _TOTAL_ ",
				infoEmpty:"Mostrando  1 de 0 ", 
				paginate:{
					first:"<<",
					previous:"<",
					next:">",
					last:">>"
				}
			},
			ajax:{
				data:{'idComprob':idComprob,'txtSearch':'','typeSearch':''},
				method:"POST",
				dataType:"JSON",
				url:rootDataTableCPEstandar,
				error:function(err,txt,thr){
					$('body').html(err.responseText);
				}
			},
			columnDefs:[
				{targets:0,className:'tb-border-d'},
				{targets:1,className:'tb-border-d'},
				{targets:2,className:'tb-border-d'},
				{targets:3,className:'tb-border-d'},
				{targets:4,className:'tb-border-d sm-number-right'},
				{targets:5,className:'tb-border-d sm-number-right'},
				{targets:6,className:'tb-border-d'}
			],
			columns:[
				{data:"codigo"},
				{data:"nombre"},
				{data:"concepto"},
				{data:"document"},
				{data:"debe"},
				{data:"haber"},
				{data:"opcion"}
			],
			deferRender: true
	 });		

}
/* Funcion para registrar el tipo documento */
var arrTypeDoc={};

function loadTypeDoc(){
	var rootLoadTypeDoc = returnPath('manager_comprobante_loadtypedoc');

	$.ajax({
		data:{'action':'load'},
		url:rootLoadTypeDoc,
		type:'post',
		dataType:'json',
		error: function(err,txt,thr){

		},
		success: function(result){
			if (result.ok=='01'){
				arrTypeDoc = result.typedoc;
				var html='';
				var contar = 0;
				/* Cargar los item de tipoDoc */
				$.each(arrTypeDoc,function(valor,items){
					html+='<option value="'+items.id+'">'+items.descrip+'</option>';
					contar++;
					if (contar==1){
						typeDocActive(items);
					}

				});	

				$("#tipoDoc").empty();
				$("#tipoDoc").append(html);

			} else {
				swal("Carga de Comprobante",result.msg,"error");
			}
		}
	})
}
function typeDocActive(items_o){
	if (items_o.condiNum==true){
		$("#numeroDoc").parent().parent().css("display","inherit");
	} else {
		$("#numeroDoc").parent().parent().css("display","none");
	}
	if (items_o.condiFechaReg==true){
		$("#fechaDoc").parent().parent().css("display","inherit");	
	} else {
		$("#fechaDoc").parent().parent().css("display","none");	
	}
	if (items_o.condiDescrip==true){
		$("#descripDoc").parent().parent().css("display","inherit");	
	} else {
		$("#descripDoc").parent().parent().css("display","none");	
	}
	if (items_o.condiFechaVenc==true){
		$("#vencDoc").parent().parent().css("display","inherit");	
	} else {
		$("#vencDoc").parent().parent().css("display","none");	
	}
	if (items_o.condiFechaEmision==true){
		$("#emisionDoc").parent().parent().css("display","inherit");	
	} else {
		$("#emisionDoc").parent().parent().css("display","none");	
	}
	if (items_o.condiId==true){
		$("#idDoc").parent().parent().css("display","inherit");	
	} else {
		$("#idDoc").parent().parent().css("display","none");	
	}
}