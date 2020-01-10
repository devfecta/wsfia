const express = require('express');
const session = require('express-session');
const cookie = require('cookie');
const app = express();
const bcrypt = require('bcrypt');
const dotenv = require('dotenv');
dotenv.config();

app.use(session({secret: 'secretValue', saveUninitialized: false, resave: false}));
app.use(express.static('public'));

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
    response.render('./registration/businessInfo.ejs', { session: request.session });
});

app.get('/register/members', (request, response) => {
    //let cookies = cookie.parse(request.headers.cookie);
    response.render('./registration/businessMembers.ejs', { session: request.session});
});

app.post('/register', async (request, response) => {

    try {
        const hashedPassword = await bcrypt.hash(request.body.password, 10);
        request.body.email

        response.render('./registration/memberInfo.ejs', { session: request.session });
        
    }
    catch {}

    response.render('register.ejs', { session: request.session, view: ''});

});

app.listen(process.env.PORT, () => {
    console.log(`App Started on Port ${process.env.PORT}`);
});