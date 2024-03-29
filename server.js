const https = require("https");
const http = require("http");
const fs = require("fs");
const helmet = require("helmet");
const express = require('express');
const session = require('express-session');

const bodyParser = require('body-parser');
//const cookie = require('cookie');
const app = express();

app.use(bodyParser.urlencoded({ extended: false }));
app.use(bodyParser.json());

app.use(helmet());
app.set('trust proxy', true);
//const bcrypt = require('bcrypt');
const dotenv = require('dotenv');
dotenv.config();
//const uuid = require('uuid/v4');
const { v4: uuidv4 } = require('uuid');
/**
 * Creates a new session ID.
 */
app.use(session({secret: uuidv4(), saveUninitialized: true, resave: true}));
/**
 * Allows for the serving of static files in the public directory.
 */
app.use('/', express.static('public'));

//app.use(express.urlencoded());
/**
 * Calls the script that instantiates all of the controllers.
 */
const Controllers = require('./controllers');
const { pathToFileURL } = require("url");
const { request } = require("express");
const controllers = new Controllers();
/**
 * Sets start and end dates for conference registration.
 */
 const startDateConference = Date.parse('2021-05-01');
 const endDateConference = Date.parse('2021-10-30');
/**
 * EJS templating library.
 */
app.set('view-engine', 'ejs');
/**
 * Creates the ablity to use the request parameter to pass form elements.
 */
app.use(express.urlencoded({extended: false}));
/**
 * Renders the homepage.
 */
app.get('/', (request, response) => {
    response.render('index.ejs', { session: request.session });
});

/**
 * Utilities START
 */
app.get('/getRegistrantCount', async (request, response) => {
    request.session.sessionId = request.sessionID;
    //console.log(request._parsedOriginalUrl.query + "&sessionId=" + request.session.sessionId);
    let results = await controllers.utilities.getRegistrantCount(request._parsedOriginalUrl.query + "&sessionId=" + request.session.sessionId);
    request.session.showRegistrationsButton = (results > 0) ? true : false;
    //console.log(results);
    response.end();
    //response.json(results);
});

app.get('/businessSearch', async (request, response) => {
    //console.log(request._parsedOriginalUrl.query);
    let results = await controllers.utilities.businessSearch(request._parsedOriginalUrl.query);
    response.json(results);
});

app.get('/checkEmailAddress', async (request, response) => {
    let results = await controllers.utilities.checkEmailAddress(request._parsedOriginalUrl.query);
    response.json(results);
});

app.get('/getMembers', async (request, response) => {
    let results = await controllers.utilities.getMembers(request._parsedOriginalUrl.query);
    response.json(results);
});

app.get('/buildStatesDropdown', async (request, response) => {
    let results = await controllers.utilities.buildStatesDropdown(request._parsedOriginalUrl.query);
    response.json(results);
});

app.get('/memberBusinessSearch', async (request, response) => {
    let results = await controllers.utilities.memberBusinessSearch(request._parsedOriginalUrl.query);
    response.json(results);
});

app.get('/addMemberBusiness', async (request, response) => {
    let results = await controllers.utilities.addMemberBusiness(request._parsedOriginalUrl.query);
    response.json(results);
});

app.get('/removeRegistrant', async (request, response) => {
    //console.log("Session: " +  request.session.sessionId);
    let results = await controllers.utilities.removeRegistrant(request._parsedOriginalUrl.query);
    request.session.registrants = await controllers.membership.getRegistrants(request.session.sessionId);
    
    //request.session.registrants = request.session.registrants.filter(registrant => registrant.emailAddress != request.query.emailAddress);    
    response.json(results);
});

/**
 * Utilities END
 */

/**
 * Renders the first registration page.
 */
app.get('/register', (request, response) => {
    request.session.conference = (Date.now() >= startDateConference && Date.now() <= endDateConference) ? true : false;
    response.render('./registration/businessSearch.ejs', { session: request.session });
});
/**
 * Renders the page for adding a business.
 */
app.get('/register/business', (request, response) => {
    response.render('./registration/businessInfo.ejs', { session: request.session, message: '' });
});
/**
 * Calls the addBusiness method to add a business/department to the database, then redirects back to the start of the registration process.
 */
