//const url = location.protocol + '//' + location.hostname + ':8000';
// wsfia-php
//const url = 'http://34.71.62.246';
//const url = 'http://localhost';

/**
 * Searches for departments or businesses in the database, and calls the getBusinesses 
 * function to process the results. 
 * @param {string} searchString Represents the name of the department or business.
 */
const businessSearch = async (searchString, conference) => {

    if(searchString.length <= 2) 
    { $('#searchResults').fadeOut(); } 
    else 
    {
        // Create the GET query string
        let parameters = 'class=Business';
        parameters += '&method=searchBusinessesByName';
        parameters += '&searchBusinesses=' + searchString;

        await fetch('/businessSearch?' + parameters, {method: 'GET'})
        .then(response => response.json())
        .then(json => {
            getBusinesses(json, conference)
        })
        .catch(error => displayError(error));
    }
}

const checkEmailAddress = async (searchString) => {

    if(searchString.length <= 2) 
    { $('#searchResults').fadeOut(); } 
    else 
    {
        let parameters = 'class=Membership';
        parameters += '&method=checkEmailAddress';
        parameters += '&searchEmailAddress=' + searchString;
        
        await fetch('/checkEmailAddress?' + parameters, {method: 'GET'})
        .then(response => response.json())
        .then(json => {
            if (json.result > 0) {
                document.querySelector('#checkResult').textContent = "e-Mall Address Already Exists";
                validateMembershipForm(document.querySelector('#membershipForm'));
            }
            else {
                document.querySelector('#checkResult').textContent = "";
                validateMembershipForm(document.querySelector('#membershipForm'));
            }
        })
        .catch(error => displayError(error));
    }
}

/**
 * Displays the departments/companies found in a search.
 * @param {*} data 
 */
