let user;
let contactList;
let groupList;
let messages;

$.ajax({
    type: 'GET',
    async: false,
    contentType: 'application/json',
	url: '../functions.php?action=getUser',
	//url: '../functions.php/getUser',
    success: (function (data) {
		console.log(data);
	    user = data;
    })
});

$.ajax({
    type: 'GET',
    async: false,
    contentType: 'application/json',
	url: '../functions.php?action=getAllContacts',
	//url: '../functions.php/getAllContacts',
    success: (function (data) {
	    contactList = data;
    })
});

$.ajax({
    type: 'GET',
    async: false,
    contentType: 'application/json',
	url: '../functions.php?action=getAllGroups',
	//url: '../functions.php/getAllGroups',
    success: (function (data) {
	    groupList = data;
    })
});

$.ajax({
    type: 'GET',
    async: false,
    contentType: 'application/json',
	url: '../functions.php?action=getAllMessages',
	//url: '../functions.php/getAllMessages',
    success: (function (data) {
	    messages = data;
    })
});

let MessageUtils = {
	getByGroupId: (groupId) => {
		return messages.filter(msg => msg.recvIsGroup && msg.recvId === groupId);
	},
	getByContactId: (contactId) => {
		return messages.filter(msg => {
			return !msg.recvIsGroup && ((msg.sender === user.id && msg.recvId === contactId) || (msg.sender === contactId && msg.recvId === user.id));
		});
	},
	getMessages: () => {
		return messages;
	},
	changeStatusById: (options) => {
		messages = messages.map((msg) => {
			if (options.isGroup) {
				if (msg.recvIsGroup && msg.recvId === options.id) msg.status = 2;
			} else {
				if (!msg.recvIsGroup && msg.sender === options.id && msg.recvId === user.id) msg.status = 2;
			}
			return msg;
		});
	},
	addMessage: (msg) => {
		msg.id = messages.length + 1;
		messages.push(msg);
	}
};