app.post('/addBusiness', async (request, response) => {
    request.session.confirm = await controllers.business.addBusiness(JSON.stringify(request.body));
    if (request.session.confirm) {
        response.redirect('/register');
    }
    else {
        response.render('./registration/businessInfo.ejs', { session: request.session, message: '<div class="alert alert-danger m-1" role="alert">There was an error when trying to add the business.</div>' });
    }
});
/**
 * Renders the page for adding a registrant.
 */
app.get('/register/member', (request, response) => {
    /*
    if(request.session.sessionId === undefined){
        request.session.sessionId = uuidv4();
    }
    */
    response.render('./registration/memberInfo.ejs', { session: request.session, message: '' });
});
/**
 * Calls the addMember method to add registrant to the database, then redirects to a page listing the registrants.
 */
app.post('/register/addMember', async (request, response) => {
    request.body.sessionId = request.session.sessionId;
    let confirm = await controllers.membership.addMember(JSON.stringify(request.body));
    if (confirm) {
        //console.log(request.session.registrants);
        response.redirect('/register/member/registrants');
    }
    else {
        response.render('./registration/memberInfo.ejs', { session: request.session, message: '<div class="alert alert-danger m-1" role="alert">There was an error when trying to add the member.</div>' });
    }    
});
/**
 * Renders the page for listing all of the current registrants.
 */
app.get('/register/member/registrants', async (request, response) => {
    //console.log("Registrants");
    request.session.registrants = await controllers.membership.getRegistrants(request.session.sessionId);
    //console.log(request.session.registrants);
    //console.log(request.session);
    response.render('./registration/registrantsInfo.ejs', { session: request.session, message: '' });
});
/**
 * Updates the user session data by setting the conference attending date for a specific registrant.
 */
app.post('/setAttendingDate', async (request, response) => {
    //console.log(request.body);
    let confirm = await controllers.conference.setAttendingDate(JSON.stringify(request.body));
    response.end();
});
/**
 * 
 */
 app.post('/setCEU', async (request, response) => {
    //console.log(request.body);
    let confirm = await controllers.conference.setCEU(JSON.stringify(request.body));
    response.end();
});
/**
 * 
 */
 app.post('/setLicenseType', async (request, response) => {
    //console.log(request.body);
    let confirm = await controllers.conference.setLicenseType(JSON.stringify(request.body));
    response.end();
});
/**
 * 
 */
 app.post('/setLicenseNumber', async (request, response) => {
    //console.log(request.body);
    let confirm = await controllers.conference.setLicenseNumber(JSON.stringify(request.body));
    response.end();
});
/**
 * 
 */
 app.post('/setGuestName', async (request, response) => {
    let confirm = await controllers.conference.setGuestName(JSON.stringify(request.body));
    response.end();
});
/**
 * 
 */
 app.post('/setBanquet', async (request, response) => {
    let confirm = await controllers.conference.setBanquet(JSON.stringify(request.body));
    response.end();
});
/**
 * 
 */
 app.post('/setBanquetGuest', async (request, response) => {
    let confirm = await controllers.conference.setBanquetGuest(JSON.stringify(request.body));
    response.end();
});
/**
 * 
 */
 app.post('/setVendorNight', async (request, response) => {
    let confirm = await controllers.conference.setVendorNight(JSON.stringify(request.body));
    response.end();
});
/**
 * 
 */
 app.post('/setVendorNightGuest', async (request, response) => {
    let confirm = await controllers.conference.setVendorNightGuest(JSON.stringify(request.body));
    response.end();
});
/**
 * 
 */
 app.post('/setVegetarianMeal', async (request, response) => {
    let confirm = await controllers.conference.setVegetarianMeal(JSON.stringify(request.body));
    response.end();
});
/**
 * 
 */
 app.post('/setVegetarianMealGuest', async (request, response) => {
    let confirm = await controllers.conference.setVegetarianMealGuest(JSON.stringify(request.body));
    response.end();
});

/**
 * Calls the registerMember method to add registrants to the database, calls to the PayPal API to create and send an invoice, then redirect to the confirmation page.
 */
