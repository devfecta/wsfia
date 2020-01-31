const axios = require("axios").default;

class Membership {

    constructor() {}

    //console.log(window.sessionStorage);
    //console.log(Math.random().toString(30).substring(2, 6));

    addMember = async (data) => {

        try {

            const formData = new FormData(document.querySelector("#membershipForm"));

            formData.append('class', 'Membership');
            formData.append('method', 'addMember');

            formData.forEach(value => {
                console.log(value);
            });
            
            await fetch('http://localhost/wsfia-dev/configuration/api.php'
                , { method: 'POST'
                , body: formData
                }
            )
            .then(response => response.json())
            .then(json => {
                if (json) {
                    window.location.replace("/register/member/confirmation");
                }
            })
            .catch(error => displayError(error));
            
            
        }
        catch {
            console.error('catch error');
        }

        return false;

        let formDataSerialized = {};
        let formLength = formData.length;

        document.cookie = 'company=WSIFA';

        document.cookie.split(';')
            .filter(cookies => cookies.indexOf('PHPSESSID') > 0)
            .map(cookies = (cookies) => {

                
                formDataSerialized = {...formDataSerialized, [`${cookies.split('=')[0].trim()}`]: cookies.split('=')[1]}							
            });

        const formView = document.querySelector('#RegistrationForm');
        //formView.innerHTML = '';

        let messageRow = document.createElement('div');
        messageRow.setAttribute('class', 'row');

        if(formLength > 0) {
            // Get form data by value.
            formData.querySelectorAll('input[type="text"], select, textarea').forEach( input => {
                        
                    formDataSerialized = {...formDataSerialized, [`${input.id}`]: input.value}
                    
                }
            );
            // Get form data by checked aka selected.
            let index = 0;
            let currentName = '';
            formData.querySelectorAll('input[type="checkbox"], input[type="radio"]').forEach( input => {

                    if(currentName !== input.name) {
                        index = 0;
                        currentName = input.name;
                    }

                    formDataSerialized[input.name] = {...formDataSerialized[input.name], [`${index}`]: {"id": input.value, "value": input.getAttribute('data-value'), "checked": input.checked}};

                    index++;

                }
            );

            //console.log(formDataSerialized);
            $(document).ready(function(){
                $.ajax({
                    type: "POST",
                    url: window.location.href + "/configuration/api.php",
                    data: {
                        'class': 'RegisterMembership',
                        'method': 'addRegistrants',
                        'formData': JSON.stringify(formDataSerialized)
                    },
                    success: function(data, status) {					

                        let messageDiv = document.createElement('div');
                        messageDiv.setAttribute('class', 'col-md-12');

                        //console.log(data);

                        if(data) {

                            messageDiv.innerHTML = '<p>' + formDataSerialized.firstName + ' ' + formDataSerialized.lastName + ' has been added.</p>';
                            messageRow.appendChild(messageDiv);

                            
                            
                        } 
                        else {
                            messageDiv.innerHTML = '<p>There has been a SQL error.</p>';
                            messageRow.appendChild(messageDiv);
                        }

                        //formView.appendChild(messageRow);

                    },
                    //timeout: 1000,
                    error: displayError
                })
                
            });
        }

        formView.innerHTML = `<p class="text-danger">If you would like to register multiple people click on the 
            "Add Another Person" button to fill out the registration for again. Otherwise, click on the "Register" button 
            to finish the registration process.</p>`;

        formView.appendChild(messageRow);
        
        getRegistrants(formDataSerialized.PHPSESSID);

    }

}

module.exports = Membership;