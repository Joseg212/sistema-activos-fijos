var arrMeses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

var arrInicialDia = ['Dom', 'Lun', 'Mar', 'Mier', 'Jue', 'Vie','Sab'];

$(document).ready(function(e){

	$("#ingreso_session").click(function(e){
		e.preventDefault();
		$("#modalSectionSoftMod").width($('body').width());
		$("#modalSectionSoftMod").height($('body').height()+150);

		$("#modalSectionSoftMod").fadeIn("slow",function(e){
			$("#modalSectionSoftMod").css("display","block");
		})
	});
	$('body').keydown(function(event){
		//console.log('codigo: '+event.keyCode);
		if (event.keyCode==27){
			$("#modalSectionSoftMod").fadeOut("slow",function(e){
				$("#modalSectionSoftMod").css("display","none");
			})
		}
	});

}) /* Fin del Document Ready*/

/* Devuelve las opciones del Menú a partie de los parametros */
function returnMenuOpcion(modulo){
  var rootModulosMenu = returnPath('soft_mod_module_main');

  $.ajax({
      data:{'modulo':modulo},
      type:'POST',
      dataType:'html',
      url:rootModulosMenu,
      error: function(err,txt,thr){
        $('body').empty();
        $('body').append(err.responseText);
      },
      success: function(result){
          $("#opcModulo").empty();
          $("#opcModulo").append(result);
      }
  });
}

/* Devuelve la ruta de Symfony  */

function returnPath(path){
	var valor = '';
	 $.ajax({
	 	data:{'path':path},
	 	type:'POST',
	 	dataType:'json',
	 	url:ajax_returnPathSys,
	 	async:false,
	 	error:function(err,txt,thr){
	 		$("body").empty();
	 		$("body").html(err.responseText);
	 	},
	 	success: function(result){
	 		//console.log('ruta:'+ result.path);
	 		if(result.ok=='01'){
	 			valor = result.path;
	 		} else {
	 			valor = 'nulo' + result.msg;
	 		}
	 	}
	 });
	 return valor;
} 


(function() {
  /**
   * Ajuste decimal de un número.
   *
   * @param {String}  tipo  El tipo de ajuste.
   * @param {Number}  valor El numero.
   * @param {Integer} exp   El exponente (el logaritmo 10 del ajuste base).
   * @returns {Number} El valor ajustado.
   */
  function decimalAdjust(type, value, exp) {
    // Si el exp no está definido o es cero...
    if (typeof exp === 'undefined' || +exp === 0) {
      return Math[type](value);
    }
    value = +value;
    exp = +exp;
    // Si el valor no es un número o el exp no es un entero...
    if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0)) {
      return NaN;
    }
    // Shift
    value = value.toString().split('e');
    value = Math[type](+(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp)));
    // Shift back
    value = value.toString().split('e');
    return +(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp));
  }

  // Decimal round
  if (!Math.round10) {
    Math.round10 = function(value, exp) {
      return decimalAdjust('round', value, exp);
    };
  }
  // Decimal floor
  if (!Math.floor10) {
    Math.floor10 = function(value, exp) {
      return decimalAdjust('floor', value, exp);
    };
  }
  // Decimal ceil
  if (!Math.ceil10) {
    Math.ceil10 = function(value, exp) {
      return decimalAdjust('ceil', value, exp);
    };
  }
})();

function addDaysToDate(day_us){
  var date_us = new Date()
  date_us.setDate(date_us.getDate() + day_us);
  return date_us;
}

