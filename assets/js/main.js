(function() {
  window.App = {
    Models: {},
    Collections: {},
    Views: {},
    Routes: {},
  };


  /*===========================================================================
  This function is tasked with saving data and able to be called by all views,
  this function accepts a model, a form (jQuery) and a collection as parameters.
  ============================================================================*/

  function saveData(model, form, collection) {
    // The data within the form is serialized using the function defined below
    // and stored within the variable "formdata". Whilst the model is stored
    // within the variable "newData".
    var formData = (form.serializeObject());
    var newData = model;

    // Backbones save method is used to store the "formData" in a model and
    // save the contents of the model by sending either a POST or PUT request.
    newData.save(formData, {
      success: function() {
        // On success the modal will be hidden by removing the class show
        // and the collection will be updated with new data using ".fetch()".
        $(".md-modal").removeClass("show");
        collection.fetch();
      }
    });
  };


  /*===========================================================================
  This function is tasked with the deletion of model data and accepts two
  parameters, these include a model and a collection.
  ============================================================================*/

  function deleteData(model, collection) {
    // The "destroy" method is executed on the model to send a delete
    // request to the backend with the models data as the payload, thus
    // resulting in either a quiz, question or option being remove.
    model.destroy({
      success: function() {
        // The collection passed will be fetched once again to contain
        // upto date information.
        collection.fetch({
          // upon the fetches completed the modal shall be removed.
          success: function() {$(".md-modal").removeClass("show"); }
        });
      }
    });
  };


  /*===========================================================================
  This function uses ".serializeArray" to convert form data into an javascript
  object and is used on a form. This code was obtained from: "http://stackover
  flow.com/questions/1184624/convert-form-data-to-javascript-object-with-jquery".
  (Stackoverflow.com, 2016)
  ============================================================================*/

  $.fn.serializeObject = function() {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
  };


  /*===========================================================================
  ajax prefiler is used to set the base url for the rest calls, allowing for
  shorthand notation to be used within both the models and collections created.
  ============================================================================*/


  $.ajaxPrefilter( function(options, originalOptions, jqXHR) {
      options.url = "http://localhost/CodeIgniter/index.php/Rest" + options.url;
  });

  /*===========================================================================
                              MODELS/COLLECTIONS
  Below both a model and collection have been defined for quizzes, questions and
  options. Each model makes use of the validate function which checks the
  integrity of the data stored within the default. In addition, the models also
  contain an "idAttribute" which is used to map an id contained within the JSON
  object to the models id. Lastly, each model makes use of a "url" attribute
  which contains the Rest API url associated with the model/collection.
  ============================================================================*/


  // Quiz Model
  App.Models.Quiz = Backbone.Model.extend({
    urlRoot: '/resource/quiz/quiz/', // This line defines the Rest API url.
    idAttribute: "quizId",     // This line maps the models id to the quizId.
    defaults: {                // The default values are defined and set to null.
      quizId: null,
      quizName: null,
      quizDescrip: null,
      quizImage: null
    },

    // The validate method is used to check the integrity of he mmodels data and
    // accepts the models "defaults" as parameters
    validate: function(attrs) {
      // If any of the values are empty an error message will returned/displayed.
      if ((attrs.quizName == "" || attrs.quizDescrip == "" || attrs.quizImage == "")) {
        $(".error").html("You must complete all fields.");
        return 'All fields must be filled out';
      }
    }
  });


  // Quiz Collection
  App.Collections.Quizzes = Backbone.Collection.extend({
    model: App.Models.Quiz,     // A model is to be used with the collection is specified.
    url: '/resource/quiz/quiz/' // The collection Rest url is defined in this line.
  });


  // Question Model
  App.Models.Question = Backbone.Model.extend({
    urlRoot: '/resource/question/question/',
    idAttribute: "questionId",
    defaults: {
      questionAnswer: null,
      questionId: null,
      questionImage: null,
      questionName: null,
      quizId: null,
    },

    validate: function(attrs) {
      if ((attrs.questionName == "" || attrs.questionImage == "" || attrs.questionAnswer == "")) {
        $(".error").html("You must complete all fields.");
        return 'All fields must be filled out';
      }
    }
  });


  // Question Collection
  App.Collections.Questions = Backbone.Collection.extend({
    model: App.Models.Question,
    url: '/resource/question/'
  });


  // Option Model
  App.Models.Option = Backbone.Model.extend({
      urlRoot: '/resource/option/option/',
      idAttribute: "optionID",
      defaults: {
        optionID: null,
        questionID: null,
        questionName: null
      },

      validate: function(attrs) {
        if ((attrs.questionName == "")) {
          $(".error").html("You must complete all fields.");
          return 'All fields must be filled out';
        }
      }
  });

  // Option Collection
  App.Collections.Options = Backbone.Collection.extend({
      model: App.Models.Option,
      url: '/resource/option/'
  });


  /*===========================================================================
                                    VIEWS
  Below a number of views are created for quizzes, questions and options all of
  which contain very similar code, each of these views are passed a collection
  and an id from the url. The Question View is passed the the quiz id to retrieve
  all the relating questions, whilst the Options View is passed the question id
  to get all the options for a question.
  ============================================================================*/


  // Quizzes (Index) View
  // This class is tasked with displaying the various quizzes available along with
  // with managing the creation, deletion and alterations of these quizzes.

  App.Views.Quizzes = Backbone.View.extend({
    el: ".page",

    events: {
      // The various events for the quiz view are defined below.
      "click .new-quiz"   : "newQuiz",
      "click .edit-quiz"  : "editQuiz",
      "click  .delete"    : "delete",
      "click .close"      : "close",
      "submit .quiz-form" : "create",
    },

    initialize: function () {
      // A listener is added to the passed collection which triggers the render
      // function upon any changes.
      this.listenTo(this.collection,"sync change", this.render);
      // A fetch is called to sync the collection, this also triggers the render
      // function.
      this.collection.fetch();
    },

    // This functions present in all view renders a view containing all the
    // available quizzes by utilising Underscore templateing along with jQuery.
    render: function(data) {
      // An existing underscore template is found using jQuery and stored within
      // the variable "template". Next, the collections resulting models are
      // stored within a javascript object named "vars".
      var template = _.template($('#quiz-list-template').html());
      var vars = {quizzes: data.models};
      // The below code shows the stored javascript object being passed to the
      // template and being rendered in the ".content" div within the html.
      var html = template(vars);
      this.$el.find(".content").html(html);
    },

    // This function renders and displays the modal used to create a new quiz, this modal contains
    // blank input fields and is also created using Underscore templateing.
    newQuiz: function() {
      // The correct template is located using jQuery and is stored within the variable template.
      var template = _.template($('#quiz-edit-template').html());
      // The passed collection is set to null to signify a new quiz is being created and is stored
      // in the javascript object named "vars".
      var vars = {quiz: null};
      var html = template(vars);
      $(".md-modal .md-content").html(html);
      // The class shown is added to the ".md-modal" to make the modal visible.
      $(".md-modal").addClass("show");
      return false;
    },


    // This function is renders the modal used to edit a quiz, this modal contains inputs containing
    // a quizzes existing data and is created using Underscore templateing.
    editQuiz: function(e) {
      // this is stored in a variable that to rescope "this."
      var that = this;
      // The id for the chosen quiz is retrieved using jQuery
      var id = $(e.target).parent().find("input").val();
      // The retrieved id stored in "id" is used to query the quiz model which returns the
      // resulting data upon fetch, this data is then passed to a modal template in which
      // the appropriate input fields will be filled in signifying an edit procedure.
      this.model = new App.Models.Quiz({quizId: id});
      this.model.fetch({
        success: function(data) {
          var template = _.template($('#quiz-edit-template').html());
          var vars = {quiz: data};
          var html = template(vars);
          $(".md-modal .md-content").html(html);
          $(".md-modal").addClass("show");
        }
      });
      return false;
    },

    // This function passed a quiz model, the name of the template and the collection being
    // used to the saveData function which is an anoymous function, this funcion then saves the
    // data.
    create: function() {
      new saveData(new App.Models.Quiz(), $(".quiz-form"), this.collection); // Add New Quiz.
      return false; // Prevent default form action.
    },

    // This function passed both the views model and colelction to the deleteData function which
    // is tasked with removing the data in the model fro the backend and reloading the collection
    // with the new data.
    delete: function(e) {
      new deleteData(this.model, this.collection) // Delete Quiz.
      return false; // Prevent default for action.
    },

    // This function closes the modal window by removing the class "show".
    close: function(e) {
      $(e.target).parent().removeClass("show");
    }

  });


  // Question View
  // This class is tasked with displaying the various quizzes available along with
  // with managing the creation, deletion and alterations of these quizzes.

  App.Views.Questions = Backbone.View.extend({
    el: '.page',

    events: {
      // The events for the question view are defined below.
      "click .new-question"  : "newQuestion",
      "click .edit-question" : "editQuestion",
      "click .delete"        : "delete",
      "click .close"         : "close",
      "submit .question-form": "create",
    },

    initialize: function(options) {
      // The quiz id passed in the url is passed and stored in the variable optons.
      this.options = options;
      // A listener is added to the question collection which fires the render
      // function upon "sync" or "change".
      this.listenTo(this.collection,"sync change", this.render);
      // The id passed to the url is appended and set as the collections url.
      this.collection.url = '/resource/question/quiz/' + options.id;
      // The collection fetches all the JSON data that that is a result of the
      // url defined above.
      this.collection.fetch();
    },

    // Once again, this function creates an underscore template and passed the
    // collections models and the quiz id to the template before appending it to
    // the ".content".
    render: function(data) {
      var template = _.template($('#question-list-template').html());
      var vars = {questions: data.models, id: this.options.id};
      var html = template(vars);
      this.$el.find(".content").html(html);
    },

    // Similar to the function "newQuiz", this function display a modal used to create a new question.
    // This modal contains blank inputs which is specified by no collection being stored in the "vars"
    // variable which is passed to the template. The modal template stored within the variable "template"
    // is passed the javascript object "vars" and added to the modal using jQuery.
    newQuestion: function() {
      var template = _.template($('#question-edit-template').html());
      var vars = {question: null, id: this.options.id};
      var html = template(vars);
      $(".md-modal .md-content").html(html);
      $(".md-modal").addClass("show");
      return false;
    },

    // This function is tasked with editing a question, the id for a particular question is retrieved using
    // jQuery and stored within id, this id is then used to query the Question Model which fetches the data
    // and sends it to the Underscore template which in turn shown a modal containing inputs filled with
    // the questions information.
    editQuestion: function(e) {
      var that = this;
      var id = $(e.target).parent().parent().find("input").val();
      this.model = new App.Models.Question({questionId: id});
      this.model.fetch({
        success: function(data) {
          var template = _.template($('#question-edit-template').html());
          var vars = {question: data, id: that.options.id};
          var html = template(vars);
          $(".md-modal .md-content").html(html);
          $(".md-modal").addClass("show");

        }
      });
      return false;
    },

    create: function() {
      new saveData(new App.Models.Question(), $(".question-form"), this.collection); // Add New Question.
      return false; // Prevent default form action.
    },

    delete: function() {
      new deleteData(this.model, this.collection); // Delete Question
      return false; // Prevent default form action (submitting).
    },

    close: function(e) {
      $(e.target).parent().removeClass("show");
    }

  });


  // Option View
  // This class is tasked with displaying the various options for a particular question,
  // along with managing the creation, deletion and alterations of these options.
  App.Views.Options = Backbone.View.extend({
    el: '.page',

    events: {
      // The various click events for the options view are defined below.
      "click .new-option"  : "newOption",
      "click .edit-option" : "editOption",
      "click .delete"      : "delete",
      "click .close"       : "close",
      "submit .option-form": "create",
    },

    initialize: function(options) {
      // The id passed in the url is passed and stored in the variable optons.
      this.options = options;
      // A listener is added to the options collection which fires the render
      // function upon "sync" or "change".
      this.listenTo(this.collection,"sync change", this.render);
      // The id passed to the url is appended and set as the collections url.
      this.collection.url = '/resource/option/question/' + options.id;
      // The collection fetches all the JSON data that that is a result of the
      // url defined above.
      this.collection.fetch();
    },

    // This functions render a iew containing all th available options for a particular
    // question, this is achieved using the question id appended to the collection url
    // and passed to the template (this.options.id).
    render: function(data) {
      // An Underscore template is retrieved using jQuery.
      var template = _.template($('#option-list-template').html());
      // The passed collection and question id are stored within an object named vars.
      var vars = {options: data.models, id: this.options.id};
      // Vars is then passed to the specified underscore template and created using
      // jQuery ".html()".
      var html = template(vars);
      this.$el.find(".content").html(html);
    },

    // This function renders and displays the modal used to create a new option, this modal contains
    // blank input fields and is also created using Underscore templateing.
    newOption: function() {
      var template = _.template($('#option-edit-template').html());
      var vars = {option: null, id: this.options.id};
      var html = template(vars);
      $(".md-modal .md-content").html(html);
      $(".md-modal").addClass("show");
      return false;
    },

    // This function retrieves the data for a particular option and create a form using Underscore templateing
    // and populates the input fields using data retrieved from the model (achieved using the id retrieved using
    // jQuery).
    editOption: function(e) {
      var that = this;
      var id = $(e.target).parent().parent().find("input").val();
      this.model = new App.Models.Option({optionID: id});
      this.model.fetch({
        success: function(data) {
          var template = _.template($('#option-edit-template').html());
          var vars = {option: data, id: that.options.id};
          var html = template(vars);
          $(".md-modal .md-content").html(html);
          $(".md-modal").addClass("show");
        }
      });

      return false;
    },

    create: function() {
      new saveData(new App.Models.Option(), $(".option-form"), this.collection); // Create/Update Option
      return false;
    },

    delete: function(e) {
      new deleteData(this.model, this.collection); // Remove Option
      return false;
    },

    close: function(e) {
      $(e.target).parent().removeClass("show"); // Hide Modal
    }

  });


  /*===========================================================================
                                    ROUTERS
  The code below maps the segments of the url after the hash  to functions,
  these urls can also be used to pass "id" values which are also passed to the
  below (as shown through the ":id") present.
  ============================================================================*/


  // Router
  App.Routes.Router = Backbone.Router.extend({
    routes: {
      "": "index",  // No additional url being added will call the function named index.
      "Quiz/:id" : "viewQuestion", // The specified will call the "viewQuestion" function.
      "Question/:id": "viewOption", // The specified url will call the "viewOptions" function.
    },

    index: function() {
      // Below we instatiate the Quizzes Collection and store it in the variable  named
      // "collection".
      var collection = new App.Collections.Quizzes();
      // A view is also created and stored in the "view" variable the previously created
      // collection is also passed to the Quizzes View.
      var view  = new App.Views.Quizzes({collection: collection});
    },

    viewQuestion: function(id) {
      // All events within page are removed to avoid events firing multiple times when new
      // ones are added.
      $(".page").unbind();
      // A new collection for "Questions" is created and stored in the variable named
      // "collection".
      var collection = new App.Collections.Questions();
      // A new Questions view is instantiated and both the previously made collection and
      // id present in the url are passed as parameters.
      var view  = new App.Views.Questions({collection: collection, id: id});
    },

    viewOption: function(id) {
      // All events within ".page" are destroyed to allow for new events to be created.
      $(".page").unbind();
      // A view and collection are created for the options, once again the created
      // collection and id are passed as a parameters.
      var collection = new App.Collections.Options();
      var view  = new App.Views.Options({collection: collection, id: id});
    },

  });

  // This code below instantiates Backbone Router and starts it.
  var router = new App.Routes.Router();
  Backbone.history.start();

})();
