$(document).ready(function(){
    
    let val = $('#nombre2').val();
    if(val.trim().length != 0){    
        $('#vehiculo2-nombre').show();
        $('#vehiculo2-dominio').show();
    } else {
        $('#vehiculo2-nombre').hide();
        $('#vehiculo2-dominio').hide();
    }


    $('#nombre').on('change', function () {
        if($('#nombre').val() == 'Camion'){
            $('#vehiculo2-nombre').show();
            $('#vehiculo2-dominio').show();
        } else {
            $('#vehiculo2-nombre').val("");
            $('#vehiculo2-nombre').hide();
            $('#vehiculo2-dominio').val("");
            $('#vehiculo2-dominio').hide();
        }
    });
});