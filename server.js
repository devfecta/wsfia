const express = require('express');
const session = require('express-session');
const cookie = require('cookie');
const app = express();
const bcrypt = require('bcrypt');
const dotenv = require('dotenv');
dotenv.config();
const uuid = require('uuid/v4');
const fetch = require("node-fetch");

// const bodyParser = require("body-parser");
// app.use(bodyParser.json());
/**
 * Creates a new session ID.
 */
app.use(session({secret: uuid(), saveUninitialized: true, resave: true}));
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
 * Renders the login page.
 */
app.get('/login', (request, response) => {
    //console.log(request.session);
    response.render('login.ejs', { session: request.session });
    // controllers.member.login();
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
        request.session.sessionId = uuid();
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
    //console.log("Processed");
    //console.log(request.session.registration);
    response.redirect('/register/confirm');

    /*
    request.body.sessionId = request.session.sessionId;
    let confirm = await controllers.membership.addMember(JSON.stringify(request.body));
    //console.log(request.session.confirm);
    if (confirm) {
        request.session.registrants = await controllers.membership.getRegistrants(request.session.sessionId);
        //console.log(request.session.registrants);
        response.redirect('/register/member/registrants');
    }
    else {
        response.render('./registration/memberInfo.ejs', { session: request.session, message: '<div class="alert alert-danger m-1" role="alert">There was an error when trying to add the member.</div>' });
    }
    */
});

app.get('/register/confirm', (request, response) => {
    //request.session.test = 'testing';
    console.log("Confirmation");
    //console.log(request.session.registration);
    response.render('./registration/confirm.ejs', { session: request.session, message: '' });
});



/*
{
  lineItems: [
    {
      emailAddress: 'testing@wsfia.org',
      userId: '105',
      quantity: 1,
      itemId: '1',
      itemName: 'WSFIA Membership',
      itemDescription: 'Member Name: FirstName LastName\nMember ID: WSFIA-10520311',
      price: '40.00'
    }
  ],
  billing: {
    billingEmailAddress: 'testing@wsfia.org',
    billingBusiness: {
      id: '2',
      name: 'Verona Fire',
      station: '4',
      streetAddress: '456 Test Circle',
      city: 'Verona',
      state: '49',
      zipcode: '53700',
      phone: '(608) 456-4567',
      url: null,
      services: null,
      type: 'Volunteer',
      stateId: '49',
      stateAbbreviation: 'WI',
      stateName: 'Wisconsin'
    }
  }
}


{
  lineItems: [
    {
      emailAddress: 'testing@wsfia.org',
      userId: '106',
      quantity: 1,
      itemId: '1',
      itemName: 'WSFIA Membership',
      itemDescription: 'Member Name: FirstName LastName\nMember ID: WSFIA-10620311',
      price: '40.00'
    },
    {
      emailAddress: 'testing1@wsfia.org',
      userId: '107',
      quantity: 1,
      itemId: '1',
      itemName: 'WSFIA Membership',
      itemDescription: 'Member Name: FirstName1 LastName1\nMember ID: WSFIA-10720311',
      price: '40.00'
    }
  ],
  billing: {
    billingEmailAddress: 'testing@wsfia.org',
    billingBusiness: {
      id: '1',
      name: 'Fitchburg Fire',
      station: '2',
      streetAddress: '123 Test Road',
      city: 'Fitchburg',
      state: '49',
      zipcode: '53719',
      phone: '(608) 123-4567',
      url: null,
      services: null,
      type: 'Combination',
      stateId: '49',
      stateAbbreviation: 'WI',
      stateName: 'Wisconsin'
    }
  }
}


*/




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
app.listen(process.env.PORT, () => {
    console.log(`App Started on Port ${process.env.PORT}`);
});