app.post('/register/process', async (request, response) => {

    request.body.sessionId = request.session.sessionId;
    //console.log(request.body);
    // Remove unnessecary properties because they're in the database.
    delete request.body.attendingDates;
    delete request.body.ceu;
    delete request.body.licenseType;
    delete request.body.licenseNumber;
    delete request.body.banquet;
    delete request.body.vendorNight;
    delete request.body.guestName;
    //console.log(request.body);
    let result = await controllers.membership.registerMember(JSON.stringify(request.body));
    //console.log("Registration Session");
    //console.log(request.session.registration);

    request.session.errorMessage = "";

    
    if (result) {
        request.session.registration = result;
        response.redirect('/register/confirm');
    }
    else {
        //console.log(result);
        //console.log("PayPal Error");
        request.session.errorMessage = 'There was an error with PayPal. Please contact us at <a href="mailto:treasurer@wsfia.org">treasurer@wsfia.org</a> to finish your registration.';
        response.redirect('/register/member/registrants');
    }
});
/**
 * Renders the page for listing all of the members for renewal.
 */
app.get('/renewal/member', async (request, response) => {
    //console.log(request.query.businessId);
    request.session.members = await controllers.membership.getRenewals(JSON.stringify(request.query));
    //console.log(JSON.stringify(request.session.members.departments));
    //console.log(request.session.members);
    response.render('./registration/renewal.ejs', { session: request.session, message: '' });
});
/**
 * Calls the registerMember method to add registrants to the database, calls to the PayPal API to create and send an invoice, then redirect to the confirmation page.
 */
app.post('/renewal/process', async (request, response) => {
    //console.log(request.session);
    console.log(request.body);
    request.body.sessionId = request.session.sessionId;
    request.session.registration = await controllers.membership.renewMember(JSON.stringify(request.body));
    response.redirect('/register/confirm');
    //request.end();
});
/**
 * Renders the confirmation of registrants, after the registration(s) have been processed.
 */
app.get('/register/confirm', (request, response) => {
    //request.session.test = 'testing';
    //console.log(request.session.registration);
    response.render('./registration/confirm.ejs', { session: request.session, message: '' });
});
/**
 * Renders the login page.
 */
app.get('/login', (request, response) => {
    request.session.message = "";
    response.render('login.ejs', { session: request.session });
});
/**
 * Handles the login process and then redirects to the members area page.
 */
app.post('/login', async (request, response) => {
    let userInfo = await controllers.membership.login(JSON.stringify(request.body));
    //console.log(userInfo);
    if (userInfo.authenticated) {
        request.session.userInfo = userInfo;
        response.redirect('/member-area');
    }
    else {
        request.session.message = "Invalid Username/Password<br/>If you know you're a member try looking up your account via the <a href=\"/register\">registration page</a>.";
        response.render('login.ejs', { session: request.session });
    }
});
/**
 * Checks to see if the user has benn authenticated, if not redirected to the login page.
 */
function authenticateUser (request, response, next) {
    if ((request.session.userInfo) && request.session.userInfo.authenticated) {
        return next();
    }
    response.redirect('/login');
}
/**
 * Renders the Forgot Password page.
 */
app.get('/resetPassword', (request, response) => {
    response.render('./resetPassword.ejs', { session: request.session, message: null });
});
/**
 * Renders the Forgot Password page with password update confirmation.
 */
app.post('/resetPassword', async (request, response) => {
    let resultJSON = await controllers.membership.resetPassword(JSON.stringify(request.body));
    //console.log(resultJSON);
    response.render('./resetPassword.ejs', { session: request.session, message: resultJSON.updatedPassword });
});
/**
 * If the user is authenticated then the members area page is rendered.
 */
app.get('/member-area', authenticateUser, (request, response) => {
    response.render('./memberArea.ejs', { session: request.session, message: '' });
});
/**
 * Gets the member card PDF.
 */
 app.get('/member-card', authenticateUser, async (request, response) => {
    //console.log(request.session.userInfo.wsfiaId);
    //let resultJSON = await controllers.membership.getMembershipCard(request.session.userInfo.wsfiaId);

    response.redirect(process.env.API + '/api.php?class=Membership&method=getMembershipCard&memberId=' + request.session.userInfo.wsfiaId);

});
/**
 * Logs out the user by removing the userInfo property from the session, then redirects to the login page.
 */