function formatSpanishDate(date_us){
    var stringDateSpanish;

    var day = String(date_us.getDate());
    var month = String(date_us.getMonth()+1);
    var year = String(date_us.getFullYear()); 
    if (day.length==1){
      day='0' +  day;
    }
    if (month.length==1){
      month='0'+ month;
    }
    return String(day+'/'+month+'/'+year);
}
function mascara(event,mask,entero,decimal){
    //console.log(event);
    //console.log(event.keyCode);
    if (event.target.selectionStart != event.target.selectionEnd){
      /* Permite la inserccion */
      event.returnValue = true;
      return;
    }
    if (event.keyCode==8 ||  event.keyCode==46)
    {
      event.returnValue = true;
      return;
    }

    var texto = event.target.value;

    /* Si esta icompleta la mascara completarla */
    if (event.keyCode==9 || event.keyCode==13){
      if (texto.length!==mask.length && decimal==""){
        if (texto.length==0){
          texto = mask;
        } else {
          var textlong = texto.length;
          texto += mask.substr(textlong,80);
        }
        texto = texto.replace(/X/g,'0');
        event.target.value = texto;
        // replace cursor
        event.target.selectionStart = event.target.selectionEnd = texto.length;
        $(event.target).change();
          
      }
      if (decimal!==""){
          var obj = document.getElementById(event.target.id);
          $(obj).css("text-align","right");
          /* Determinar si esta el decimal  */
          var posc_decimal = texto.indexOf(decimal);
          mask = mask.replace(/9/g,'0');
          var posc_decimal_mask = mask.indexOf(decimal);
          var decimales  = mask.substr(posc_decimal_mask,50);
          /* completacion  */
          if (posc_decimal>0){
            var decimal_texto  = texto.substr(posc_decimal,50);
            /* se procede a completar  */
            texto += decimales.substr(decimal_texto.length,50);
          } else {
            //console.log("longitud:" + texto.length);
            if (texto.length<1){
              texto += '0' + decimales;
            } else {
              texto += decimales;
            }
          }
          var long_text = texto.length;
          var conteo = 0;
          var vistoDecimal = false;
          var cadena = '';
          /* Comienza colocar los separadores de miles */
          for(j=long_text;j>-1;j--)
          {
            var digito = texto.substr(j,1);
            cadena +=digito;
            if (vistoDecimal){
              conteo++;
              if (conteo==3){
                conteo = 0;
                if (j>0){
                  cadena += entero;
                }

              }
            }
             if (digito==decimal){
              vistoDecimal = true;
            }

          } // fin del for
          texto = '';
          long_text=cadena.length;
          for (var i=long_text; i>-1; i--) {
              texto += cadena.substr(i,1); 
          }

          event.target.value = texto;  
          $(event.target).change();
      }
      //console.log(event);
      event.returnValue = true;
      return;
    } // fin del 9 - 13
    texto+=event.key;
    var longitud = mask.length;
    var digito = ''; 
    var comparar = '';
    if (texto.length>longitud){
      event.returnValue = false;
      return ;
    }
    if (decimal.length>0){
      /* Numerica datos */
      var obj = document.getElementById(event.target.id);
      $(obj).css("text-align","right");

      var ubic_decimal = mask.indexOf(decimal);
      var decimales = mask.substr(ubic_decimal,50);

      /* Si escribe el decimal */
      if (event.keyCode==110 || event.keyCode==190 || event.keyCode==188)
      {
        if (texto.indexOf(decimal)>0){
          event.returnValue = false;
          return;
        } else {
          event.target.value+=decimal;
          event.returnValue = false;
          return;
        }
      } 

      var numero = event.key;
      comparar=parseInt(numero); 
      if (isNaN(comparar)){
        event.returnValue = false;
        return;
      } else {
        if ((texto.length==(ubic_decimal+1)) && texto.indexOf(decimal)<0){
          event.target.value+=decimal;
        } else {
          if (texto.indexOf(decimal)>0){
            var posc_decimal = texto.indexOf(decimal);
            var valor_decimal = texto.substr(posc_decimal,50);
            //valor_decimal += event.key;
            if (valor_decimal.length>decimales.length){
              event.returnValue = false;
              return;
            } 
          }
        }
        event.returnValue = true;
        return;
      }

    } else {
      /* Mascara Textuales Numerica */
      for (i=0;i<longitud;i++){
        digito = mask.substr(i,1);
        // funcion numerica de 0-9
        if (digito=='X') {
            comparar=parseInt(texto.substr(i,1)); 
            if (isNaN(comparar)){
              event.returnValue = false;
              break;
            } else {
              event.returnValue = true;
              if (texto.length==i+1){
                break;
              }
            }
        } else {
          // Elemento complemento
          comparar = texto.substr(i,1);
          esnumero=parseInt(texto.substr(i,1)); 
          if (digito!==comparar && isNaN(esnumero)){
              break;
          } else {
              if (digito==comparar) {
                if (!isNaN(esnumero)){
                  texto+=comparar;
                }
                event.returnValue = true;
              } else {
                texto+=comparar;
                event.target.value = event.target.value + digito;
                event.returnValue = true;
                // ver si adelante hay un X o 9 antes de terminar
                verDigito = mask.substr(i+1,1); 
                if (verDigito=='X'){
                  break;
                }
              }

          } 
        }

      }      
    }
}

