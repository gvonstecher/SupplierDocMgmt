function armaSelect(result, selected){
    var html = '<option></option>';
    result.forEach(element => {
        html += "<option value='"+element.id+"' data-vencimiento='"+element.vencimiento+"'>"+element.nombre+"</option>";
    });
    $('#id_tipo_documentacion').html(html);
    $('#id_tipo_documentacion').select2({
        placeholder: "Por favor, elija un tipo de documento"
    });
}

function actualizaListadoDocumentos(tipo_entidad,id_entidad){

    var nombre_tabla = '#data_table_'+tipo_entidad.toString()+'_'+id_entidad.toString();
    
    new DataTable(nombre_tabla, {
        processing:true, 
        bDestroy: true, 
        ajax:{
            url: 'modules/proveedor_planta/functions_ajax.php',
            type: 'POST',
            dataSrc:"",
            processing: true,
            serverSide: true,
            data: {action : 'busca_documentos',tipo_entidad : tipo_entidad, id_entidad:id_entidad},
            dataType: 'json'
        },
        columns: [
            {
                data: 'nombre_documento'
            },
            {
                data: 'fecha_vencimiento'
            },
            {
                data: 'url_documento',
                render: function (data) {
                    return '<a class="btn" href="'+data+'" target="_blank"><i class="fas fa-file"></i></a>';
                }
            },
            {
                data: 'id_documento',
                render: function (data) {
                    return '<a class="btn btn-borrar_documentacion" data-id_documento="'+data+'"><i class="fas fa-trash"></i></a>';
                }
            },
        ],
        language: {
            lengthMenu: "Mostrar _MENU_ documentos por pagina",
            zeroRecords: "No hay documentos cargados",
            info: "Mostrando página _PAGE_ de _PAGES_",
            infoEmpty: "No hay documentos cargados",
            infoFiltered: "(filtrado de uno total de _MAX_ registros)",
            loadingRecords: "Cargando...",
            search: "Buscar:",
            paginate: {
                first: "Primero",
                last: "Ultimo",
                next: "Siguiente",
                previous: "Anterior"
            },
        }
    });

}

if($('#tipouser').val() != 0){
    $(".hide-su").hide();
}

