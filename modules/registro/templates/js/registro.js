$.validator.addMethod(
    "regex",
    function(value, element, regexp) {
        var check = false;
        return this.optional(element) || regexp.test(value);
    },
    "El nombre de usuario no es valido."
);


$(function () {
    $("#form").validate({
        rules: {
            username: {
                required: true,
                minlength: 2,
                maxlength: 15,
                regex: /^[a-zA-Z0-9]+$/,
            },
            password: {
                required: true,
                minlength: 5
            },
            password2: {
                required: true,
                minlength: 5,
                equalTo: "#password"
            },
            mail: {
                required: true,
                email: true
            },
            planta: {
                required: true
            }
        },
        messages: {
            username: {
                required: "Ingrese un usuario",
                minlength: "Tu usuario debe tener al menos dos caracteres",
                maxlength: "Tu usuario no debe tener mas de 15 caracteres.",
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
            mail: "Ingrese un correo válido",
            planta: "Seleccione una planta"
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
          error.addClass('invalid-feedback');
          element.closest('.container-input').append(error);
        },
        highlight: function (element, errorClass, validClass) {
          $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
          $(element).removeClass('is-invalid');
        }
    });
});