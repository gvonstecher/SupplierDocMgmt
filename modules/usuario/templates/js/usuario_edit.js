
$(function () {
    if($('#id').val() != 0){
        var password_required = false;
    } else {
        password_required = true;
    }
    $("#form").validate({
        rules: {
            username: {
                required: true,
                minlength: 2
            },
            password: {
                required: password_required,
                minlength: 5
            },
            password2: {
                required: password_required,
                minlength: 5,
                equalTo: "#password"
            },
            mail: {
                required: true,
                email: true
            },
        },
        messages: {
            username: {
                required: "Ingrese un usuario",
                minlength: "Tu usuario debe tener al menos dos caracteres"
            },
            password: {
                required: "Ingrese una contrasena",
                minlength: "Tu contraseña debe tener al menos 5 caracteres"
            },
            password2: {
                required: "Ingrese una contrasena",
                minlength: "Tu contraseña debe tener al menos 5 caracteres",
                equalTo: "Las contraseñas deben coincidir"
            },
            mail: "Ingrese un correo válido"
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

$(document).ready(function() {
    $('#plantas').select2();
});