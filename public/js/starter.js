$(document).ready(function(e){
    const validationRule = {
        firstName: {
            presence: {
                allowEmpty: false
            },
            format: {
                pattern: "[A-Za-z]+",
                message: "First Name must be alphabets only"
            },
            length: {
                minimum: 2,
                maximum: 32
            }
        },
        lastName: {
            presence: {
                allowEmpty: false
            },
            format: {
                pattern: "[A-Za-z]+",
                message: "Last Name must be alphabets only"
            },
            length: {
                minimum: 2,
                maximum: 32
            }
        },
        email: {
            presence: {
                allowEmpty: false
            },
            email: true
        },
        subject: {
            presence: {
                allowEmpty: false
            },
            length: {
                minimum: 12,
                maximum: 200
            }
        },
        message: {
            presence: {
                allowEmpty: false
            },
            length: {
                minimum: 20,
                maximum: 1000
            }
        }
    }

    // Clear previous validation when the field change value
    $("input, textarea").keyup(function(e){
        if($(this).hasClass('is-invalid') || $(this).hasClass('is-valid')){
            $(this).removeClass('is-invalid').removeClass('is-valid');
        }
    })

    $("#enquiry-form").submit(function(e){
        e.preventDefault();

        // If label had been previously flagged for error, clear error
        $(".is-invalid").removeClass("is-invalid");
        $(".invalid-feedback").text('').removeClass('mb-3');


        // This is collection of submitted data from the form fields
        
        const submittedData = {
            firstName: $("#firstname").val(),
            lastName: $("#lastname").val(),
            email: $("#email").val(),
            subject: $("#subject").val(),
            message: $("#message").val()
        }

        // Although I already handled validation on the server-side script,
        // In a large application, handling it here would reduce dev-ops budget
        // So let's save the business!

        const resp = validate(submittedData, validationRule);
        if(typeof resp === 'object'){
            handleErrors(resp);
            return;
        }


        // Validation is done! Let's push to the server.
        const endpoint = "./api/enquiries"

        $.ajax(endpoint, {
            data: JSON.stringify(submittedData),
            contentType: 'application/json',
            type: 'POST',
            success: (response) => {
                if (response.hasOwnProperty('errors')) {
                    handleErrors(response.errors);
                    return;
                }

                if (response.hasOwnProperty('status')) {
                    if(response.status == 'saved') {
                        window.location = "./enquiries-saved"
                    }
                }
            }
        });
    });


    function handleErrors(errorObject){
        // Let people know their wrong doings.
        for (const key in errorObject) {
            $(`#${key.toLowerCase()}`).addClass("is-invalid");
            const errorMessage = errorObject[key].constructor === Array ? errorObject[key][0] : errorObject[key];
            $(`#${key.toLowerCase()}-feedback`).text(errorMessage).addClass('mb-3')
        }

        // A pat on the back for the right.
        $("input:not(is-invalid), textarea:not(is-invalid)").addClass('is-valid');
    }
});