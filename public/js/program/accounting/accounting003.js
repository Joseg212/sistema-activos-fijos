$(document).ready(function(e){



	$(document).on("click","#consulComprob",function(e){
		/* Parametro de esquema de cuentas */
		e.preventDefault();

		$("#pageWindowProgram").empty();

		$("#sm-menu-windows").fadeOut("slow",function(e){
			$("#sm-menu-windows").css("display","none");
		});

		var rutaProg01 = returnPath('manager_consulta_comprob_lista');

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

	$(document).on("click","#controlMes",function(e){
		/* Parametro de esquema de cuentas */
		e.preventDefault();

		$("#pageWindowProgram").empty();

		$("#sm-menu-windows").fadeOut("slow",function(e){
			$("#sm-menu-windows").css("display","none");
		});

		var rutaProg01 = returnPath('proccess_controlmes_proc');

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

});