app.get('/logout', (request, response) => {
    delete request.session.userInfo
    response.redirect('/login');
});
/**
 * If the user is authenticated then renders the user account information form.
 */
app.get('/account', authenticateUser, async (request, response) => {
    let accountInfo = await controllers.membership.getAccountInfo(request.session.userInfo.wsfiaId);
    request.session.accountInfo = accountInfo;
    response.render('./account.ejs', { session: request.session, message: request.session.message });
});
/**
 * If the user is authenticated then processes the update of the account information.
 */
app.post('/account', authenticateUser, async (request, response) => {
    request.body.userId = request.session.accountInfo.userId;
    let resultJSON = await controllers.membership.updateAccountInfo(JSON.stringify(request.body));
    request.session.message = resultJSON.updatedAccount;
    response.redirect('/account');
});
/**
 * If the user is authenticated then renders a listing of all the members.
 */
 app.get('/member-list', authenticateUser, async (request, response) => {
    let members = await controllers.membership.getMembers();
    request.session.members = members;
    response.render('./membersList.ejs', { session: request.session, message: '' });
});
/**
 * If the user is authenticated then renders the documents page.
 */
app.get('/documents', authenticateUser, (request, response) => {
    response.render('./documents.ejs', { session: request.session, message: '' });
});
/**
 * Static Pages
 */
/**
 * Renders the area map page.
 */
app.get('/area-map', (request, response) => {
    response.render('./areaMap.ejs', { session: request.session, message: '' });
});
/**
 * Renders the awards page.
 */
app.get('/awards', (request, response) => {
    response.render('./awards.ejs', { session: request.session, message: '' });
});
/**
 * Renders the WSFIA calendar page.
 */
app.get('/calendar', (request, response) => {
    response.render('./calendar.ejs', { session: request.session, message: '' });
});
/**
 * Renders the contact page with the executive board members.
 */
app.get('/contact', (request, response) => {
    response.render('./contact.ejs', { session: request.session, message: '' });
});
/**
 * Renders the job postings page.
 */
app.get('/job-postings', (request, response) => {
    response.render('./jobPostings.ejs', { session: request.session, message: '' });
});
/**
 * Renders the link page.
 */
app.get('/links', (request, response) => {
    response.render('./links.ejs', { session: request.session, message: '' });
});
/**
 * Renders the scholarships page.
 */
app.get('/scholarships', (request, response) => {
    response.render('./scholarships.ejs', { session: request.session, message: '' });
});
// REPORTS START
/**
 * Downloads an Excel file of the members.
 */
app.get('/reports/members', async (request, response) => {

    response.redirect("https://api.wsfia.org/api.php?class=Membership&method=exportMemberInfo");
    
});
// REPORTS END
// CONFERENCE START
/**
 * Renders the conference information page.
 */
app.get('/conference', (request, response) => {
    response.render('./conference.ejs', { session: request.session, message: '' });
});
/**
 * Renders the conference registration page for current members.
 */
 app.get('/conference/currentMembers', async (request, response) => {
    //request.session.sessionId = uuidv4();
    if(request.session.sessionId === undefined){
        request.session.sessionId = uuidv4();
    }
    // Reusing the getRenewals method just to get current members.
    request.session.members = await controllers.membership.getRenewals(JSON.stringify(request.query));

    request.session.businessId = request.query.businessId;
    response.render('./registration/attendeeCurrentMembers.ejs', { session: request.session, message: '' });
});
/**
 * Adds selected current member(s) to the registration session data.
 */
 app.post('/conference/currentMembers/process', async (request, response) => {
    request.body.sessionId = request.session.sessionId;
    //console.log(JSON.stringify(request.body));
    let confirm = await controllers.conference.addConferenceCurrentMembers(JSON.stringify(request.body));

    let registrationMessage = '';
    if (request.session.conference) {
        registrationMessage = '<div class="alert alert-danger m-1" role="alert">If you have any new member(s), you can register them now. Otherwise, click "Next".</div>';
    }

    if (confirm) {
        request.session.registrants = await controllers.membership.getRegistrants(request.session.sessionId);
        response.render('./registration/memberInfo.ejs', { session: request.session, message: registrationMessage });
        //console.log(request.session.registrants);
        //response.redirect('/register/member');
    }
    else {
        response.render('./registration/attendeeCurrentMembers.ejs', { session: request.session, message: '<div class="alert alert-danger m-1" role="alert">There was an error when trying to process the member(s).</div>' });
    }
    
});


