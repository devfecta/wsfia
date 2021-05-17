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
            .then(response => console.log(response.data))
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
            .then(response => console.log(response.data))
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
            .then(response => console.log(response.data))
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
        //console.log(memberData);
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
            .then(response => console.log(response.data))
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
        //console.log(memberData);
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
            .then(response => console.log(response.data))
            .catch(error => console.log(error));
        }
        catch (e) {
            console.error(e);
        }
    }
    /**
     * Sets the guest name field for the attendee.
     * @param {string} data 
     */
     setGuestName = (data) => {

        let memberData = JSON.parse(data);

        //console.log(memberData);

        try {
            
            let params = new URLSearchParams();

            params.append('sessionId', memberData.sessionId);
            params.append('emailAddress', memberData.emailAddress);
            params.append('guestName', memberData.guestName);
            params.append('class', 'RegisterConferenceMember');
            params.append('method', 'setGuestName');
            
            axios.post(process.env.API + '/api.php'
                , params
            )
            .then(response => console.log(response.data))
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

        console.log(data);



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
            params.append('sponsorships', data.sponsorships);
            params.append('class', 'Sponsor');
            params.append('method', 'registerSponsor');
            
            axios.post(process.env.API + '/api.php'
                , params
            )
            .then(response => console.log(response.data))
            .catch(error => console.log(error));
            
        }
        catch (e) {
            console.error(e);
        }
    }
    
}

module.exports = Conference;