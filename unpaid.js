var mysql      = require('mysql');

var connection = mysql.createConnection({
    host     : 'localhost',
    user     : 'root',
    password : 'stage972',
    database : 'OPAS'
});

connection.connect(function(err) {
    if (err) {
	console.error('error connecting: ' + err.stack);
	return;
    }

    console.log('connected as id ' + connection.threadId);
});

// connection.query('USE OPAS;', function (error, results, fields) {
// });

connection.query('SELECT * FROM webcontrat_contrat;', function (error, results, fields) {
    if (error) throw error;
    console.log(results);
});

connection.end(function(err) {

});
