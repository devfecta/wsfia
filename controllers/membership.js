const axios = require("axios").default;

//const url = '';

class Membership {

    constructor() {}
    //console.log(window.sessionStorage);
    //console.log(Math.random().toString(30).substring(2, 6));
    addMember = async (data) => {

        let formData = JSON.parse(data);
        //console.log(formData);
        let confirmation = false;
        
        try {

            let params = new URLSearchParams();

            params.append('sessionId', formData.sessionId);
            params.append('emailAddress', formData.emailAddress);
            params.append('firstName', formData.firstName);
            params.append('lastName', formData.lastName);
            params.append('jobTitle', formData.jobTitle);
            params.append('studentId', formData.studentId);
            params.append('areas', formData.areas);
            params.append('businesses', formData.businesses);
            params.append('class', 'Membership');
            params.append('method', 'addMember');
            //console.log(params);

            await axios.post(process.env.API + '/api.php'
                , params
            )
            .then(response => {
                //console.log(response.data);
                //return false;
                confirmation = response.data;
            })
            .catch(error => console.log(error));

            return confirmation;
            
        }
        catch (e) {
            console.error(e);
        }
/*
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
*/
    }

    getRegistrants = async (data) => {
        //let registrants = JSON.parse(data);

        let response = '';

        try {
            //console.log(data);
            let params = new URLSearchParams();

            params.append('sessionId', data);
            params.append('class', 'Membership');
            params.append('method', 'getRegistrants');
            //console.log(params);
            
            return await axios.post(process.env.API + '/api.php'
                , params
            )
            .then(response => response.data)
            .catch(error => console.log(error));

            return response;
            
        }
        catch (e) {
            console.error(e);
        }
    }

    registerMember = async (data) => {

        let formData = JSON.parse(data);
        //console.log(formData);
        let confirmation = false;

        //let registrants = await this.getRegistrants(sessionId);
        
        try {
            
            let params = new URLSearchParams();

            params.append('sessionId', formData.sessionId);
            params.append('emailAddress', formData.emailAddress);
            params.append('businessId', formData.business);
            params.append('class', 'Membership');
            params.append('method', 'register');
            //console.log(params);

            return await axios.post(process.env.API + '/api.php'
                , params
            )
            .then(response => {
                const lineItems = response.data;

                params = new URLSearchParams();
                params.append('lineItems', JSON.stringify(response.data));
                //console.log("Parameters");
                //console.log(params);
                
                return axios.post(process.env.API + '/PayPal-PHP-SDK/SendInvoice.php'
                    , params
                )
                .then(response => lineItems)
                .catch(error => console.log(error));
                //return false;
                //confirmation = response.data;
            })
            .catch(error => console.log(error));
            
            return confirmation;
            
        }
        catch (e) {
            console.error(e);
        }
    }

    login = async (data) => {

        let formData = JSON.parse(data);
        let response = '';

        try {
            //console.log(data);
            let params = new URLSearchParams();

            params.append('emailAddress', formData.inputEmail);
            params.append('password', formData.inputPassword);
            params.append('class', 'Membership');
            params.append('method', 'login');
            //console.log("Params");
            //console.log(params);

            return await axios.post(process.env.API + '/api.php'
                , params
            )
            .then(response => response.data)
            .catch(error => console.log(error));
            
        }
        catch (e) {
            console.error(e);
        }
    }
    
    getAccountInfo = async (wsfiaId) => {
        try {
            let parameters = 'class=Membership';
            parameters += '&method=getAccountInfo';
            parameters += '&wsfiaId=' + wsfiaId;

            return await axios.get(process.env.API + '/api.php?' + parameters)
            .then(response => response.data)
            .then(json => json)
            .catch(error => error);
        }
        catch (e) {
            console.error(e);
        }
    }

    resetPassword = async (data) => {

        let formData = JSON.parse(data);
        
        try {
            //console.log(data);
            let params = new URLSearchParams();

            params.append('emailAddress', formData.inputEmail);
            params.append('password', formData.inputPassword);
            params.append('class', 'Membership');
            params.append('method', 'resetPassword');

            return await axios.post(process.env.API + '/api.php'
                , params
            )
            .then(response => response.data)
            .catch(error => console.log(error));
            
        }
        catch (e) {
            console.error(e);
        }
        
    }

    getRenewals = async (data) => {

        let formData = JSON.parse(data);

        let parameters = 'class=Membership';
            parameters += '&method=getRenewals';
            parameters += '&businessId=' + formData.businessId;

        return await axios.get(process.env.API + '/api.php?' + parameters)
        .then(response => response.data)
        .then(json => json)
        .catch(error => error);
    }

    renewMember = async (data) => {

        let formData = JSON.parse(data);
        //console.log(formData);
        let confirmation = false;
        //let registrants = await this.getRegistrants(sessionId);
        try {
            
            let params = new URLSearchParams();

            params.append('members', JSON.stringify(formData.members));
            params.append('emailAddress', formData.emailAddress);
            params.append('businessId', formData.business);
            params.append('class', 'Membership');
            params.append('method', 'renew');
            //console.log(params);

            return await axios.post(process.env.API + '/api.php'
                , params
            )
            .then(response => {
                const lineItems = response.data;

                params = new URLSearchParams();
                params.append('lineItems', JSON.stringify(response.data));
                //console.log("Parameters");
                //console.log(params);
                
                return axios.post(process.env.API + '/PayPal-PHP-SDK/SendInvoice.php'
                    , params
                )
                .then(response => lineItems)
                .catch(error => console.log(error));
                //return false;
                //confirmation = response.data;
            })
            .catch(error => console.log(error));
            
            return confirmation;
            
        }
        catch (e) {
            console.error(e);
        }
    }

    updateAccountInfo = async (data) => {

        let formData = JSON.parse(data);
        /*
        {
            firstName: 'Kevin',
            lastName: 'Kelm',
            jobTitle: 'Fire Inspector',
            studentId: '',
            areas: [ '2', '3', '10' ],
            searchTextBox: 'test ',
            businesses: [ '232', '270' ]
        }
        */
        try {
            
            let params = new URLSearchParams();

            params.append('class', 'Membership');
            params.append('method', 'updateAccountInfo');
            params.append('userId', formData.userId);
            params.append('firstName', formData.firstName);
            params.append('lastName', formData.lastName);
            params.append('jobTitle', formData.jobTitle);
            params.append('studentId', formData.studentId);
            params.append('areas', formData.areas);
            params.append('businesses', formData.businesses);
            //console.log("Parameters");
            //console.log(params);

            return await axios.post(process.env.API + '/api.php'
                , params
            )
            .then(response => response.data)
            .catch(error => console.log(error));            
        }
        catch (e) {
            console.error(e);
        }
        
    }

}

module.exports = Membership;
