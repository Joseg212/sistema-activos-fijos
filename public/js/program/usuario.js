$(document).ready(function(){

	$(document).on("click",".selMyAvatar",function(event){
		event.preventDefault();
		var nameAvatar = $(this).data('name');
		var imgAvartar  = rootImagenAvatar + nameAvatar + ".png";

		$("#siAvatar").val("SI");
		$("#nameAvatar").val("");
		$("#nameAvatar").val(nameAvatar);
		$("#softmod_masterbundle_usuario_fotoUsuario").val(nameAvatar);

		$("#imgUserModSoft").attr("src",imgAvartar);		

	});

	$("#selImgFoto").click(function(event){
		event.preventDefault()

		$("#imgFileSoftMod").click();
	});

	$("#softmod_masterbundle_usuario_idPais").change(function(){
		//var idPais = $(this).find("option:selected").text();
		var idPais = $(this).val();

		//console.log("pais seleccionado:" + idPais);
		var rootLoadCity = returnPath('webpage_user_loadCity');

		var htmlOptions = ''; 

		$.ajax({
			data:{'idPais':idPais},
			type:'post',
			dataType:'json',
			url:rootLoadCity,
			error:function (err,txt,thr){
				$('body').empty();
				$('body').html(err.responseText);
			},
			success: function(result){
				console.log(result);
				if (result.ok=='01'){

					$.each(result.datos,function(posc,items){
						console.log(items);
						htmlOptions+='<option value="'+items.id+'">'+items.nombre+'</option>';
					});

					$("#softmod_masterbundle_usuario_idCiudad").empty();
					$("#softmod_masterbundle_usuario_idCiudad").append(htmlOptions);
				} else {
					swal("Registro de Usuario",result.msg,"error");
				}
			}
		})
	})

}); // Fin de Document Ready


LimitDescSelR = 7;
LimitMaxSelR = 7;

function left_selAvatar() {
	if (LimitDescSelR>0){
		LimitDescSelR-=1;
		$('#moveCarusel').animate({
		'marginLeft' : "-=25.6rem" //moves left
		},1000);
	}
}
function right_selAvatar(e) {
	if (LimitDescSelR<LimitMaxSelR){
	 LimitDescSelR+=1;
	 $('#moveCarusel').animate({
     'marginLeft' : "+=25.6rem" //moves left
     },1000);
	}
}

var validos = ['image/png','image/jpeg'];

function validarExtension(file){
	for(var i=0;i<validos.length;i++){
		if(file.type===validos[i]){
			return true;
		}
	}
	return false;
}

function muestraImagen(ev){

	var file = ev.target.files[0];
	var result = false;

	console.log(file);

	if (validarExtension(file)){
		var fotoPrevia = document.getElementById('imgUserModSoft');
		fotoPrevia.src = '';
		fotoPrevia.src = window.URL.createObjectURL(file);
	
		$("#siAvatar").val("NO");
		$("#nameAvatar").val("");
		$("#softmod_masterbundle_usuario_fotoUsuario").val("fotousuario");
		//$("#typeImage").val("Photo");
		//$("#nameAvatar").val("");
		result = true;
	} else {
		swal("Registro de Usuario","El archivo debe ser una imagen jpg o png!!","error");
	}
	return result;
}