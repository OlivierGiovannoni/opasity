function changeTheme(forced) {

    let dark = '#131313';
    let light = '#e1e1e1';

    let darkBool = document.getElementById('darkBool');
    let darkBoolReview = document.getElementById('darkBoolReview');
    let darkBoolOrder = document.getElementById('darkBoolOrder');
    let darkBoolDate = document.getElementById('darkBoolDate');
    // let darkBoolClient = document.getElementById('darkBoolClient');

    let textReview = document.getElementById('reviewName');
    let submitReview = document.getElementById('submitReview');
    let resetReview = document.getElementById('resetReview');

    let textOrder = document.getElementById('contractId');
    let submitOrder = document.getElementById('submitOrder');
    let resetOrder = document.getElementById('resetOrder');

    let textDate = document.getElementById('dueDate');
    let submitDate = document.getElementById('submitDate');
    let resetDate = document.getElementById('resetDate');

    // let textClient = document.getElementById('dueClient');
    // let submitClient = document.getElementById('submitClient');
    // let resetClient = document.getElementById('resetClient');

    if (darkBool.checked || forced) {
	document.body.style.backgroundColor = dark;
	document.body.style.color = light;

	darkBoolReview.value = true;
	darkBoolOrder.value = true;
	darkBoolDate.value = true;
	// darkBoolClient.value = true;

	textReview.style['color'] = light;
	submitReview.style['color'] = light;
	resetReview.style['color'] = light;

	textOrder.style['color'] = light;
	submitOrder.style['color'] = light;
	resetOrder.style['color'] = light;

	textDate.style['color'] = light;
	submitDate.style['color'] = light;
	resetDate.style['color'] = light;

	// textClient.style['color'] = light;
	// submitClient.style['color'] = light;
	// resetClient.style['color'] = light;

	textReview.style['border'] = '1px solid ' + light;
	submitReview.style['border'] = '1px solid ' + light;
	resetReview.style['border'] = '1px solid ' + light;

	textOrder.style['border'] = '1px solid ' + light;
	submitOrder.style['border'] = '1px solid ' + light;
	resetOrder.style['border'] = '1px solid ' + light;

	textDate.style['border'] = '1px solid ' + light;
	submitDate.style['border'] = '1px solid ' + light;
	resetDate.style['border'] = '1px solid ' + light;

	// textClient.style['border'] = '1px solid ' + light;
	// submitClient.style['border'] = '1px solid ' + light;
	// resetClient.style['border'] = '1px solid ' + light;

	textReview.style['background-color'] = dark;
	submitReview.style['background-color'] = dark;
	resetReview.style['background-color'] = dark;

	textOrder.style['background-color'] = dark;
	submitOrder.style['background-color'] = dark;
	resetOrder.style['background-color'] = dark;

	textDate.style['background-color'] = dark;
	submitDate.style['background-color'] = dark;
	resetDate.style['background-color'] = dark;

	// textClient.style['background-color'] = dark;
	// submitClient.style['background-color'] = dark;
	// resetClient.style['background-color'] = dark;
    }
    else {
	document.body.style.backgroundColor = light;
	document.body.style.color = dark;

	darkBoolReview.value = false;
	darkBoolOrder.value = false;
	darkBoolDate.value = false;
	// darkBoolClient.value = false;

	textReview.style['color'] = dark;
	submitReview.style['color'] = dark;
	resetReview.style['color'] = dark;

	textOrder.style['color'] = dark;
	submitOrder.style['color'] = dark;
	resetOrder.style['color'] = dark;

	textDate.style['color'] = dark;
	submitDate.style['color'] = dark;
	resetDate.style['color'] = dark;

	// textClient.style['color'] = dark;
	// submitClient.style['color'] = dark;
	// resetClient.style['color'] = dark;

	textReview.style['border'] = '1px solid ' + dark;
	submitReview.style['border'] = '1px solid ' + dark;
	resetReview.style['border'] = '1px solid ' + dark;

	textOrder.style['border'] = '1px solid ' + dark;
	submitOrder.style['border'] = '1px solid ' + dark;
	resetOrder.style['border'] = '1px solid ' + dark;

	textDate.style['border'] = '1px solid ' + dark;
	submitDate.style['border'] = '1px solid ' + dark;
	resetDate.style['border'] = '1px solid ' + dark;

	// textClient.style['border'] = '1px solid ' + dark;
	// submitClient.style['border'] = '1px solid ' + dark;
	// resetClient.style['border'] = '1px solid ' + dark;

	textReview.style['background-color'] = light;
	submitReview.style['background-color'] = light;
	resetReview.style['background-color'] = light;

	textOrder.style['background-color'] = light;
	submitOrder.style['background-color'] = light;
	resetOrder.style['background-color'] = light;

	textDate.style['background-color'] = light;
	submitDate.style['background-color'] = light;
	resetDate.style['background-color'] = light;

	// textClient.style['background-color'] = light;
	// submitClient.style['background-color'] = light;
	// resetClient.style['background-color'] = light;
    }
}
