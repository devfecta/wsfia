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

    setAttendingDate = async (data) => {

        let memberData = JSON.parse(data);

        //console.log(memberData.sessionId);

        try {
            
            let params = new URLSearchParams();

            params.append('sessionId', memberData.sessionId);
            params.append('emailAddress', memberData.emailAddress);
            params.append('attendingDate', memberData.attendingDate);
            params.append('attendingChecked', memberData.attendingChecked);
            params.append('class', 'RegisterConferenceMember');
            params.append('method', 'setAttendingDate');
            
            await axios.post(process.env.API + '/api.php'
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