var fechaInicio  ='';
var fechaFinal  ='';
var fechaActual = new Date();

$(document).ready(function(e){
	/* Se calcula el primer mes de Membresía */
	calcularPeriodo(30);

	$(document).on("click",".member-btn",function(e){
		e.preventDefault();
		var plan='';
		$("#plan-select div").each(function(e){
			plan = parseInt($(this).data('plan'));
			if (plan>0){
				$(this).removeClass('member-activ-btn')
				//$(this).children('label').css("color","#2E6583");
			}
		})
		$(this).addClass('member-activ-btn');
		//$(this).children('label').css("color","#776BB0");
		plan = parseInt($(this).data('plan'));

		switch (plan){
			case 1:
				$("#opcionSel01").html("01");
				$("#montoSel01").html("10$");
				$("input[name='plan']").val("estandar");
				$("input[name='costoplan']").val("10");
				break;
			case 2:
				$("#opcionSel01").html("02");
				$("#montoSel01").html("30$");
				$("input[name='plan']").val("especial");
				$("input[name='costoplan']").val("30");
				break;
			case 3:
				$("#opcionSel01").html("03");
				$("#montoSel01").html("60$");
				$("input[name='plan']").val("corporativo");
				$("input[name='costoplan']").val("60");
				break;
			case 4:
				$("#opcionSel01").html("04");
				$("#montoSel01").html("80$");
				$("input[name='plan']").val("empresarial");
				$("input[name='costoplan']").val("80");
				break;
		}
		totalPago();
	});
	$("#pdoCancelacion").change(function(e){
		var pdoType = $(this).val();
		switch (pdoType){
			case 'mensual':
				calcularPeriodo(30);
				$("#info_dias").html('30 días hábiles');
				break;
			case 'trimestral':
				calcularPeriodo(90);
				$("#info_dias").html('3 meses');
				break;
			case 'semestral':
				calcularPeriodo(180);
				$("#info_dias").html('6 meses');
				break;
			case 'anual':
				calcularPeriodo(360);
				$("#info_dias").html('12 meses');
				break;
		}
		totalPago();
	})
	$(document).on("click",".buttom-select-program",function(event){
		event.preventDefault();
		//swal("Seleccionado");
		var condi ='yes';
		var seleccionado=$(this).hasClass('select-program-off');
		var selMod = $(this).data('mod');

		if (seleccionado){
			$(this).removeClass('select-program-off');
			$(this).addClass('program-active');
			condi = 'yes';
		} else {
			if (selMod!='mod01'){
				$(this).removeClass('program-active');
				$(this).addClass('select-program-off');
				condi = 'not';
			}
		}

		switch(selMod){
			case 'mod01': //Contabilidad
				$("#mod_01").val(condi);
				break;
			case 'mod02': //Contabilidad
				$("#mod_02").val(condi);
				break;
			case 'mod03': //Contabilidad
				$("#mod_03").val(condi);
				break;
		}
		totalPago();
	});


}) /* Fin de Document Ready */



function totalPago(){
	var totalPago = 0.00;
	var factorMod = 1;
	var costoPlan = parseFloat($("input[name='costoplan']").val());
	var namePlan = $("input[name='plan']").val();
	switch (namePlan){
		case 'estandar':
			factorMod = 1;
			break;
		case 'especial':
			factorMod = 1.5;
			break;
		case 'corporativo':
			factorMod = 2;
			break;
		case 'empresarial':
			factorMod = 3;
			break;
	}

	var costoMod = 0.00;
	if ($("#mod_01").val()=='yes'){
		costoMod+=parseFloat($("#mod_01c").val());
	}
	if ($("#mod_02").val()=='yes'){
		costoMod+=parseFloat($("#mod_02c").val());
	}
	if ($("#mod_03").val()=='yes'){
		costoMod+=parseFloat($("#mod_03c").val());
	}

	$("input[name='costoModulo']").val((costoMod*factorMod));

	var pdoCancel = $("#pdoCancelacion").val();
	var multiplicador = 0;
	switch(pdoCancel){
		case 'mensual':
			multiplicador = 1
			break;
		case 'trimestral':
			multiplicador = 3
			break;
		case 'semestral':
			multiplicador = 6
			break;
		case 'anual':
			multiplicador = 12
			break;
	}
	var descuento = 1;
	if (multiplicador==6){
		descuento = 0.92;
	} else {
		if (multiplicador==12){
			descuento = 0.90;
		}
	}
	totalPago = descuento*(multiplicador*((costoMod*factorMod) + costoPlan));
	console.log("Descuento: " + descuento + " Multiplicador: " + multiplicador + " costoMod: " + costoMod + " costoPlan: " + costoPlan + " montoPagar:" + totalPago);
	$("input[name='total_pago']").val(Math.round10(totalPago,-2));
	$("#montoPagar").val(Math.round10(totalPago,-2));
}

function calcularPeriodo(dias_sumar){
	var fechaI  = addDaysToDate(1);
	var fechaF  = addDaysToDate(dias_sumar+1);

	fechaInicio = formatSpanishDate(fechaI);
	fechaFinal = formatSpanishDate(fechaF);

	$("#fechaInic").html(fechaInicio);
	$("#fechaFin").html(fechaFinal);

	$("input[name='fecha_inicio']").val(fechaInicio);
	$("input[name='fecha_final']").val(fechaFinal);
}

