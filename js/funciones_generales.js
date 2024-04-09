function beginSearch(url,formId,returnId){

	searchParameters = new Array();

	$('#' + formId + ' input, #'+ formId +' select, #'+ formId +' radio').each(
		function(index){
			var input = $(this);

			switch(input.prop('type')){

				case 'text':
				case 'email':
				case 'hidden':
				case 'select-one':
					if(input.val() !== ''){
						searchParameters.push({
							'nombre' : input.attr('name'),
							'valor'	 : input.val()
						});
					}
				break;

				case 'radio':
				case 'checkbox':
					if(input.is(':checked')){
						searchParameters.push({
							'nombre' : input.attr('name'),
							'valor'	 : input.val()
						});
					}
				break;

				default:
				break;

			}
		}
	);

	$.ajax({
		url : url,
		cache: false,
		type: 'post',
		data : { parametros : searchParameters }
	}).done(function(result){
		$('#' + returnId).html(result);
	}).fail(function(error){
		console.error(error);
		return false;
	});

}


function replaceTags(element,replaceTag){

	//contenidoOriginal = element.html();
	contenidoOriginal  = element.prop('innerHTML');
	contenidoAProcesar = '';
	contenidoProcesado = '';

	for(i=0;i<replaceTag.length;i++){

		if(i==0){
			contenidoAProcesar = contenidoOriginal;
		}
		
		// Solo procesa array multidimensionales si lo que se está reemplazado es informacion para reemplazar n veces
		// un pedazo de código (ie. una row de una tabla)
		if(replaceTag[i].length != undefined){

			contenidoAProcesar = contenidoOriginal;

			for(j=0;j<replaceTag[i].length;j++){		
				contenidoAProcesar = contenidoAProcesar.replaceAll(replaceTag[i][j]['tag'],replaceTag[i][j]['tagValue']);
			}

			contenidoProcesado += contenidoAProcesar;

		}else{

			contenidoAProcesar = contenidoAProcesar.replaceAll(replaceTag[i]['tag'],replaceTag[i]['tagValue']);

			contenidoProcesado = contenidoAProcesar;
		}
	}

	element.html(contenidoProcesado);
}

function applyDatePicker(elementId,DateAndTime=false){
	
	if(DateAndTime){
		formatOption = 'DD/MM/YYYY HH:ss';

		dateRangePickerOptions = {
			"singleDatePicker": true,
			"showDropdowns": true,
			"timePicker": true,
			"timePicker24Hour": true,
			"autoApply": true,
			locale: {
				format : formatOption,
			}
		};

	}else{
		formatOption = "DD/MM/YYYY";

		dateRangePickerOptions = {
			"singleDatePicker": true,
			"showDropdowns": true,
			"timePicker": false,
			"autoApply": true,
			locale: {
				format : formatOption,
			}
		};
	}

	$(elementId).daterangepicker(dateRangePickerOptions);
}