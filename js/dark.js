function reviewTheme(darkBool, main, alt) {

    let darkBoolReview = document.getElementById('darkBoolReview');

    let textReview = document.getElementById('reviewName');
    let submitReview = document.getElementById('submitReview');
    let resetReview = document.getElementById('resetReview');

    darkBoolReview.value = darkBool;

    textReview.style['background-color'] = main;
    submitReview.style['background-color'] = main;
    resetReview.style['background-color'] = main;

    textReview.style['color'] = alt;
    submitReview.style['color'] = alt;
    resetReview.style['color'] = alt;

    textReview.style['border'] = '1px solid ' + alt;
    submitReview.style['border'] = '1px solid ' + alt;
    resetReview.style['border'] = '1px solid ' + alt;
}

function orderTheme(darkBool, main, alt) {

    let darkBoolOrder = document.getElementById('darkBoolOrder');

    let textOrder = document.getElementById('contractId');
    let submitOrder = document.getElementById('submitOrder');
    let resetOrder = document.getElementById('resetOrder');

    darkBoolOrder.value = darkBool;

    textOrder.style['background-color'] = main;
    submitOrder.style['background-color'] = main;
    resetOrder.style['background-color'] = main;

    textOrder.style['color'] = alt;
    submitOrder.style['color'] = alt;
    resetOrder.style['color'] = alt;

    textOrder.style['border'] = '1px solid ' + alt;
    submitOrder.style['border'] = '1px solid ' + alt;
    resetOrder.style['border'] = '1px solid ' + alt;
}

function dateTheme(darkBool, main, alt) {

    let darkBoolDate = document.getElementById('darkBoolDate');

    let textDate = document.getElementById('dueDate');
    let submitDate = document.getElementById('submitDate');
    let resetDate = document.getElementById('resetDate');

    darkBoolDate.value = darkBool;

    textDate.style['background-color'] = main;
    submitDate.style['background-color'] = main;
    resetDate.style['background-color'] = main;

    textDate.style['color'] = alt;
    submitDate.style['color'] = alt;
    resetDate.style['color'] = alt;

    textDate.style['border'] = '1px solid ' + alt;
    submitDate.style['border'] = '1px solid ' + alt;
    resetDate.style['border'] = '1px solid ' + alt;
}

function clientTheme(darkBool, main, alt) {

    let darkBoolClient = document.getElementById('darkBoolClient');

    let textClient = document.getElementById('clientName');
    let submitClient = document.getElementById('submitClient');
    let resetClient = document.getElementById('resetClient');

    darkBoolClient.value = darkBool;

    textClient.style['background-color'] = main;
    submitClient.style['background-color'] = main;
    resetClient.style['background-color'] = main;

    textClient.style['color'] = alt;
    submitClient.style['color'] = alt;
    resetClient.style['color'] = alt;

    textClient.style['border'] = '1px solid ' + alt;
    submitClient.style['border'] = '1px solid ' + alt;
    resetClient.style['border'] = '1px solid ' + alt;
}

function tableSubs(background, text) {

    let tableSub = document.getElementById('tableSub');

    if (tableSub == null)
	return ;
    tableSub.style['background-color'] = background;
    tableSub.style['color'] = text;
}

function changeTheme(forced) {

    let dark = '#131313';
    let light = '#e1e1e1';

    let darkBool = document.getElementById('darkBool');

    if (darkBool.checked || forced) {
	document.body.style.backgroundColor = dark;
	document.body.style.color = light;

	reviewTheme(true, dark, light);
	orderTheme(true, dark, light);
	dateTheme(true, dark, light);
	clientTheme(true, dark, light);

	tableSubs(dark, 'orange');
    }
    else {
	document.body.style.backgroundColor = light;
	document.body.style.color = dark;

	reviewTheme(false, light, dark);
	orderTheme(false, light, dark);
	dateTheme(false, light, dark);
	clientTheme(false, light, dark);

	tableSubs(light, 'blue');
    }
}
