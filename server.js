const express = require('express');
const app = express();
const bcrypt = require('bcrypt');

app.use(express.static('public'));

const Controllers = require('./controllers');
const controllers = new Controllers();

app.set('view-engine', 'ejs');
// Gives the ablity to use the request parameter to pass form elements.
app.use(express.urlencoded({extended: false}));

app.get('/', (request, response) => {
    response.render('index.ejs', { session: '' });
});

app.get('/login', (request, response) => {
    response.render('login.ejs', { session: '' });
    // controllers.member.login();
});

app.get('/register', (request, response) => {
    response.render('./registration/businessInfo.ejs');
});

app.post('/register', async (request, response) => {

    try {
        const hashedPassword = await bcrypt.hash(request.body.password, 10);
        request.body.email

        response.render('./registration/memberInfo.ejs', { session: '' });
        
    }
    catch {}

    response.render('register.ejs', { session: '', view: ''});
});

app.listen(3000);