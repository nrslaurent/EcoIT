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

//check if instructor account is valiadated when login
$("#loginForm").on("submit", (event) => {
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

//instructor page - add active class in first course
$.when($.ready).then(() => {
  if (!$("#coursesList").children("p").hasClass("active")) {
    $("#coursesList li p").first().addClass("active fw-bolder");
  }
});

//display all sections when clicking on a course
$("#coursesList").on("click", "p", (event) => {
  $("p").removeClass("active fw-bolder");
  $(event.target).addClass("active fw-bolder");
  $.ajax({
    url: window.location.href,
    method: "GET",
    dataType: "json",
    data: {
      course: $("#coursesList .active").text(),
      isCourseChanged: "true",
    },
    success: function (data) {
      $("#sectionsListInline").children().remove();
      $.each(data["message"], (key, value) => {
        $("#sectionsListInline").append(
          '<li class="mx-2"><span class="french-lilac fs-4">' +
            value["section"] +
            "</span></li>"
        );
      });
      //add active class in first section
      $("#sectionsListInline li span").first().addClass("active fw-bolder");
      //add all lessons for active class
      $.ajax({
        url: window.location.href,
        method: "GET",
        dataType: "json",
        data: {
          course: $("#coursesList .active").text(),
          section: $("#sectionsListInline .active").text(),
          lesson: $("#lessonsListInline .active").text(),
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
          $("#lessonsListInline li span").first().addClass("active fw-bolder");
          $.ajax({
            url: window.location.href,
            method: "GET",
            dataType: "json",
            data: {
              course: $("#coursesList .active").text(),
              section: $("#sectionsListInline .active").text(),
              lesson: $("#lessonsListInline .active").text(),
            },
            success: function (data) {
              $.each(data["message"], (key, value) => {
                if ($("#lessonsListInline .active").text() === value["title"]) {
                  let finishedCurrentLesson = false;
                  $("#lessonInProgress").children().remove();
                  if (value["finishedLesson"].length > 0) {
                    value["finishedLesson"].forEach((element) => {
                      if (element === value["userId"]) {
                        finishedCurrentLesson = true;
                      }
                    });
                  }
                  $("#lessonInProgress").append(
                    '<div class="col-7 row"><iframe width="736" height="400" src="' +
                      value["video"] +
                      '" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe><div class="my-5 bg-mellow-apricot"><ul class="nav">' +
                      $.each(value["resources"], function (index, resource) {
                        '<li class="mx-2"><a href="/uploads/resources/"' +
                          resource +
                          '" class="french-lilac fs-4">' +
                          resource +
                          "</a></li>";
                      }),
                    '</ul></div></div><div class="col-5 d-flex flex-column justify-content-around"><p>' +
                      value["description"] +
                      '</p><div class="d-flex justify-content-end" id="isFinishedLesson">'
                  );
                  if (value["isInstructor"] === false) {
                    if (finishedCurrentLesson === true) {
                      $("#isFinishedLesson").append(
                        '<img src="/uploads/images/done.png" alt="done"></div></div>'
                      );
                    } else {
                      $("#isFinishedLesson").append(
                        '<button type="button" class="btn btn-french-lilac w-50 align-self-end finishedLessonButton">J\'ai terminé ce cours</button></div></div>'
                      );
                    }
                  }
                }
              });
            },
          });
        },
      });
    },
  });
});

//display all lessons for a section
//if no section with active class, add active class in the first section
$.when($.ready).then(() => {
  if (!$("#sectionsListInline").children("span").hasClass("active")) {
    $("#sectionsListInline li span").first().addClass("active fw-bolder");
    $.ajax({
      url: window.location.href,
      method: "GET",
      dataType: "json",
      data: {
        course: $("#coursesList .active").text(),
        section: $("#sectionsListInline .active").text(),
        lesson: $("#lessonsListInline .active").text(),
      },
      success: function (data) {
        $.each(data["message"], (key, value) => {
          $("#lessonsListInline").append(
            '<li class="mx-2"><span class="french-lilac fs-5">' +
              value["title"] +
              "</span></li>"
          );
        });
        $("#lessonsListInline li span").first().addClass("active fw-bolder");
      },
    });
  }
});

//display all lessons when clicking on a section
$("#sectionsListInline").on("click", "span", (event) => {
  $("span").removeClass("active fw-bolder");
  $(event.target).addClass("active fw-bolder");
  $.ajax({
    url: window.location.href,
    method: "GET",
    dataType: "json",
    data: {
      course: $("#coursesList .active").text(),
      section: $(event.target).text(),
      lesson: $("#lessonsListInline .active").text(),
    },
    success: function (data) {
      console.log(data);
      $("#lessonsListInline").children().remove();
      $.each(data["message"], (key, value) => {
        $("#lessonsListInline").append(
          '<li class="mx-2"><span class="french-lilac fs-5">' +
            value["title"] +
            "</span></li>"
        );
      });
      //add active class in first lesson
      $("#lessonsListInline li span").first().addClass("active fw-bolder");
      //display lesson with active class
      $.each(data["message"], (key, value) => {
        if ($("#lessonsListInline .active").text() === value["title"]) {
          let finishedCurrentLesson = false;
          $("#lessonInProgress").children().remove();
          if (value["finishedLesson"].length > 0) {
            value["finishedLesson"].forEach((element) => {
              if (element === value["userId"]) {
                finishedCurrentLesson = true;
              }
            });
          }
          $("#lessonInProgress").append(
            '<div class="col-7 row"><iframe width="736" height="400" src="' +
              value["video"] +
              '" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe><div class="my-5 bg-mellow-apricot"><ul class="nav">' +
              $.each(value["resources"], function (index, resource) {
                '<li class="mx-2"><a href="/uploads/resources/"' +
                  resource +
                  '" class="french-lilac fs-4">' +
                  resource +
                  "</a></li>";
              }),
            '</ul></div></div><div class="col-5 d-flex flex-column justify-content-around"><p>' +
              value["description"] +
              '</p><div class="d-flex justify-content-end" id="isFinishedLesson">'
          );
          if (value["isInstructor"] === false) {
            if (finishedCurrentLesson === true) {
              $("#isFinishedLesson").append(
                '<img src="/uploads/images/done.png" alt="done"></div></div>'
              );
            } else {
              $("#isFinishedLesson").append(
                '<button type="button" class="btn btn-french-lilac w-50 align-self-end finishedLessonButton">J\'ai terminé ce cours</button></div></div>'
              );
            }
          }
        }
      });
    },
  });
});

//display lesson elements when clicking on its name
$("#lessonsListInline").on("click", "span", (event) => {
  $("#lessonsListInline span").removeClass("active fw-bolder");
  $(event.target).addClass("active fw-bolder");
  $.ajax({
    url: window.location.href,
    method: "GET",
    dataType: "json",
    data: {
      course: $("#coursesList .active").text(),
      section: $("#sectionsListInline .active").text(),
      lesson: $("#lessonsListInline .active").text(),
    },
    success: function (data) {
      $.each(data["message"], (key, value) => {
        if ($("#lessonsListInline .active").text() === value["title"]) {
          let finishedCurrentLesson = false;
          $("#lessonInProgress").children().remove();
          if (value["finishedLesson"].length > 0) {
            value["finishedLesson"].forEach((element) => {
              if (element === value["userId"]) {
                finishedCurrentLesson = true;
              }
            });
          }
          $("#lessonInProgress").append(
            '<div class="col-7 row"><iframe width="736" height="400" src="' +
              value["video"] +
              '" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe><div class="my-5 bg-mellow-apricot"><ul class="nav">' +
              $.each(value["resources"], function (index, resource) {
                '<li class="mx-2"><a href="/uploads/resources/"' +
                  resource +
                  '" class="french-lilac fs-4">' +
                  resource +
                  "</a></li>";
              }),
            '</ul></div></div><div class="col-5 d-flex flex-column justify-content-around"><p>' +
              value["description"] +
              '</p><div class="d-flex justify-content-end" id="isFinishedLesson">'
          );
          if (value["isInstructor"] === false) {
            if (finishedCurrentLesson === true) {
              $("#isFinishedLesson").append(
                '<img src="/uploads/images/done.png" alt="done"></div></div>'
              );
            } else {
              $("#isFinishedLesson").append(
                '<button type="button" class="btn btn-french-lilac w-50 align-self-end finishedLessonButton">J\'ai terminé ce cours</button></div></div>'
              );
            }
          }
        }
      });
    },
  });
});

//finishedLesson
$("body").on("click", "button", () => {
  $.ajax({
    url: window.location.href,
    method: "GET",
    data: {
      finishedLesson: "true",
      section: $("#sectionsListInline .active").text(),
      lesson: $("#lessonsListInline .active").text(),
    },
    success: function (data) {
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

//display all lessons when clicking on a section
/*$("#allCoursesInstructor").on("click", "p", (event) => {
  $("p").removeClass("active fw-bolder");
  $(event.target).addClass("active fw-bolder");
  $.ajax({
    url: window.location.href,
    method: "GET",
    dataType: "json",
    data: {
      course: $(event.target).text(),
      section: $("#sectionsListInline .active").text(),
      lesson: $("#lessonsListInline .active").text(),
    },
    success: function (data) {
      $("#sectionsListInline").children().remove();
      $("#lessonsListInline").children().remove();
      $.each(data["message"], (key, value) => {
        $("#sectionsListInline").append(
          '<li class="mx-2"><span class="french-lilac fs-4">' +
            value["section"] +
            "</span></li>"
        );

        $("#lessonsListInline").append(
          '<li class="mx-2"><span class="french-lilac fs-5">' +
            value["title"] +
            "</span></li>"
        );
      });
      //add active class in first lesson
      $("#lessonsListInline li span").first().addClass("active fw-bolder");
      //display lesson with active class
      $.each(data["message"], (key, value) => {
        if ($("#lessonsListInline .active").text() === value["title"]) {
          $("#lessonInProgress").children().remove();
        }
        $("#lessonInProgress").append(
          '<div class="col-7 row"><iframe width="736" height="400" src="' +
            value["video"] +
            '" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe><div class="my-5 bg-mellow-apricot"><ul class="nav">' +
            $.each(value["resources"], function (index, resource) {
              '<li class="mx-2"><a href="/uploads/resources/"' +
                resource +
                '" class="french-lilac fs-4">' +
                resource +
                "</a></li>";
            }),
          '</ul></div></div><div class="col-5 d-flex flex-column justify-content-around"><p>' +
            value["description"] +
            '</p><div class="d-flex justify-content-end" id="isFinishedLesson"></div>'
        );
      });
    },
  });
});*/
