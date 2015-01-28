var getURLParams = function() {
	var queryStringArr = window.location.href.substring(window.location.href.indexOf('?') + 1).split('&');
	
	var params = {};
	for (var i = 0; i < queryStringArr.length; i++) {
		var queryStringKeyValuePair = queryStringArr[i].split('=');
		params[queryStringKeyValuePair[0].toLowerCase()] = queryStringKeyValuePair[1];
	}
	return params;
}