/**
 * Renders the conference register current members page.
 */
app.get('/conference/register', (request, response) => {
    request.session.conference = (Date.now() >= startDateConference && Date.now() <= endDateConference) ? true : false;
    response.render('./registration/businessSearch.ejs', { session: request.session });
});

app.post('/addConferenceRegistrants', async (request, response) => {
    if(request.session.sessionId === undefined){
        request.session.sessionId = uuidv4();
    }
    request.body.sessionId = request.session.sessionId;
    //console.log(request.body);
    let results = await controllers.conference.addConferenceRegistrants(JSON.stringify(request.body));
    //response.json(results);
});
// SPONSOR START
/**
 * Renders the conference sponsor registration form.
 */
 app.get('/conference/sponsor', async (request, response) => {
    request.session.inventory = await controllers.conference.getInventory("Sponsor");
    response.render('./registration/sponsorInfo.ejs', { session: request.session, message: '' });
});
/**
 * Calls the registerSponsor method to add the sponsor to the database, 
 * calls to the PayPal API to create and send an invoice, 
 * then redirects back to the registration page with a confirmation message.
 */
 app.post('/conference/sponsor/register', async (request, response) => {
    let formData = Object.assign({}, request.body);
    let result = await controllers.conference.registerSponsor(JSON.stringify(formData));
    request.session.message = result;
    response.redirect('/conference/sponsor');
});
// SPONSOR END
// VENDOR START
/**
 * Renders the conference vendor registration form.
 */
 app.get('/conference/vendor', async (request, response) => {
    request.session.inventory = await controllers.conference.getInventory("Vendor");
    response.render('./registration/vendorInfo.ejs', { session: request.session, message: '' });
});
/**
 * Calls the registerVendor method to add the vendor to the database, 
 * calls to the PayPal API to create and send an invoice, 
 * then redirects back to the registration page with a confirmation message.
 */
 app.post('/conference/vendor/register', async (request, response) => {
    let formData = Object.assign({}, request.body);
    let result = await controllers.conference.registerVendor(JSON.stringify(formData));
    request.session.message = result;
    response.redirect('/conference/vendor');
});
// VENDOR END
// SPEAKER START
/**
 * Renders the conference speaker registration information.
 */
 app.get('/conference/speaker', (request, response) => {
    response.render('./registration/speakerInfo.ejs', { session: request.session });
});


app.post('/conference/speaker/register', async (request, response) => {
    let formData = Object.assign({}, request.body);
    let result = await controllers.conference.registerSpeaker(JSON.stringify(formData));
    request.session.message = result;
    response.redirect('/conference/speaker');
});
// SPEAKER END
// CONFERENCE END
/*
app.post('/register', async (request, response) => {

    try {
        const hashedPassword = await bcrypt.hash(request.body.password, 10);
        request.body.email

        response.render('./registration/memberInfo.ejs', { session: request.session });
        
    }
    catch {}

    response.render('register.ejs', { session: request.session, view: ''});

});
*/
//const PORT = process.env.PORT || 8080;
app.listen(process.env.PORT, () => {
  console.log(`Server listening on port ${process.env.PORT}...`);
});

// Uncomment for Production
/*
const options = {
    key: fs.readFileSync("/etc/letsencrypt/live/wsfia.org/privkey.pem"),
    cert: fs.readFileSync("/etc/letsencrypt/live/wsfia.org/fullchain.pem")
};
https.createServer(options, app).listen(process.env.HTTPS);
*/