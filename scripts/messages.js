// This Javascript handles refreshing the the messages table
//  depending on how user wants it sorted!

// BASED ON lecture 24 demo code (Original Work by Kyle Harms)

function refreshTable(sortby) {

	$.get('contact.php?', 'api=&sort=' + sortby, function (results) {

		// Replace the old results with the new ones
		$("#messages_table").html(results);

		// I don't think we need to update URL/history given the nature
		// of this function.
	}, 'html');
}
