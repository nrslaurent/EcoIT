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

let searchBar = $("#searchBar");
searchBar.on("keyup", () => {
  $.ajax({
    url: window.location.href,
    method: "GET",
    data: { search: searchBar.val() },
    success: function (data) {
      //delete all children
      $("#allCardsPage").empty();

      //Display a message if there is no result
      if (data["message"].length === 0) {
        $("#allCardsPage").append(
          '<h3 class="text-danger text-center my-5">Aucune correspondance trouvée.<h3>'
        );
      } else {
        //add card for each line in "data"
        $.each(data["message"], (key, value) => {
          $("#allCardsPage").append(
            '<div class="col-xs-12 col-lg-3"><div class="card mx-4 my-5"><div class="courseCard"><img src="/uploads/images/' +
              value["picture"] +
              '") class="card-img-top" alt="course_picture"><div class="card-body col courseCardText d-flex flex-column justify-content-evenly"><h5 class="card-title text-center">' +
              value["title"] +
              '</h5><p class="card-text">' +
              value["description"] +
              '</p><a href="#" class="btn btn-french-lilac align-self-end">Suivre</a></div></div></div>'
          );
        });
      }
    },
  });
});

//display all lessons for a section
let sectionsList = $("#sectionsListInline");
//if no section with active class, add active class in the first section
if (!sectionsList.children("span").hasClass("active")) {
  $("#sectionsListInline li span").first().addClass("active fw-bolder");
  $.ajax({
    url: window.location.href,
    method: "GET",
    dataType: "json",
    data: {
      section: $(".active").text(),
    },
    success: function (data) {
      $.each(data["message"], (key, value) => {
        $("#lessonsListInline").append(
          '<li class="mx-2"><span class="french-lilac fs-5">' +
            value["title"] +
            "</span></li>"
        );
      });
      $("#lessonsListInline>li>span").first().addClass("fw-bolder");
    },
  });
}
sectionsList.on("click", "span", (event) => {
  $("span").removeClass("active fw-bolder");
  $(event.target).addClass("active fw-bolder");
  $.ajax({
    url: window.location.href,
    method: "GET",
    dataType: "json",
    data: {
      course: $("#courseTitle").text(),
      section: $(".active").text(),
    },
    success: function (data) {
      $("#lessonsListInline").children().remove();
      $.each(data["message"], (key, value) => {
        $("#lessonsListInline").append(
          '<li class="mx-2"><span class="french-lilac fs-5">' +
            value["title"] +
            "</span></li>"
        );
      });
      $("#lessonsListInline>li>span").first().addClass("fw-bolder");
    },
  });
});

let lessonsList = $("#lessonsListInline");
lessonsList.on("click", "span", (event) => {
  $("#lessonsListInline span").removeClass("active fw-bolder");
  $(event.target).addClass("active fw-bolder");
});
