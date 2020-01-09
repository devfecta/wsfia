class Member {

    constructor() {}

    login = () => {

        console.log('Login');
    }

    /**
     * Searches for departments or businesses in the database, and calls the getBusinesses 
     * function to process the results. 
     * @param {string} searchString Represents the name of the department or business.
     */
    businessSearch = (searchString) => {

        if(searchString.length <= 2) 
        { $('#searchResults').fadeOut(); } 
        else 
        {
            
            $(document).ready(function(){
                $.ajax({
                    type: "GET",
                    url: window.location.href + "/configuration/api.php",
                    data: {
                        'class': 'Business',
                        'method': 'searchBusinessesByName',
                        'searchBusinesses' : searchString
                    },
                    success: getBusinesses,
                    error: displayError
                })
            });
        }
    }
}

module.exports = Member;