$(document).ready(function(e){




	$(document).on("click","#esquemaCta",function(e){
		/* Parametro de esquema de cuentas */
		e.preventDefault();
		console.log('Esta por aquí!!');

		$("#pageWindowProgram").empty();

		$("#sm-menu-windows").fadeOut("slow",function(e){
			$("#sm-menu-windows").css("display","none");
		});

		var rutaProg01 = returnPath('parameter_esquemaCtas_new');

		$.ajax({
			'data':{'action':'nada'},
			'url':rutaProg01,
	    	'type':'POST',
    		'dataType':'HTML',
			'error':function(err,txt,thr){
				$('body').empty();
				$('body').append(err.responseText);
			},
			'success': function(result){
				$("#pageWindowProgram").append(result);

				$("#pageInicialSM").fadeOut("slow",function(e){
					$("#pageInicialSM").css("display","none");
					$("#pageWindowProgram").fadeIn("slow",function(e){
						$("#pageWindowProgram").css("display","inherit");
					});
				})
				window.scrollTo(0,0);
			}
		})

	});


	$(document).on("submit","#reg002-esquemacta",function(e){
    	e.preventDefault();

    	var mascara  = $("#accounting_esquemacta_mascara").val();
    	var ctactrl = $("#accounting_esquemacta_ctaCtrl").val();
    	var nivelcta = $("#accounting_esquemacta_nivelCta").val();
    	separador = $("#accounting_esquemacta_separador").val();

    	if (mascara.indexOf(ctactrl)>0){
    		$("#accounting_esquemacta_ctaCtrl").focus();
    		swal("Error esquema de Cuenta","Cuenta control no esta contenida en la mascara principal!!","error");
    		return false;
    	} 
    	if (separador=='-'){
	    	mascara = String(mascara.replace(/\-/g,''));
    	} else {
	    	mascara = String(mascara.replace(/\./g,''));
    	}
    	if (mascara.length!==nivelcta.length){
    		$("#accounting_esquemacta_nivelCta").focus();
    		swal("Error esquema de Cuenta","La cuenta nivel debe tener la misma cantidad de dígitos que la mascara!!","error");
    		return false;
    	} 

    	var datos = $(this).serialize();

    	console.log(datos);

    	var rootReg001 = returnPath('parameter_esquemaCtas_write');
    	//var rootApplication =returnPath('soft_mod_application');

    	$.ajax({
    		data:datos,
    		type:'POST',
    		dataType:'html',
           	url:rootReg001,
			error: function(err,txt,thr){
				$('body').empty();
				$('body').html(err.responseText);
			},
			success: function(result){
				if (result.indexOf("nro:1333600650Correct#1200")>0){
					/*Cierra la ventana de windows */
					$("#pageWindowProgram").fadeOut('slow',function(e){
						
						$("#pageWindowProgram").css("display","none");

						$("#pageInicialSM").fadeIn('slow',function(e){
							$("#pageInicialSM").css("display","inherit");
						});
					})
					//window.location=rootApplication;

				} else {
					$("#pageWindowProgram").empty();
					$("#pageWindowProgram").html(result);
				}


			}
    	});

	});

	
	$(document).on("click","#catalogoCta",function(e){
		/* Catalogo contable */
		e.preventDefault();

		$("#pageWindowProgram").empty();

		$("#sm-menu-windows").fadeOut("slow",function(e){
			$("#sm-menu-windows").css("display","none");
		});

		datasetMaestroCtasWin();

	});

	$(document).on("click","#paramGeneral",function(e){
		/* Parametro de esquema de cuentas */
		e.preventDefault();

		$("#pageWindowProgram").empty();

		$("#sm-menu-windows").fadeOut("slow",function(e){
			$("#sm-menu-windows").css("display","none");
		});

		var rutaProg01 = returnPath('parameter_parametros_new');

		$.ajax({
			'data':{'action':'nada'},
			'url':rutaProg01,
	    	'type':'POST',
    		'dataType':'HTML',
			'error':function(err,txt,thr){
				$('body').empty();
				$('body').append(err.responseText);
			},
			'success': function(result){
				$("#pageWindowProgram").append(result);

				$("#pageInicialSM").fadeOut("slow",function(e){
					$("#pageInicialSM").css("display","none");
					$("#pageWindowProgram").fadeIn("slow",function(e){
						$("#pageWindowProgram").css("display","inherit");
					});
				})
				window.scrollTo(0,0);
			}
		})

	});

	$(document).on("click","#tipoDocumento",function(e){
		/* Parametro de esquema de cuentas */
		e.preventDefault();

		$("#pageWindowProgram").empty();

		$("#sm-menu-windows").fadeOut("slow",function(e){
			$("#sm-menu-windows").css("display","none");
		});

		var rutaProg01 = returnPath('parameter_tipodoc_dataset');

		$.ajax({
			'data':{'action':'nada'},
			'url':rutaProg01,
	    	'type':'POST',
    		'dataType':'HTML',
			'error':function(err,txt,thr){
				$('body').empty();
				$('body').append(err.responseText);
			},
			'success': function(result){
				$("#pageWindowProgram").append(result);

				$("#pageInicialSM").fadeOut("slow",function(e){
					$("#pageInicialSM").css("display","none");
					$("#pageWindowProgram").fadeIn("slow",function(e){
						$("#pageWindowProgram").css("display","inherit");
					});
				})
				window.scrollTo(0,0);
			}
		})

	});
}); /*  Fin de Document Ready */

