function changeTheme(forced) {

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
	document.body.style.backgroundColor = '#131313';
	document.body.style.color = '#e1e1e1';

	darkBoolReview.value = true;
	darkBoolOrder.value = true;
	darkBoolDate.value = true;
	// darkBoolClient.value = true;

	textReview.style['color'] = '#131313';
	submitReview.style['color'] = '#131313';
	resetReview.style['color'] = '#131313';

	textOrder.style['color'] = '#131313';
	submitOrder.style['color'] = '#131313';
	resetOrder.style['color'] = '#131313';

	textDate.style['color'] = '#131313';
	submitDate.style['color'] = '#131313';
	resetDate.style['color'] = '#131313';

	// textClient.style['color'] = '#131313';
	// submitClient.style['color'] = '#131313';
	// resetClient.style['color'] = '#131313';

	textReview.style['background-color'] = '#e1e1e1';
	submitReview.style['background-color'] = '#e1e1e1';
	resetReview.style['background-color'] = '#e1e1e1';

	textOrder.style['background-color'] = '#e1e1e1';
	submitOrder.style['background-color'] = '#e1e1e1';
	resetOrder.style['background-color'] = '#e1e1e1';

	textDate.style['background-color'] = '#e1e1e1';
	submitDate.style['background-color'] = '#e1e1e1';
	resetDate.style['background-color'] = '#e1e1e1';

	// textClient.style['background-color'] = '#e1e1e1';
	// submitClient.style['background-color'] = '#e1e1e1';
	// resetClient.style['background-color'] = '#e1e1e1';

    }
    else {
	document.body.style.backgroundColor = '#e1e1e1';
	document.body.style.color = '#131313';

	darkBoolReview.value = false;
	darkBoolOrder.value = false;
	darkBoolDate.value = false;
	// darkBoolClient.value = false;

	textReview.style['color'] = '#e1e1e1';
	submitReview.style['color'] = '#e1e1e1';
	resetReview.style['color'] = '#e1e1e1';

	textOrder.style['color'] = '#e1e1e1';
	submitOrder.style['color'] = '#e1e1e1';
	resetOrder.style['color'] = '#e1e1e1';

	textDate.style['color'] = '#e1e1e1';
	submitDate.style['color'] = '#e1e1e1';
	resetDate.style['color'] = '#e1e1e1';

	// textClient.style['color'] = '#e1e1e1';
	// submitClient.style['color'] = '#e1e1e1';
	// resetClient.style['color'] = '#e1e1e1';

	textReview.style['background-color'] = '#131313';
	submitReview.style['background-color'] = '#131313';
	resetReview.style['background-color'] = '#131313';

	textOrder.style['background-color'] = '#131313';
	submitOrder.style['background-color'] = '#131313';
	resetOrder.style['background-color'] = '#131313';

	textDate.style['background-color'] = '#131313';
	submitDate.style['background-color'] = '#131313';
	resetDate.style['background-color'] = '#131313';

	// textClient.style['background-color'] = '#131313';
	// submitClient.style['background-color'] = '#131313';
	// resetClient.style['background-color'] = '#131313';
    }
}
