const express = require('express');
const session = require('express-session');
//const cookie = require('cookie');
const app = express();
app.set('trust proxy', true);
//const bcrypt = require('bcrypt');
const dotenv = require('dotenv');
dotenv.config();
//const uuid = require('uuid/v4');
const { v4: uuidv4 } = require('uuid');
//const fetch = require("node-fetch");

// const bodyParser = require("body-parser");
// app.use(bodyParser.json());
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
const controllers = new Controllers();
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
 * Renders the first registration page.
 */
app.get('/register', (request, response) => {
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
    if(request.session.sessionId === undefined){
        request.session.sessionId = uuidv4();
    }
    response.render('./registration/memberInfo.ejs', { session: request.session, message: '' });
});
/**
 * Calls the addMember method to add registrant to the database, then redirects to a page listing the registrants.
 */
app.post('/register/addMember', async (request, response) => {
    request.body.sessionId = request.session.sessionId;
    let confirm = await controllers.membership.addMember(JSON.stringify(request.body));
    if (confirm) {
        request.session.registrants = await controllers.membership.getRegistrants(request.session.sessionId);
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
app.get('/register/member/registrants', (request, response) => {
    //console.log("Registrants");
    //console.log(request.session.registrants);
    response.render('./registration/registrantsInfo.ejs', { session: request.session, message: '' });
});
/**
 * Calls the registerMember method to add registrants to the database, calls to the PayPal API to create and send an invoice, then redirect to the confirmation page.
 */
app.post('/register/process', async (request, response) => {
    request.body.sessionId = request.session.sessionId;
    request.session.registration = await controllers.membership.registerMember(JSON.stringify(request.body));
    response.redirect('/register/confirm');
});
/**
 * Renders the page for listing all of the members for renewal.
 */
app.get('/renewal/member', async (request, response) => {
    //console.log(request.query.businessId);
    request.session.members = await controllers.membership.getRenewals(JSON.stringify(request.query));
    console.log(request.session.members);
    response.render('./registration/renewal.ejs', { session: request.session, message: '' });
});
/**
 * Calls the registerMember method to add registrants to the database, calls to the PayPal API to create and send an invoice, then redirect to the confirmation page.
 */
app.post('/renewal/process', async (request, response) => {
    console.log(request.session);
    console.log(request.body);
    //request.body.sessionId = request.session.sessionId;
    /*
    request.session.registration = await controllers.membership.renewMember(JSON.stringify(request.body));
    
    console.log(request.session.registration);
    
    response.redirect('/register/confirm');
    */
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
    response.render('login.ejs', { session: request.session });
});
/**
 * Handles the login process and then redirects to the members area page.
 */
app.post('/login', async (request, response) => {
    let userInfo = await controllers.membership.login(JSON.stringify(request.body));
    request.session.userInfo = userInfo;
    response.redirect('/member-area');
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
 * If the user is authenticated then the members area page is rendered.
 */
app.get('/member-area', authenticateUser, (request, response) => {
    response.render('./memberArea.ejs', { session: request.session, message: '' });
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
app.get('/account', authenticateUser, (request, response) => {
    response.render('./account.ejs', { session: request.session, message: '' });
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
const PORT = process.env.PORT || 8080;
app.listen(PORT, () => {
  console.log(`Server listening on port ${PORT}...`);
});
