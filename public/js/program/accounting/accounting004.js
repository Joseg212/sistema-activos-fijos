$(document).ready(function(e){

	$(document).on("click","#cargaComprob",function(e){
		/* Parametro de esquema de cuentas */
		e.preventDefault();
		var rutaCargadeComprob = returnPath('manager_comprobante_load');
		getWindowSoftMod(rutaCargadeComprob);
	});
	$(document).on("click","#reportBC",function(e){
		/* Informe de balance de comprobanción */
	    e.preventDefault();
		var rutaBalanceComprob = returnPath('report_balance_comprob');
		getWindowSoftMod(rutaBalanceComprob);
	});
	$(document).on("click","#reportBG",function(e){
		/* Informe de balance de comprobanción */
	    e.preventDefault();
		var rutaBalanceGeneral = returnPath('report_balance_general');
		getWindowSoftMod(rutaBalanceGeneral);
	});
	$(document).on("click","#reportGYP",function(e){
		/* Informe de balance de comprobanción */
    	e.preventDefault();
		var rutaEstadoSituacion = returnPath('report_estado_situacion');
		getWindowSoftMod(rutaEstadoSituacion);
	});

}); // End of Document Ready