$(document).ready(function(){
    
    
    
    selected = null;

    //$('#fecha_vto_documentacion').inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' })
    $('#fecha_vto_documentacion').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        autoApply: true,
        minDate: moment().format('DD/MM/YYYY'),
        locale: {
            format: "DD/MM/YYYY",
            applyLabel: "Apply",
            cancelLabel: "Cancel",
            fromLabel: "From",
            toLabel: "To",
            customRangeLabel: "Custom",
            weekLabel: "W",
            daysOfWeek: [
                "Do",
                "Lu",
                "Ma",
                "Mie",
                "Jue",
                "Vie",
                "Sa"
            ],
            monthNames: [
                "Enero",
                "Febrero",
                "Marzo",
                "Abril",
                "Mayo",
                "Junio",
                "Julio",
                "Agosto",
                "Septiembre",
                "Octubre",
                "Noviembre",
                "Deciembre"
            ],
            "firstDay": 1
            }
      });


    //incializo tablas con documentacion
    $('.table-documentos').each(function(){
        var id= $(this).data('id');
        var tipo_entidad = $(this).data('tipo_entidad');
        actualizaListadoDocumentos(tipo_entidad,id);
    });


    $('.btn-agregar_documentacion').on('click', function () {
        var id_entidad = $(this).data('id_entidad');
        var tipo_entidad = $(this).data('tipo_entidad');
        var nombre_entidad = $(this).data('nombre_entidad')
        var id_proveedor = $(this).data('id_proveedor')
        
        $("#id_entidad_asociada_documentacion").val(id_entidad);
        $("#tipo_entidad_documentacion").val(tipo_entidad);
        $("#titulo_modal").html('Agregar Documentacion ' + nombre_entidad);

        $.ajax({
			url : 'modules/proveedor_planta/functions_ajax.php',
			cache: false,
			type: 'post',
            dataType: 'json',
			data : { action : 'busca_tipos_documentos', tipo_entidad : tipo_entidad, id_entidad: id_entidad, id_proveedor:id_proveedor}
            
		}).done(function(result) {
			if(result){
				armaSelect(result, selected);
			} else {
                console.log("error");
			}
		});

    });

    $('#filename_documentacion').on('change', function(){
        $('#filename_documentacion').removeClass('is-invalid');
        $('#filename_documentacion-error').html('');
    })

    $('#id_tipo_documentacion').on('change', function () {
        if($('#id_tipo_documentacion option:selected').data('vencimiento') == 1){
            $('#vencimiento-container').show();
            $("#fecha_vto_documentacion").prop('required',true);
            $('#ignora_fecha').val('0');
        } else {
            $('#vencimiento-container').hide();
            $("#fecha_vto_documentacion").prop('required',false);
            $('#ignora_fecha').val('1');
        }
    });

    $('#cancelar_form_documentacion').on('click', function () { 
        document.getElementById("form_documentacion").reset();
    });

    $('#modal-documentacion').on('hidden.bs.modal', function (e) {
        $('#filename_documentacion').val('');
        $('#vencimiento-container').val('');
        $('#vencimiento-container').hide();
        $("#fecha_vto_documentacion").prop('required',false);
    });

    $('#form_documentacion').submit(function(e){

        e.preventDefault();
        var errors = false;

        if(filename_documentacion.files[0].size > 2097152){
            $('#filename_documentacion').addClass('is-invalid');
            $('#filename_documentacion-error').html('El tamaño del archivo debe ser menor a 2mb');
            errors = true;
        }

        if(!errors){
            var formData = new FormData();
            formData.append("action", "agrega_documento");
            formData.append("id_entidad_asociada_documentacion", $('#id_entidad_asociada_documentacion').val());
            formData.append("tipo_entidad_documentacion", $('#tipo_entidad_documentacion').val());
            formData.append("id_tipo_documentacion", $('#id_tipo_documentacion').val());
            formData.append("fecha_vto_documentacion", $('#fecha_vto_documentacion').val());
            formData.append("filename_documentacion", filename_documentacion.files[0]);
            formData.append("fecha_vto_documentacion", $('#fecha_vto_documentacion').val());
            formData.append("ignora_fecha", $('#ignora_fecha').val());

            $.ajax({
                url : 'modules/proveedor_planta/functions_ajax.php',
                cache: false,
                type: 'POST',
                dataType: 'json',
                data : formData,
                processData: false,
                contentType: false
            }).done(function(result) {
                if(result){
                    actualizaListadoDocumentos($('#tipo_entidad_documentacion').val(),$('#id_entidad_asociada_documentacion').val());
                    $('#modal-documentacion').modal('hide');
                    $('#flag_documentacion').val('1');

                } else {
                    console.log("error");
                }
            });

        }

        console.log(filename_documentacion);
    
        
    });

    $("div#dropzone").dropzone({ 
        uploadMultiple: false,
        maxFilesize: 900
    });


	$('body').on('click', '.btn-borrar_documentacion', function () {
        
        var id_documento = $(this).data('id_documento');
        var id_entidad = $(this).closest('.table-documentos').data('id');
        var tipo_entidad = $(this).closest('.table-documentos').data('tipo_entidad');

        $.ajax({
			url : 'modules/proveedor_planta/functions_ajax.php',
			cache: false,
			type: 'post',
            dataType: 'json',
			data : { action : 'borra_documento', id : id_documento}
            
		}).done(function(result) {
			if(result){
                console.log(tipo_entidad);
                console.log(id_entidad);
				actualizaListadoDocumentos(tipo_entidad,id_entidad);
			} else {
                console.log("error");
			}
		});
    });


    $('#form_estado').submit(function(e){

        e.preventDefault();
        $('#estado-mensaje').html('');
    
        var formData = new FormData();
        formData.append("action", "edita_estado");
        formData.append("estado", $('#estado').val());
        formData.append("estado_detalle", $('#estado_detalle').val());
        formData.append("id_proveedor_planta", $('#id_proveedor_planta').val());
        formData.append("id_proveedor", $('#id_proveedor').val());


        $.ajax({
			url : 'modules/proveedor_planta/functions_ajax.php',
			cache: false,
			type: 'POST',
            dataType: 'json',
			data : formData,
            processData: false,
            contentType: false
		}).done(function(result) {
			if(result){
                $('#estado-mensaje').html('El estado ha sido guardado. Se le envio un correo al proveedor. Tenga en cuenta que si el proveedor tiene documentación vencida el estado figurará como "no habilitado"');

			} else {
                $('#estado-mensaje').html('Hubo un problema, por favor intente nuevamente');
			}
		});
    });

    $('body').on('click', '#presentar-documentacion', function () {
        var formData = new FormData();
        formData.append("id_proveedor_planta", $('#id_proveedor_planta').val());
        formData.append("action", "presentar_documentacion");

        $.ajax({
			url : 'modules/proveedor_planta/functions_ajax.php',
			cache: false,
			type: 'POST',
            dataType: 'json',
			data : formData,
            processData: false,
            contentType: false
		}).done(function(result) {
			if(result){
                $('#presentarDocumentacion-mensaje').html('La documentacion ha sido presentada. Se le envio un correo al administrador');

			} else {
                $('#presentarDocumentacion-mensaje').html('Hubo un problema, por favor intente nuevamente');
			}
		});


        
    });

});