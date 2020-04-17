const Member = require('./controllers/membership');
const Utilities = require('./controllers/utilities');
const Business = require('./controllers/business');
const Membership = require('./controllers/membership');
const Conference = require('./controllers/conference');

class Controllers {

    constructor() {

        this.member = new Member();
        this.utilities = new Utilities();
        this.business = new Business();
        this.membership = new Membership();
        this.conference = new Conference();

    }

}

module.exports = Controllers;