$(document).ready(function(event){

	$("#btn_ingresarEmpresa").click(function(event){
		/* Ingresa nueva empresa  */
		event.preventDefault();

		$("#pageWindowProgram").empty();

		var rutaProg01 = returnPath('company_proccess_new');

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
	$(document).on("click",".sm-btn-close-window",function(event){
		/*Cierra la ventana de windows */
		$("#pageWindowProgram").fadeOut('slow',function(e){
			
			$("#pageWindowProgram").css("display","none");

			$("#pageInicialSM").fadeIn('slow',function(e){
				$("#pageInicialSM").css("display","inherit");
			});
		})
	})
	$(document).on("click","#btnExitWindow",function(e){
		/*Cierra la ventana de windows */
		$("#pageWindowProgram").fadeOut('slow',function(e){
			
			$("#pageWindowProgram").css("display","none");

			$("#pageInicialSM").fadeIn('slow',function(e){
				$("#pageInicialSM").css("display","inherit");
			});
		})
	});

    $(document).on("submit","#reg001-empresa",function(e){
    	e.preventDefault();
    	var datos = $(this).serialize();

    	console.log(datos);

    	var rootReg001 = returnPath('company_proccess_write');
    	var rootApplication =returnPath('soft_mod_application');

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
					window.location=rootApplication;

				} else {
					$("#pageWindowProgram").empty();
					$("#pageWindowProgram").html(result);
				}


			}
    	});

    });


}) // Fin de Document ready

var arrDatosCta = {};

function verificarCuenta(codeAccount){
	var rootSeek = returnPath('seekAccount_001');
	var valor = false;
	$.ajax({
		data:{'action':'sinwin','codeAccount':codeAccount},
		type:'post',
		dataType:'json',
		url:rootSeek,
		async:false,
		error: function(err,txt,thr){
			$('body').empty();
			$('body').html(err.responseText);
		},
		success: function(result){
			if (result.ok=='01'){
				valor = true;
				arrDatosCta = result.datosCta;
			} else {
				swal("Parametros Generales",result.msg,"error");
			}
		}
	});
	return valor;
}