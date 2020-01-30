const axios = require("axios").default;

class Business {

    addBusiness = async (data) => {

        let formData = JSON.parse(data);

        console.log(formData);

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

            let res = await axios.post('http://localhost/wsfia-dev/configuration/api.php'
                , params
            )
            .then(response => {
                console.log(response.data);
                return response})
            .then(json => {
                //console.log(json);
                if (json) {
                    //window.location.replace("/register");
                }
            })
            //.catch(error => displayError(error));
            .catch(error => console.log(error));

            //const formData = new FormData(document.querySelector("#businessForm"));
            //const formData = JSON.parse(JSON.stringify(data));

            //formData.append('class', 'Business');
            //formData.append('method', 'createBusiness');

            //console.log(typeof(form));
            //return false;
            
            /*
            await fetch('http://localhost/wsfia-dev/configuration/api.php'
                , { method: 'POST'
                , body: form
                , headers: {"Content-type": "text/plain", "Accept": "text/plain", "Accept-Charset": "utf-8"}
                }
            )
            .then(response => response.json())
            .then(json => {
                console.log(json);
                if (json) {
                    //window.location.replace("/register");
                }
            })
            //.catch(error => displayError(error));
            .catch(error => console.log(error));
            */
            
        }
        catch (e) {
            console.error(e);
        }

        return false;
        
    }

}

module.exports = Business;