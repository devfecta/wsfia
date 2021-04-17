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
            .then(response => console.log(response.data))
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

}

module.exports = Conference;