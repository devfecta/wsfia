const axios = require("axios").default;

class Business {

    constructor() {}

    addBusiness = async (data) => {

        let formData = JSON.parse(data);
        //console.log(formData);
        let confirmation = false;

        try {

            let params = new URLSearchParams();
            params.append('businessName', formData.businessName);
            params.append('station', formData.station);
            params.append('streetAddress', formData.streetAddress);
            params.append('city', formData.city);
            params.append('states', formData.states);
            params.append('zipcode', formData.zipcode);
            params.append('phone', formData.phone);
            params.append('url', formData.url);
            params.append('services', formData.services);
            params.append('departmentType', formData.departmentType);
            params.append('class', 'Business');
            params.append('method', 'createBusiness');

            await axios.post('http://localhost/wsfia-dev/configuration/api.php'
                , params
            )
            .then(response => {
                confirmation = response.data;
            })
            //.catch(error => displayError(error));
            .catch(error => console.log(error));
            
        }
        catch (e) {
            console.error(e);
        }

        return confirmation;
        
    }

}

module.exports = Business;