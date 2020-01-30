const Member = require('./controllers/member');
const Utilities = require('./controllers/utilities');
const Businesss = require('./controllers/business');

class Controllers {

    constructor() {

        this.member = new Member();
        this.utilities = new Utilities();
        this.business = new Businesss();

    }

}

module.exports = Controllers;