const getBusinesses = (data, conference) => {

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
            resultButton.addEventListener('click', function(){ getMembers(business.id, conference); });
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
const getMembers = async (businessId, conference) => {

    let parameters = 'class=Member';
        parameters += '&method=getMembersByBusiness';
        parameters += '&businessId=' + businessId;
        
        await fetch('/getMembers?' + parameters, {method: 'GET'})
        .then(response => response.json())
        .then(data => {
            console.log(data);
            // Clears the search textbox
            const searchTextBox = document.querySelector('#searchTextBoxId');
            searchTextBox.value = '';
            // Clears the business search results
            const currentForm = document.querySelector('#searchResultsId');

            if (conference) {
                currentForm.innerHTML = `<p class="text-danger">If you are already a member you will see your account and others 
                associated with the department/business information listed below. Simply leave the checkboxes checked for those 
                members you wisth to register for the conference. If you don't see a member listed, and need to register them as 
                a new member you will be able to do that later in the registration process. Click "Add Registrant" to continue.</p>`;
            }
            else {
                currentForm.innerHTML = `<p class="text-danger">If you are already a member you will see your account information listed below. 
                If you don't see your account listed, click on the "Create New Account" button to register.</p>`;
            }
            
            data.forEach( member => {

                let resultRow = document.createElement("div");
                resultRow.className = 'row';
                resultRow.id = 'memberId' + member.id;

                let resultInfoColumn = document.createElement("div");
                resultInfoColumn.className = 'col-md-8';

                let resultButtonColumn = document.createElement("div");
                resultButtonColumn.className = 'col-md-4 text-center';

                let resultButton = null;
                if (conference) {
                    // Conference Checkboxes
                    resultButton = document.createElement("input");
                    resultButton.setAttribute("class", "form-control w-50");
                    resultButton.setAttribute("type", "checkbox");
                    resultButton.setAttribute("value", "checkbox");
                    resultButton.setAttribute("name", "members[]");
                    resultButton.setAttribute("id", member.user.userId);
                    resultButton.setAttribute("data-value", member.user.userId);
                    resultButton.setAttribute("value", member.user.userId);
                    resultButton.checked = true;
                }
                else {
                    // Non-Conference Buttons
                    resultButton = document.createElement("a");
                    resultButton.className = 'btn btn-success';
                    //resultButton.style = 'cursor: pointer;';
                    let currentDate = new Date();
                    var expirationDate = new Date(member.expirationDate);

                    if(currentDate > expirationDate) {
                        resultButton.href = '/renewal/member?businessId=' + member.departments.id;
                        resultButton.innerHTML = 'Renew Membership';
                    }
                    else {

                        resultButton.href = '/login';
                        resultButton.innerHTML = 'Login';


                        conferenceButton = document.createElement("a");
                        conferenceButton.className = 'btn btn-secondary m-2';

                        conferenceButton.href = '/conference/currentMembers?businessId=' + member.departments.id;
                        conferenceButton.innerHTML = 'Conference Registration';
                        resultButtonColumn.appendChild(conferenceButton);
                    }
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

            let registerButton = null;
            if (conference) {
                registerButton = document.createElement("button");
                registerButton.className = 'btn btn-success';
                registerButton.style = 'cursor: pointer;';
                registerButton.addEventListener('click', function(){ addConferenceRegistrants(document.getElementsByName("members[]")); });
                registerButton.innerHTML = 'Add Registrants';
            }
            else {
                registerButton = document.createElement("a");
                registerButton.className = 'btn btn-primary';
                registerButton.href = '/register/member';
                registerButton.innerHTML = 'Create New Account';
            }

            registerButtonColumn.appendChild(registerButton);
            registerRow.appendChild(registerButtonColumn);

            currentForm.appendChild(registerRow);

        })
        .catch(error => displayError(error));
}

const addConferenceRegistrants = async (members) => {

    let memberIds = [];

    memberIds = Array.from(members).filter(member => {
        if (member.checked) {
            return member;
        }
    })
    .map(member => parseInt(member.value));

    await fetch('/addConferenceRegistrants', {
        method: 'POST', 
        body: JSON.stringify({"memberIds" : memberIds}), 
        headers: { 'Content-Type': 'application/json' },
    })
    .then(response => response)
    .catch(error => displayError(error));
}
/**
 * Gets a list of HTML options for the states.
 */
const buildStatesDropdown = async () => {

    let states = document.querySelector("#states");

    let parameters = 'class=Business';
        parameters += '&method=getStates';

    await fetch('/buildStatesDropdown?' + parameters, {method: 'GET'})
    .then(response => response.json())
    .then(data => {
        
        data.forEach(state => {
            let stateOption = document.createElement("option");
            stateOption.setAttribute('value', state.stateId);
            stateOption.text = state.stateName;
            if (state.stateId == 49) { stateOption.selected = true }
            states.appendChild(stateOption);
        })
    })
    .catch(error => displayError(error));
}
/**
 * Gets a list of businesses to add to a member registration.
 * @param {*} businessId 
 */
const memberBusinessSearch = async (searchString) => {

    const businessList = document.querySelector('#businessList');
    const searchResults = document.querySelector('#searchResults');

    if (searchString.length > 0) {

        let parameters = 'class=Business';
            parameters += '&method=searchBusinessesByName';
            parameters += '&searchBusinesses=' + searchString;

        await fetch('/memberBusinessSearch?' + parameters, {method: 'GET'})
        .then(response => response.json())
        .then(data => {

            const searchResults = document.getElementById('searchResults');
            searchResults.innerHTML = '';
            searchResults.style.display = 'block';

            let results = document.createElement("div");

            if (data.length > 0) {
                data.forEach(business => {
                    let resultRow = document.createElement("div");
                    resultRow.className = 'row';
                    resultRow.id = 'departmentId' + business.id;

                    let resultInfoColumn = document.createElement("div");
                    resultInfoColumn.className = 'col-md-9';
            
                    resultInfoColumn.innerHTML = `
                        <p><strong>${business.name} (Station ${business.station})</strong><br/>
                        ${business.streetAddress}<br/>
                        ${business.city}, ${business.state.abbreviation} ${business.zipcode}</p>
                    `;

                    let resultButtonColumn = document.createElement("div");
                    resultButtonColumn.className = 'col-md-3';

                    let resultButton = document.createElement("button");
                    resultButton.setAttribute('type', 'button');
                    resultButton.setAttribute('class', 'btn btn-primary');
                    resultButton.setAttribute('style', 'cursor: pointer;');
                    //resultButton.addEventListener('click', function(){ addBusiness(business.id) });
                    resultButton.addEventListener('click', function(){ addMemberBusiness(business.id) });
                    resultButton.innerHTML = 'Add Department/Business';

                    resultButtonColumn.appendChild(resultButton);

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
            
        })
        .catch(error => displayError(error));
    }
    else {
        searchResults.innerHTML = '';
    }
}
/**
 * Adds business(s) to a member registration.
 * @param {*} businessId 
 */
const addMemberBusiness = async (businessId) => {

	const businessList = document.querySelector('#businessList');
    const searchResults = document.querySelector('#searchResults');
    
    let parameters = 'class=Business';
        parameters += '&method=Business';
        parameters += '&businessId=' + businessId;

    await fetch('/addMemberBusiness?' + parameters, {method: 'GET'})
    .then(response => response.json())
    .then(data => {

        // console.log(data);

        let businessRow = document.createElement("div");
        businessRow.setAttribute('class', 'form-check');

        let businessInput = document.createElement("input");
        businessInput.setAttribute('type', 'checkbox');
        businessInput.setAttribute('id', 'business['+data.id+']');
        businessInput.setAttribute('data-value', `${data.name} (Station ${data.station})`);
        businessInput.setAttribute('name', 'businesses');
        businessInput.setAttribute('class', 'form-check-input');
        //businessInput.setAttribute('checked', false);
        businessInput.setAttribute('value', data.id);
        businessInput.addEventListener('click', function(){ validateMembershipForm(this.form) });

        let businessInputLabel = document.createElement("label");
        businessInputLabel.setAttribute('for', 'business['+data.id+']');
        businessInputLabel.setAttribute('class', 'form-check-label ml-1 mr-3');
        businessInputLabel.innerHTML = `${data.name} (Station ${data.station})`;

        searchResults.innerHTML = '';

        businessRow.appendChild(businessInput);
        businessRow.appendChild(businessInputLabel);
        businessList.appendChild(businessRow);

    })
    .catch(error => displayError(error));
}
/**
 * Checks to make sure the radio buttons on the pre-registration confirmation page have been selected for billing.
 * @param {*} form 
 */
const validateRegistrationForm = (form) => {
    let radioButtons = form.querySelectorAll('input[type="radio"]');

    let count = 0;
    radioButtons.forEach(radio => {
        if (radio.checked) {
            count++;
        }
    });
    
    if (count > 1) {
        document.querySelector('#registerButton').removeAttribute('disabled');
    }
}
/**
 * Checks to make sure there is an area and department/company selected on the Membership Registration page.
 * @param {*} form 
 */
const validateMembershipForm = (form) => {

    let areaCheckboxes = form.querySelectorAll('input[name="areas"]');

    let businessCheckboxes = form.querySelectorAll('input[name="businesses"]');
    
    let BreakException = {areas : false, businesses : false};

    try {
        areaCheckboxes.forEach(area => {
            if (area.checked) {
                throw BreakException;
            }
        });
    }
    catch(e) {
        BreakException.areas = true;
    }

    try {
        businessCheckboxes.forEach(business => {
            if (business.checked) {
                throw BreakException;
            }
        });
    }
    catch(e) {
        BreakException.businesses = true;
    }

    

    if (document.querySelector('#addMemberButton')) {
        
        if (BreakException.areas && BreakException.businesses && document.querySelector('#checkResult').textContent.length < 1) {
            document.querySelector('#addMemberButton').removeAttribute('disabled');
        }
        else {
            document.querySelector('#addMemberButton').disabled = true;
        }
    }
    else {
        if (BreakException.areas && BreakException.businesses) {
            document.querySelector('#updateButton').removeAttribute('disabled');
        }
        else {
            document.querySelector('#updateButton').disabled = true;
        }
    }

}
/**
 * 
 * @param {*} sessionId 
 * @param {*} emailAddress
 * @param {*} attendingDate 
 */
 const setAttendingDate = (sessionId, emailAddress, attendingDate, checked) => {

    console.log(sessionId, emailAddress, attendingDate, checked);
    
    fetch('/setAttendingDate', {
        method: 'POST', 
        body: JSON.stringify({"sessionId": sessionId, "emailAddress": emailAddress, "attendingDate": attendingDate, "attendingChecked": checked}), 
        headers: { 'Content-Type': 'application/json' },
    })
    .then(response => response)
    .catch(error => displayError(error));

}
/**
 * Removes a member from the conference attendee registration form.
 * @param {*} id 
 * @param {*} emailAddress 
 */
 const removeRegistrant = (id, emailAddress) => {

    document.querySelectorAll(".userRow" + id).forEach(element => element.remove());

    let parameters = 'class=Membership&method=removeRegistrant&emailAddress=' + emailAddress;
    
    fetch('/removeRegistrant?' + parameters, {method: 'GET'})
    .then(response => {
        window.location = '/register/member/registrants';
    })
    .catch(error => displayError(error));
    
}
/**
 * Remove a member from the membership renewal form.
 * @param {*} id 
 */
const removeRenewal = (id) => {
    let lineItem = document.querySelector("#userRow" + id);
    lineItem.remove();
    lineItem = document.querySelector("#emailAddressRow" + id);
    lineItem.remove();
    
}
/**
 * Remove a member from the conference attendee registration form.
 * @param {*} id 
 */
const removeAttendee = (id) => {
    let lineItem = document.querySelector("#userRow" + id);
    lineItem.remove();
}

/**
 * Displays the request error from the API call.
 * @param {*} error 
 */
const displayError = (error) => {
    document.getElementsByTagName('main')[0].innerHTML = error;
}
