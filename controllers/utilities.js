class Utilities {

    /**
     * Searches for departments or businesses in the database, and calls the getBusinesses 
     * function to process the results. 
     * @param {string} searchString Represents the name of the department or business.
     */
    businessSearch = async (searchString) => {

        if(searchString.length <= 2) 
        { $('#searchResults').fadeOut(); } 
        else 
        {
            //console.log(searchString);
            // await fetch('http://localhost/wsfia-dev/configuration/api.php?class=Business&method=searchBusinessesByName&searchBusinesses='+searchString, {method: 'POST', headers: {'Content-Type': 'text/json'}});
            let parameters = 'class=Business';
            parameters += '&method=searchBusinessesByName';
            parameters += '&searchBusinesses=' + searchString;
            
            await fetch('http://localhost/wsfia-dev/configuration/api.php?' + parameters, {method: 'GET'})
            .then(response => response.json())
            .then(json => {
                console.log(json)
                getBusinesses(json)
            })
            .catch(error => displayError(error));
        }
    }

    /**
     * Displays the departments/companies found in a search.
     * @param {*} data 
     */
    getBusinesses = (data) => {

        const searchResults = document.getElementById('searchResultsId');
        searchResults.innerHTML = `
            <p class="text-danger">If you don't see your department/business, click on the "Create Department/Business" button 
            to add your department/business to our system.</p>
        `;

        let results = document.createElement("div");
        //console.log(data);
        if (data.length > 0) {

            searchResults.innerHTML += `
                <p class="text-danger">Click on the "View Members" button to view all of the members of associated with the department/business.</p>
            `;

            data.forEach(business => {

                //document.cookie = 'businessId=' + business.id;
                //console.log(document.cookie);

                let resultRow = document.createElement("div");
                resultRow.className = 'row';
                resultRow.id = 'departmentId' + business.id;

                let resultInfoColumn = document.createElement("div");
                resultInfoColumn.className = 'col-md-8';
        
                resultInfoColumn.innerHTML = `
                    <p><strong>${business.name} (Station ${business.station})</strong><br/>
                    ${business.streetAddress}<br/>
                    ${business.city}, ${business.state.abbreviation} ${business.zipcode}</p>
                `;

                let resultButtonColumn = document.createElement("div");
                resultButtonColumn.className = 'col-md-4 d-flex justify-content-around align-items-center';

                let resultButton = document.createElement("button");
                resultButton.className = 'btn btn-success m-1';
                resultButton.style = 'cursor: pointer;';
                resultButton.addEventListener('click', function(){ getMembers(business.id); });
                resultButton.innerHTML = 'View Members';
                resultButtonColumn.appendChild(resultButton);
                /*
                let backButton = document.createElement("button");
                backButton.className = 'btn btn-secondary';
                backButton.style = 'cursor: pointer;';
                backButton.addEventListener('click', goBack);
                backButton.innerHTML = 'Go Back';
                resultButtonColumn.appendChild(backButton);
                */

                resultRow.appendChild(resultInfoColumn);
                resultRow.appendChild(resultButtonColumn);

                results.appendChild(resultRow);

            });
        }
        else {
            
            results.innerHTML = `
                <p>No Results Found <a href="/register/business" class="btn btn-primary">Create Department/Business</a></p>
            `;
        }

        searchResults.appendChild(results);
        
    }

    /**
     * Displays members of a department/company.
     * @param {*} businessId 
     */
    getMembers = async (businessId) => {

        let parameters = 'class=Member';
            parameters += '&method=getMembersByBusiness';
            parameters += '&businessId=' + businessId;
            
            await fetch('http://localhost/wsfia-dev/configuration/api.php?' + parameters, {method: 'GET'})
            .then(response => response.json())
            .then(data => {
                console.log(data)
                // Clears the search textbox
                const searchTextBox = document.querySelector('#searchTextBoxId');
                searchTextBox.value = '';
                // Clears the business search results
                const currentForm = document.querySelector('#searchResultsId');
                currentForm.innerHTML = `<p class="text-danger">If you are already a member you will see your account information listed below. 
                    If you don't see your account listed, click on the "Create New Account" button to register.</p>`;

                data.forEach( member => {

                    let resultRow = document.createElement("div");
                    resultRow.className = 'row';
                    resultRow.id = 'memberId' + member.id;

                    let resultInfoColumn = document.createElement("div");
                    resultInfoColumn.className = 'col-md-9';

                    let resultButtonColumn = document.createElement("div");
                    resultButtonColumn.className = 'col-md-3';

                    let resultButton = document.createElement("a");
                    resultButton.className = 'btn btn-success';
                    //resultButton.style = 'cursor: pointer;';
                    resultButton.href = '/login';

                    let currentDate = new Date();
                    var expirationDate = new Date(member.expirationDate);

                    if(currentDate > expirationDate) {
                        //resultButton.addEventListener('click', function(){ loadView('login'); });
                        resultButton.innerHTML = 'Renew Membership';
                    }
                    else {
                        //resultButton.addEventListener('click', function(){ loadView('login'); });
                        resultButton.innerHTML = 'Login';
                    }

                    resultButtonColumn.appendChild(resultButton);
                    

                    if(member.studentId === null) {
                        member.studentId = 'N/A';
                    }
                    resultInfoColumn.innerHTML = `
                        <p><strong>${member.user.firstName} ${member.user.lastName} (${member.jobTitle})</strong><br/>
                        ${member.user.emailAddress}<br/>
                        Expires On: ${member.expirationDate}, ${member.status.status}<br/>
                        Student ID: ${member.studentId}</p>
                    `;

                    resultRow.appendChild(resultInfoColumn);
                    resultRow.appendChild(resultButtonColumn);

                    currentForm.appendChild(resultRow);
                    

                });
                
                let registerRow = document.createElement("div");
                registerRow.className = 'row';

                let registerButtonColumn = document.createElement("div");
                registerButtonColumn.className = 'col-md-12 text-center';

                let registerButton = document.createElement("a");
                registerButton.className = 'btn btn-primary';
                registerButton.href = '/register/member';
                registerButton.innerHTML = 'Create New Account';

                registerButtonColumn.appendChild(registerButton);
                registerRow.appendChild(registerButtonColumn);

                currentForm.appendChild(registerRow);

            })
            .catch(error => displayError(error));
    }

    buildStatesDropdown = async () => {

        let parameters = 'class=Business';
            parameters += '&method=getStates';

        await fetch('http://localhost/wsfia-dev/configuration/api.php?' + parameters, {method: 'GET'})
        .then(response => response.json())
        .then(data => {
            const statesDropdown = document.querySelector('#states');

                data.forEach( state => {

                    let stateOption = document.createElement('option');
                    stateOption.setAttribute('value', state.stateId);
                    if(state.stateId == 49) {
                        stateOption.setAttribute('selected', true);
                    }
                    stateOption.innerHTML = state.stateName;
                    statesDropdown.appendChild(stateOption);
                    
                });
        })
        .catch(error => displayError(error));

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