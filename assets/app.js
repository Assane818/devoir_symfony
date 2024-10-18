/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

document.addEventListener('DOMContentLoaded', function() {
    const inputNom = document.getElementById('user_nom');
    const inputPrenom = document.getElementById('user_prenom');
    const inputLogin = document.getElementById('user_login');
    const inputPassword = document.getElementById('user_password');
    const inputUsername = document.getElementById('client_username');
    const inputTelephone = document.getElementById('client_telephone');
    const inputAddress = document.getElementById('client_address');
    const btnSubmit = document.getElementById('toggleSwitch');
    const formClient = document.getElementById('formClient');
    const formUser = document.getElementById('formUser');

    inputNom.removeAttribute('required');
    inputPrenom.removeAttribute('required');
    inputLogin.removeAttribute('required');
    inputPassword.removeAttribute('required');
    inputUsername.removeAttribute('required');
    inputTelephone.removeAttribute('required');
    inputAddress.removeAttribute('required');

    toggleSwitch.addEventListener('click', function() {
        if (toggleSwitch.checked) {
            formUser.classList.remove('hidden');
            formClient.classList.add('hidden');
        } else {
            formClient.classList.remove('hidden');
            formUser.classList.add('hidden');
        }
    });

});
