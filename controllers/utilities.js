const axios = require("axios").default;

class Utilities {

    getApi = async (queryString) => {
        try {
            return await axios.get(process.env.API + '/api.php?' + queryString)
            .then(response => response.data)
            .catch(error => error);
        }
        catch (e) {
            console.error(e);
        }
    }

    /**
     * Searches for departments or businesses in the database, and returns JSON of the search results. 
     * @param {string} queryString The parameters for the API.
     * @returns {json} JSON of the search results.
     */
    businessSearch = (queryString) => {
        return this.getApi(queryString);
    }
    /**
     * Checks to see if an e-mail address exists.
     * @param {*} queryString 
     * @returns {json} JSON of the results.
     */
    checkEmailAddress = (queryString) => {
        return this.getApi(queryString);
    }
    /**
     * Displays members of a department/company.
     * @param {*} queryString 
     * @returns {json} JSON of the results.
     */
    getMembers = (queryString) => {
        return this.getApi(queryString);
    }
    /**
     * Gets a list of HTML options for the states.
     * @param {*} queryString
     * @returns {json} JSON of the results.
     */
    buildStatesDropdown = (queryString) => {
        return this.getApi(queryString);
    }
    /**
     * Gets a list of businesses to add to a member registration.
     * @param {*} queryString
     * @returns {json} JSON of the results.
     */
    memberBusinessSearch = (queryString) => {
        return this.getApi(queryString);
    }
    /**
     * Adds business(s) to a member registration.
     * @param {*} queryString
     * @returns {json} JSON of the results.
     */
    addMemberBusiness = (queryString) => {
        return this.getApi(queryString);
    }


    /**
     * Displays the request error from the API call.
     * @param {*} error 
     */
    displayError = (error) => {
        document.getElementsByTagName('main')[0].innerHTML = error;
    }

}

module.exports = Utilities;