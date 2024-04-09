$(function () {
    $("#form").validate({
        rules: {
            username: {
                required: true,
            },
            password: {
                required: true
            },
            planta:{
                required: true,
            }
        },
        messages: {
            username: {
                required: "Ingrese un usuario",
            },
            password: {
                required: "Ingrese una contrasena",
                minlength: "Tu contraseña debe tener al menos 5 caracteres"
            },
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
          error.addClass('invalid-feedback');
          element.closest('.col-lg-6').append(error);
        },
        highlight: function (element, errorClass, validClass) {
          $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
          $(element).removeClass('is-invalid');
        }
    });
});