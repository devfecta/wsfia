const axios = require("axios").default;

class Conference {

    constructor() {}

    addConferenceCurrentMembers = async (data) => {

        let memberData = JSON.parse(data);
        //console.log(memberData);
        let confirmation = false;

        try {
            
            let params = new URLSearchParams();

            params.append('sessionId', memberData.sessionId);
            params.append('memberIds', memberData.members);
            params.append('class', 'RegisterConferenceMember');
            params.append('method', 'addConferenceCurrentMembers');
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
    /**
     * Sets the dates an attendee will be attending at the conference.
     * @param {string} data 
     */
    setAttendingDate = (data) => {

        //console.log('setAttendingDate');

        let memberData = JSON.parse(data);

        //console.log(memberData);

        try {
            
            let params = new URLSearchParams();

            params.append('sessionId', memberData.sessionId);
            params.append('emailAddress', memberData.emailAddress);
            params.append('attendingDate', memberData.attendingDate);
            params.append('attendingChecked', memberData.attendingChecked);
            params.append('class', 'RegisterConferenceMember');
            params.append('method', 'setAttendingDate');
            
            axios.post(process.env.API + '/api.php'
                , params
            )
            .then(response => response.data)
            .catch(error => console.log(error));
            
        }
        catch (e) {
            console.error(e);
        }

    }
    /**
     * Sets the CEU field if the attendee requires it.
     * @param {string} data 
     */
    setCEU = (data) => {

        let memberData = JSON.parse(data);

        //console.log(memberData);

        try {
            
            let params = new URLSearchParams();

            params.append('sessionId', memberData.sessionId);
            params.append('emailAddress', memberData.emailAddress);
            params.append('ceu', memberData.ceu);
            params.append('class', 'RegisterConferenceMember');
            params.append('method', 'setCEU');
            
            axios.post(process.env.API + '/api.php'
                , params
            )
            .then(response => response.data)
            .catch(error => console.log(error));
            
        }
        catch (e) {
            console.error(e);
        }

    }
    /**
     * Sets the license type field for the attendee.
     * @param {string} data 
     */
    setLicenseType = (data) => {

        let memberData = JSON.parse(data);

        //console.log(memberData);

        try {
            
            let params = new URLSearchParams();

            params.append('sessionId', memberData.sessionId);
            params.append('emailAddress', memberData.emailAddress);
            params.append('licenseType', memberData.licenseType);
            params.append('class', 'RegisterConferenceMember');
            params.append('method', 'setLicenseType');
            
            axios.post(process.env.API + '/api.php'
                , params
            )
            .then(response => response.data)
            .catch(error => console.log(error));
            
        }
        catch (e) {
            console.error(e);
        }

    }
    /**
     * Sets the license number field for the attendee.
     * @param {string} data 
     */
    setLicenseNumber = (data) => {

        let memberData = JSON.parse(data);

        //console.log(memberData);

        try {
            
            let params = new URLSearchParams();

            params.append('sessionId', memberData.sessionId);
            params.append('emailAddress', memberData.emailAddress);
            params.append('licenseNumber', memberData.licenseNumber);
            params.append('class', 'RegisterConferenceMember');
            params.append('method', 'setLicenseNumber');
            
            axios.post(process.env.API + '/api.php'
                , params
            )
            .then(response => response.data)
            .catch(error => console.log(error));
            
        }
        catch (e) {
            console.error(e);
        }

    }    
    /**
     * Sets the banquet field if the attendee is attending it.
     * @param {string} data 
     */
     setBanquet = (data) => {

        let memberData = JSON.parse(data);
        
        try {
            
            let params = new URLSearchParams();

            params.append('sessionId', memberData.sessionId);
            params.append('emailAddress', memberData.emailAddress);
            params.append('banquet', memberData.banquet);
            params.append('class', 'RegisterConferenceMember');
            params.append('method', 'setBanquet');
            
            axios.post(process.env.API + '/api.php'
                , params
            )
            .then(response => response.data)
            .catch(error => console.log(error));
        }
        catch (e) {
            console.error(e);
        }
    }
    /**
     * Sets the banquet field if the attendee guest is attending it.
     * @param {string} data 
     */
     setBanquetGuest = (data) => {

        let memberData = JSON.parse(data);
        
        try {
            
            let params = new URLSearchParams();

            params.append('sessionId', memberData.sessionId);
            params.append('emailAddress', memberData.emailAddress);
            params.append('guestId', memberData.guestId);
            params.append('banquet', memberData.banquet);
            params.append('class', 'RegisterConferenceMember');
            params.append('method', 'setBanquetGuest');
            
            axios.post(process.env.API + '/api.php'
                , params
            )
            .then(response => response.data)
            .catch(error => console.log(error));
        }
        catch (e) {
            console.error(e);
        }
    }
    /**
     * Sets the banquet field if the attendee is attending it.
     * @param {string} data 
     */
     setVendorNight = (data) => {
         
        let memberData = JSON.parse(data);
        
        try {
            
            let params = new URLSearchParams();

            params.append('sessionId', memberData.sessionId);
            params.append('emailAddress', memberData.emailAddress);
            params.append('vendorNight', memberData.vendorNight);
            params.append('class', 'RegisterConferenceMember');
            params.append('method', 'setVendorNight');
            
            axios.post(process.env.API + '/api.php'
                , params
            )
            .then(response => response.data)
            .catch(error => console.log(error));
        }
        catch (e) {
            console.error(e);
        }
    }
    /**
     * Sets the banquet field if the attendee guest is attending it.
     * @param {string} data 
     */
     setVendorNightGuest = (data) => {
         
        let memberData = JSON.parse(data);
        
        try {
            
            let params = new URLSearchParams();

            params.append('sessionId', memberData.sessionId);
            params.append('emailAddress', memberData.emailAddress);
            params.append('guestId', memberData.guestId);
            params.append('vendorNight', memberData.vendorNight);
            params.append('class', 'RegisterConferenceMember');
            params.append('method', 'setVendorNightGuest');
            
            axios.post(process.env.API + '/api.php'
                , params
            )
            .then(response => response.data)
            .catch(error => console.log(error));
        }
        catch (e) {
            console.error(e);
        }
    }
    /**
     * Sets the vegetarian meal field for the attendee.
     * @param {string} data 
     */
    setVegetarianMeal = (data) => {
        let memberData = JSON.parse(data);
        try {
            
            let params = new URLSearchParams();

            console.log(memberData);

            params.append('sessionId', memberData.sessionId);
            params.append('emailAddress', memberData.emailAddress);
            params.append('vegetarianMeal', memberData.vegetarianMeal);
            params.append('class', 'RegisterConferenceMember');
            params.append('method', 'setVegetarianMeal');
            
            axios.post(process.env.API + '/api.php'
                , params
            )
            .then(response => response.data)
            .catch(error => console.log(error));
        }
        catch (e) {
            console.error(e);
        }
    }
    /**
     * Sets the vegetarian meal field for the attendee guest.
     * @param {string} data 
     */
     setVegetarianMealGuest = (data) => {
        let memberData = JSON.parse(data);
        try {
            
            let params = new URLSearchParams();

            params.append('sessionId', memberData.sessionId);
            params.append('emailAddress', memberData.emailAddress);
            params.append('guestId', memberData.guestId);
            params.append('vegetarianMeal', memberData.vegetarianMeal);
            params.append('class', 'RegisterConferenceMember');
            params.append('method', 'setVegetarianMealGuest');
            
            axios.post(process.env.API + '/api.php'
                , params
            )
            .then(response => response.data)
            .catch(error => console.log(error));
        }
        catch (e) {
            console.error(e);
        }
    }
    /**
     * Sets the guest name field for the attendee guest.
     * @param {string} data 
     */
     setGuestName = (data) => {

        let memberData = JSON.parse(data);

        try {
            
            let params = new URLSearchParams();

            params.append('sessionId', memberData.sessionId);
            params.append('emailAddress', memberData.emailAddress);
            params.append('guestId', memberData.guestId);
            params.append('guestName', memberData.guestName);
            params.append('class', 'RegisterConferenceMember');
            params.append('method', 'setGuestName');
            
            axios.post(process.env.API + '/api.php'
                , params
            )
            .then(response => response.data)
            .catch(error => console.log(error));
            
        }
        catch (e) {
            console.error(e);
        }

    }
    /**
     * Get order options based on type.
     * @param {*} inventoryType 
     * @returns JSON of order options.
     */
    getInventory = async (inventoryType) => {
        try {
            let parameters = 'class=Sponsor';
            parameters += '&method=getInventory';
            parameters += '&type=' + inventoryType;

            return await axios.get(process.env.API + '/api.php?' + parameters)
            .then(response => response.data)
            .catch(error => error);
        }
        catch (e) {
            console.error(e);
        }
    }

    registerSponsor = async (formData) => {
        let data = JSON.parse(formData);

        try {
            
            let params = new URLSearchParams();

            params.append('companyName', data.companyName);
            params.append('contactName', data.contactName);
            params.append('emailAddress', data.emailAddress);
            params.append('contactPhone', data.contactPhone);
            params.append('streetAddress', data.streetAddress);
            params.append('city', data.city);
            params.append('state', data.state);
            params.append('zipcode', data.zipcode);
            params.append('companyUrl', data.companyUrl);
            params.append('services', data.services);
            params.append('sponsorships', JSON.stringify(data.sponsorships));
            params.append('class', 'Sponsor');
            params.append('method', 'registerSponsor');
            
            return await axios.post(process.env.API + '/api.php'
                , params
            )
            .then(response => {

                params.append('lineItems', JSON.stringify(response.data));
                
                return axios.post(process.env.API + '/PayPal-PHP-SDK/SendInvoice.php'
                    , params
                )
                .then(response => {
                    
                    if(response.data) {
                        return '<div class="alert alert-danger m-1" role="alert">There was an error with PayPal. Please contact us at <a href="mailto:treasurer@wsfia.org">treasurer@wsfia.org</a> to finish your registration.</div>';
                    }
                    else {

                        params.append('method', 'updateInventory');

                        axios.post(process.env.API + '/api.php'
                            , params
                        )
                        .then(response => response.data)
                        .catch(error => console.log(error));

                        return '<div class="alert alert-success m-1" role="alert">Thank you for registering as a sponsor for our conference. You should get an invoice from PayPal in a couple minutes for the options you selected below.</div>';
                    }
                    
                })
                .catch(error => console.log(error));
            })
            .catch(error => console.log(error));
            
        }
        catch (e) {
            console.error(e);
        }
    }

    registerVendor = async (formData) => {
        let data = JSON.parse(formData);

        try {
            
            let params = new URLSearchParams();

            params.append('companyName', data.companyName);
            params.append('contactName', data.contactName);
            params.append('emailAddress', data.emailAddress);
            params.append('contactPhone', data.contactPhone);
            params.append('streetAddress', data.streetAddress);
            params.append('city', data.city);
            params.append('state', data.state);
            params.append('zipcode', data.zipcode);
            params.append('companyUrl', data.companyUrl);
            params.append('services', data.services);
            params.append('representativeOne', data.representative1Name);
            params.append('representativeTwo', data.representative2Name);
            params.append('booths', JSON.stringify(data.booths));
            params.append('class', 'Vendor');
            params.append('method', 'registerVendor');
            
            return await axios.post(process.env.API + '/api.php'
                , params
            )
            .then(response => {

                params.append('lineItems', JSON.stringify(response.data));
                
                return axios.post(process.env.API + '/PayPal-PHP-SDK/SendInvoice.php'
                    , params
                )
                .then(response => {
                    
                    if(response.data) {
                        return '<div class="alert alert-danger m-1" role="alert">There was an error with PayPal. Please contact us at <a href="mailto:treasurer@wsfia.org">treasurer@wsfia.org</a> to finish your registration.</div>';
                    }
                    else {
                        return '<div class="alert alert-success m-1" role="alert">Thank you for registering as a vendor for our conference. You should get an invoice from PayPal in a couple minutes for the options you selected below.</div>';
                    }
                    
                })
                .catch(error => console.log(error));
            })
            .catch(error => console.log(error));
            
        }
        catch (e) {
            console.error(e);
        }
    }

    registerSpeaker = async (formData) => {
        let data = JSON.parse(formData);

        try {
            
            let params = new URLSearchParams();

            params.append('fullName', data.fullName);
            params.append('phoneNumber', data.phoneNumber);
            params.append('emailAddress', data.emailAddress);
            params.append('streetAddress', data.streetAddress);
            params.append('city', data.city);
            params.append('stateAbbreviation', data.state);
            params.append('zipcode', data.zipcode);
            params.append('shortBio', data.shortBio);
            params.append('classTitle', data.classTitle);
            params.append('classDescription', data.classDescription);
            params.append('specialEquipment', data.specialEquipment);
            params.append('speakerFee', data.speakerFee);
            params.append('travelExpenses', data.travelExpenses);
            params.append('hotelNights', data.hotelNights);
            params.append('meals', data.meals);
            params.append('miscExpenses', data.miscExpenses);
            params.append('class', 'Speaker');
            params.append('method', 'registerSpeaker');
            
            return await axios.post(process.env.API + '/api.php'
                , params
            )
            .then(response => {
                
                if(response.data <= 0) {
                    return '<div class="alert alert-danger m-1" role="alert">There was an error with PayPal. Please contact us at <a href="mailto:treasurer@wsfia.org">treasurer@wsfia.org</a> to finish your registration.</div>';
                }
                else {
                    return '<div class="alert alert-success m-1" role="alert">Thank you for registering as a speaker for our conference. If you have a presentation you would like to send us please e-Mail it to <a href="mailto:AsstTraining@wsfia.org?subject=Speaker Registration">AsstTraining@wsfia.org</a></div>';
                }

            })
            .catch(error => console.log(error));
            
        }
        catch (e) {
            console.error(e);
        }
    }
    
}

module.exports = Conference;