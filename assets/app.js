/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import "./styles/app.scss";

// start the Stimulus application
import "./bootstrap";

const $ = require("jquery");
// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require("bootstrap");

/*$(document).ready(function () {
  $('[data-toggle="popover"]').popover();
});*/

/*let allInstructors = $.ajax({
  url: "login",
  dataType: "json",
  success: function (data) {
    console.log(data);
  },
});*/

let loginForm = $("#loginForm");
loginForm.on("submit", (event) => {
  event.preventDefault();
  //fetch email and validation status for all instructors
  $.ajax({
    url: window.location.href,
    method: "GET",
    dataType: "json",
    success: function (data) {
      //compare email value with email field
      $.each(data["message"], (key, value) => {
        if (
          value["email"].toLowerCase() === $("#inputEmail").val().toLowerCase()
        ) {
          if (value["isValidated"] === false) {
            alert(
              "Votre compte n'a pas encore été validé, vous ne pouvez pas vous connecter pour le moment."
            );
            return false;
          } else {
            event.currentTarget.submit();
          }
        } else {
          event.currentTarget.submit();
        }
      });
    },
  });
});
