const axios = require("axios").default;

class Conference {

    constructor() {}

    addConferenceRegistrants = async (data) => {

        let memberData = JSON.parse(data);
        //console.log(memberData);
        let confirmation = false;

        try {
            
            let params = new URLSearchParams();

            params.append('sessionId', memberData.sessionId);
            params.append('memberIds', memberData.memberIds);
            params.append('class', 'Membership');
            params.append('method', 'addConferenceRegistrants');
            //console.log(params);

            return await axios.post(process.env.API + '/api.php'
                , params
            )
            .then(response => {
                const lineItems = response.data;

                console.log(response.data);
            })
            .catch(error => console.log(error));
            
            return confirmation;
            
        }
        catch (e) {
            console.error(e);
        }
    }

}

module.exports = Conference;