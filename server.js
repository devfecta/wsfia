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

app.use(session({secret: uuid(), saveUninitialized: true, resave: true}));
// app.use(express.static('public'));
app.use('/', express.static('public'));

//app.use(express.urlencoded());

const Controllers = require('./controllers');
const controllers = new Controllers();

app.set('view-engine', 'ejs');
// Gives the ablity to use the request parameter to pass form elements.
app.use(express.urlencoded({extended: false}));

app.get('/', (request, response) => {
    request.session.test = 'testing';
    console.log(request.session);
    response.render('index.ejs', { session: request.session });
});

app.get('/login', (request, response) => {

    console.log(request.session);
    response.render('login.ejs', { session: request.session });
    // controllers.member.login();
});

app.get('/register', (request, response) => {
    //console.log(app.request.get('header'));
    response.render('./registration/businessSearch.ejs', { session: request.session });
});
/**
 * Adding a business
 */
app.get('/register/business', (request, response) => {
    //let cookies = cookie.parse(request.headers.cookie);
    response.render('./registration/businessInfo.ejs', { session: request.session, message: '' });
});

app.post('/addBusiness', async (request, response) => {
    //console.log(request.body);
    request.session.confirm = await controllers.business.addBusiness(JSON.stringify(request.body));
    //console.log(response.statusCode);
    if (request.session.confirm) {
        response.redirect('/register');
    }
    else {
        response.render('./registration/businessInfo.ejs', { session: request.session, message: '<div class="alert alert-danger m-1" role="alert">There was an error when trying to add the business.</div>' });
    }
});
/**
 * Adding a member
 */
app.get('/register/member', (request, response) => {

    if(request.session.sessionId === undefined){
        request.session.sessionId = uuid();
    }

    console.log(request.session.sessionId);

    request.session.test2 = 'testing2';
    //let cookies = cookie.parse(request.headers.cookie);
    response.render('./registration/memberInfo.ejs', { session: request.session, message: '' });
});

app.post('/register/addMember', async (request, response) => {
    //console.log(request.body);
    request.body.sessionId = request.session.sessionId;
    request.session.confirm = await controllers.membership.addMember(JSON.stringify(request.body));

    if (request.session.confirm) {
        response.redirect('/register/member/registrants');
    }
    else {
        response.render('./registration/memberInfo.ejs', { session: request.session, message: '<div class="alert alert-danger m-1" role="alert">There was an error when trying to add the member.</div>' });
    }    
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
app.listen(process.env.PORT, () => {
    console.log(`App Started on Port ${process.env.PORT}`);
});
