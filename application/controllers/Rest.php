<?php
class Rest extends CI_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Rest_model');
  }

  /* =========================================================================
     _remap is called upon the controller load and is used to pass the URL
     after the controller name as an argument so it can be manipulated. This
     function handles the request types of: GET, POST, PUT and DELETE and loads
     the appropriate functions.
     =======================================================================*/

  public function _remap() {

    // The method being requested is identified and the correct function
    // is called as a result, this is achieved via IF statements.
    $method = $this->input->server('REQUEST_METHOD');
    if($method == "GET")  $this->get();
    if($method == "POST")  $this->post();
    if($method == "PUT")  $this->put();
    if($method == "DELETE")  $this->delete();

  }

  /* =========================================================================
     The 'get' function will be called when the request method is 'GET', this
     function converts the url into an associative array which is then used
     to retrieve results from the database, these results are then returned
     in JSON format.
		 =======================================================================*/

  public function get() {

    // I convert the URL to an associative array with every two values being a
    // key and value pair.
    $args = $this->uri->uri_to_assoc(2);

    // The resource value is retrieved and stored within the $type variable so
   	// it is known whatthe user is trying to access (questions, Quiz or option).
   	// This is then removed from the  array so the same array can be can be used
    //  at a later point.
    $type = $args["resource"];
    unset($args["resource"]);

    // $args and $type into the 'get' function which returns an assosiative
    // array which is then stored in $res and is converted to json and echoed.
    $res = $this->Rest_model->get($type, $args);
    echo json_encode($res);

  }

  /* =========================================================================
     The 'post' function will be called when the request method is 'POST',
     this function will determine where the data is being posted to through
     the resource specified in the url and will then pass the data to the
     insert method within the model which will insert the query and return
     the data that was inserted which will then be returned in JSON format.
		 =======================================================================*/

  public function post() {

    // The variables used within this function are initialised here, $notJSON stores
    // the form post, whilst $args stores an associative array derived from the URL
    // and $type includes the resource being posted to (question, option or quiz).
    $notJSON = $this->input->post();
    $args = $this->uri->uri_to_assoc(2);
    $type = $args["resource"];
    $post = "";

    // isJSON is used to determine whether the post request is from a form post. If
    // this amounts to false it means the data is not from a form and instead from
    // backbone in JSON format as such an alternate method of retrieving the data
    // can be used and the data can be decoded from its JSON format.
    if($notJSON) {
      $args = $notJSON;
    } else {
      // 'php://input' is used to retrieve all the raw data passed via post.
      // $post = file_get_contents("php://input");
      $post = file_get_contents("php://input");
      $args = json_decode($post);
    }

    // $args and $type into the 'insert' function which inserts a row into the
    // contsining the data in $args into table specified in $type
    $res = $this->Rest_model->insert($type, $args);

    // The inserted data is returned back in JSON format in case the data needs
    // to be manipulated
    echo json_encode($res);

  }

  /* =========================================================================
      A put function refers to updating a record as such this function will
     be called when the request type is 'PUT' once again the url will be used
     to determine where the data is being PUT to and the data to be inserted
     is retrieved from the raw post data and is passed to the update function
     which will update the records and return the data that has been updating
     which will be returned back in JSON format.
		 =======================================================================*/

  public function put() {

    // Variables are once again initialised the url converted into an associative array
    // array and the resource being access is stored within $type.
    $args = $this->uri->uri_to_assoc(2);
    $type = $args["resource"];

    // put stores the data being passed via post and this is decoded and stored in
    // args which is passed to $res which update the record.
    $put  = file_get_contents("php://input");
    $args = json_decode($put, true);
    $res = $this->Rest_model->update($type, $args);

    // Success is echoed in JSON format if no errors are returned.
    echo json_encode($res);

  }

  /* =========================================================================
     The delete function is called when the request method is 'DELETE', this
     will use the id retrieved from the post data and the type retrieved from
     the url which are passed to the 'delete' function to remove the specific
     row from the database. The delete function will return the delete row,
     which will be returned via JSON.
		 =======================================================================*/

  public function delete() {

    // The resource in which a row is being deleted is defined by converting the
    // URL to an associative array, the resource is then stored within $type and
    // this key and value are removed from the $args array.
    $args = $this->uri->uri_to_assoc(2);
    $type = $args["resource"];
    unset($args["resource"]);

    // $type and $args are passed to the delete function with $type containing the
    // table to delete from and $args contains id of the record being deleted.
    $res = $this->Rest_model->delete($type, $args);

    // Echo the returned value (either "success or failed") based on the execution
    // of the delete statement, this can be used to act accordingly by the
    // application.
    echo json_encode($res);

  }

}

?>
