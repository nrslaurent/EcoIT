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

//check if instructor account is validated when login
$("#loginForm").on("submit", (event) => {
  event.preventDefault();
  //fetch email and validation status for all instructors
  $.ajax({
    url: window.location.href,
    method: "GET",
    dataType: "json",
    success: function (data) {
      var ValidatedAccount = true;
      //compare email value with email field
      $.each(data["message"], (key, value) => {
        if (
          value["email"].toLowerCase() ===
            $("#inputEmail").val().toLowerCase() &&
          value["isValidated"] === false
        ) {
          ValidatedAccount = false;
        }
      });
      if (ValidatedAccount === false) {
        alert(
          "Votre compte n'a pas encore été validé, vous ne pouvez pas vous connecter pour le moment."
        );
        window.location.replace("/logout");
        return false;
      } else {
        event.currentTarget.submit();
      }
    },
  });
});

//Display results without reloading page when a guest makes a research
$("#searchBar").on("keyup", () => {
  $.ajax({
    url: window.location.href,
    method: "GET",
    data: { search: $("#searchBar").val() },
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
              '</p><a href="/course/inprogress/' +
              value["id"] +
              '" class="btn btn-french-lilac align-self-end">Accéder</a></div></div></div>'
          );
        });
      }
    },
  });
});

//finishedLesson
$("#isFinishedLesson").on("click", "button", () => {
  $.ajax({
    url: window.location.href,
    method: "GET",
    data: {
      finishedLesson: "true",
    },
    success: function () {
      $("#isFinishedLesson").children().remove();
      $("#isFinishedLesson").append(
        '<img src="/uploads/images/done.png" alt="done">'
      );
    },
  });
});

//Display all courses in progress or done
$("input").on("click", (event) => {
  let myCourses = "false";
  let doneCourses = "false";
  let allCourses = "false";
  if ($("#myCourses").is(":checked")) {
    myCourses = "true";
  }
  if ($("#doneCourses").is(":checked")) {
    doneCourses = "true";
  }
  if (!$("#myCourses").is(":checked") && !$("#doneCourses").is(":checked")) {
    allCourses = "true";
  }
  $.ajax({
    url: window.location.href,
    method: "GET",
    dataType: "json",
    data: {
      myCourses: myCourses,
      doneCourses: doneCourses,
      allCourses: allCourses,
    },
    success: function (data) {
      //delete all children
      $("#allCardsPage").empty();
      //clear search bar
      if ($("#myCourses").is(":checked") || $("#doneCourses").is(":checked")) {
        $("#searchBar").val("").prop("disabled", true);
      } else {
        $("#searchBar").prop("disabled", false);
      }
      if (allCourses === "true") {
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
                '</p><a href="/course/inprogress/' +
                value["id"] +
                '" class="btn btn-french-lilac align-self-end">Accéder</a></div></div></div>'
            );
          });
        }
      }
      if ($("#myCourses").is(":checked") && $("#doneCourses").is(":checked")) {
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
                '</p><a href="/course/inprogress/' +
                value["id"] +
                '" class="btn btn-french-lilac align-self-end">Accéder</a></div></div></div>'
            );
          });
        }
      } else {
        if ($("#myCourses").is(":checked")) {
          let course = [];
          $.each(data["message"], (key, value) => {
            if (value["isDone"] === false) {
              course.push(value);
            }
          });
          if (course.length === 0) {
            $("#allCardsPage").append(
              '<h3 class="text-danger text-center my-5">Aucune correspondance trouvée.<h3>'
            );
          } else {
            //add card for each line in "data"
            $.each(course, (key, value) => {
              $("#allCardsPage").append(
                '<div class="col-xs-12 col-lg-3"><div class="card mx-4 my-5"><div class="courseCard"><img src="/uploads/images/' +
                  value["picture"] +
                  '") class="card-img-top" alt="course_picture"><div class="card-body col courseCardText d-flex flex-column justify-content-evenly"><h5 class="card-title text-center">' +
                  value["title"] +
                  '</h5><p class="card-text">' +
                  value["description"] +
                  '</p><a href="/course/inprogress/' +
                  value["id"] +
                  '" class="btn btn-french-lilac align-self-end">Accéder</a></div></div></div>'
              );
            });
          }
        }
        if ($("#doneCourses").is(":checked")) {
          let course = [];
          $.each(data["message"], (key, value) => {
            if (value["isDone"] === true) {
              course.push(value);
            }
          });
          if (course.length === 0) {
            $("#allCardsPage").append(
              '<h3 class="text-danger text-center my-5">Aucune correspondance trouvée.<h3>'
            );
          } else {
            //add card for each line in "data"
            $.each(course, (key, value) => {
              $("#allCardsPage").append(
                '<div class="col-xs-12 col-lg-3"><div class="card mx-4 my-5"><div class="courseCard"><img src="/uploads/images/' +
                  value["picture"] +
                  '") class="card-img-top" alt="course_picture"><div class="card-body col courseCardText d-flex flex-column justify-content-evenly"><h5 class="card-title text-center">' +
                  value["title"] +
                  '</h5><p class="card-text">' +
                  value["description"] +
                  '</p><a href="/course/inprogress/' +
                  value["id"] +
                  '" class="btn btn-french-lilac align-self-end">Accéder</a></div></div></div>'
              );
            });
          }
        }
      }
    },
  });
});

$(function () {
  if (!$("#coursesList li").children().hasClass("fw-bolder")) {
    $("#coursesList").children().first().addClass("fw-bolder");
  }
  if (!$("#sectionsListInline li").children().hasClass("fw-bolder")) {
    $("#sectionsListInline").children().first().addClass("fw-bolder");
  }
  if (!$("#lessonsListInline li").children().hasClass("fw-bolder")) {
    $("#lessonsListInline").children().first().addClass("fw-bolder");
  }
});
