const express = require('express');
const app = express();
app.use(express.static('public'))

app.set('view-engine', 'ejs');

app.get('/', (request, response) => {
    response.render('index.ejs');
});

app.get('/login', (request, response) => {
    response.render('login.ejs');
});

app.get('/register', (request, response) => {
    response.render('register.ejs');
});

app.listen(3000);