function showPassword() {

    let password = document.getElementById('password');
    let checkbox = document.getElementById('showpwd');

    if (checkbox.checked) {

	password.type = 'text';
    }
    else {

	password.type = 'password';
    }
}
