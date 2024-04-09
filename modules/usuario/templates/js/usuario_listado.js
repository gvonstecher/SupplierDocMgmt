$(document).ready(function(){
    new DataTable('#data_table_proveedores', {
        processing:true, 
        bDestroy: true, 
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
});