function texto_mayuscula(event){
   if (event.keyCode==9 || event.keyCode==8  ||  event.keyCode==46 || event.keyCode==13)
    {
      event.returnValue = true;
      return;
    }

    var letra = String(event.key);

    if (letra.length>1){
      event.returnValue = false;
      return;
    }   
    letra = String(letra.toUpperCase());

    var pasa = /^[0-9A-ZÑ\s\&\#\$\%\.\,]$/g ;
    if (!pasa.test(letra)){
      event.returnValue = false;
      return;
    }
    // get old value
    var start = event.target.selectionStart;
    var end = event.target.selectionEnd;
    var oldValue = event.target.value;

    // replace point and change input value
    var newValue = oldValue.slice(0, start) + letra + oldValue.slice(end);
    event.target.value = newValue;

    // replace cursor
    event.target.selectionStart = event.target.selectionEnd = start + 1;

    event.preventDefault();

}
function solo_texto(event){
   //console.log(event.keyCode);
   if (event.keyCode==9 || event.keyCode==8  ||  event.keyCode==46 || event.keyCode==13 || event.keyCode==39 || event.keyCode==37)
    {
      event.returnValue = true;
      return;
    }

    var letra = String(event.key);

    if (letra.length>1){
      event.returnValue = false;
      return;
    }   
    //letra = String(letra.toUpperCase());

    var pasa = /^[0-9A-Za-zÑ\s\&\#\$\%\.\,\-íáúóéÁÍÚÓÉ]$/g ;
    if (!pasa.test(letra)){
      event.returnValue = false;
      return;
    }
    // get old value
    var start = event.target.selectionStart;
    var end = event.target.selectionEnd;
    var oldValue = event.target.value;

    // replace point and change input value
    var newValue = oldValue.slice(0, start) + letra + oldValue.slice(end);
    event.target.value = newValue;

    // replace cursor
    event.target.selectionStart = event.target.selectionEnd = start + 1;

    event.preventDefault();

}
function solo_numero(event){
  if (event.keyCode==9 || event.keyCode==8  ||  event.keyCode==46 || event.keyCode==13 || event.keyCode==39 || event.keyCode==37)
   {
     event.returnValue = true;
     return;
   }

   var letra = String(event.key);

   if (letra.length>1){
     event.returnValue = false;
     return;
   }   
   //letra = String(letra.toUpperCase());

   var pasa = /^[0-9]$/g ;
   if (!pasa.test(letra)){
     event.returnValue = false;
     return;
   }
   // get old value
   var start = event.target.selectionStart;
   var end = event.target.selectionEnd;
   var oldValue = event.target.value;

   // replace point and change input value
   var newValue = oldValue.slice(0, start) + letra + oldValue.slice(end);
   event.target.value = newValue;

   // replace cursor
   event.target.selectionStart = event.target.selectionEnd = start + 1;

   event.preventDefault();

}

function validaFecha(d_fecha){
    var valor = true;
    var expr = /^[0-3][0-9]\/[0-1][0-9]\/[0-9]{4}$/g;

    if (expr.test(d_fecha)){
       valor = false

    }
    return valor;
}