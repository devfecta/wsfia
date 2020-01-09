const Member = require('./controllers/member');

class Controllers {

    constructor() {

        this.member = new Member();

    }

}

module.exports = Controllers;