function datasetTipoDocumentWin(){

	var rutaProg01 = returnPath('parameter_tipodoc_dataset');

	$.ajax({
		'data':{'action':'nada'},
		'url':rutaProg01,
    	'type':'POST',
		'dataType':'HTML',
		'error':function(err,txt,thr){
			$('body').empty();
			$('body').append(err.responseText);
		},
		'success': function(result){
			$("#pageWindowProgram").html(result);

			$("#pageInicialSM").fadeOut("slow",function(e){
				$("#pageInicialSM").css("display","none");
				$("#pageWindowProgram").fadeIn("slow",function(e){
					$("#pageWindowProgram").css("display","inherit");
				});
			})
			window.scrollTo(0,0);
		}
	})
} 


function datasetMaestroCtasWin(){

	var rutaProg01 = returnPath('parameter_maestroCtas_dataset');

	$.ajax({
		'data':{'action':'nada'},
		'url':rutaProg01,
    	'type':'POST',
		'dataType':'HTML',
		'error':function(err,txt,thr){
			$('body').empty();
			$('body').append(err.responseText);
		},
		'success': function(result){
			$("#pageWindowProgram").html(result);

			$("#pageInicialSM").fadeOut("slow",function(e){
				$("#pageInicialSM").css("display","none");
				$("#pageWindowProgram").fadeIn("slow",function(e){
					$("#pageWindowProgram").css("display","inherit");
				});
			})
			window.scrollTo(0,0);
		}
	})
} 

var separador = '';
/* Funcion para controlar los datos Ingresado */
function keyEsquema(event,tipo){
  var valor = event.target.value + event.key;
  switch(tipo){
    case 'separador':
      var pasa = /^[.-]$/g;
      var exprEsq = pasa.test(valor);
    break;
    case 'mascara':
      separador = $("#accounting_esquemacta_separador").val();
      if (separador =='.'){
        var pasa = /^[.Xx]{1,60}$/g;
        var exprEsq = pasa.test(valor);
      } else {
        var pasa = /^[-Xx]{1,60}$/g;
        var exprEsq = pasa.test(valor);
      }
    break;
    case 'ctactrl':
      separador = $("#accounting_esquemacta_separador").val();
      if (separador =='.'){
        var pasa = /^[.Xx]{1,30}$/g;
        var exprEsq = pasa.test(valor);
      } else {
        var pasa = /^[-Xx]{1,30}$/g;
        var exprEsq = pasa.test(valor);
      }
    break;
    case 'nivelCta':
      var pasa = /^[01]{1,60}$/g;
      var exprEsq = pasa.test(valor);
    break;
  }
  if (exprEsq){
    event.returnValue = true;

    letra = String(event.key);
    letra = String(letra.toUpperCase());
    // get old value
    //console.log(event);
    var start = event.target.selectionStart;
    var end = event.target.selectionEnd;
    var oldValue = event.target.value;

    // replace point and change input value
    var newValue = oldValue.slice(0, start) + letra + oldValue.slice(end);
    event.target.value = newValue;

    // replace cursor
    event.target.selectionStart = event.target.selectionEnd = start + 1;
    event.preventDefault();
  } else {
    event.returnValue = false;
  }
  return;
}

