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