<?php

class Rest_model extends CI_Model {

  function __construct() {
    parent::__construct();
    $this->load->database();
  }

  /* =========================================================================
	 	 This function handles returning the get request data, it takes the
     parameters $type which specifies the table name and $args which are the
     parameters for the search. The keys of $type are altered to their
     values in the database and stored within $query so the query can be run
     correctly and the results array of this query is returned.
	   =======================================================================*/

  function get($type, $args) {

    $query = array();

    // Here I map the proper url names to their database counter parts, this was
    // done to remove the need to rename individual columns within the database,
    // which would result in certain aspects ceasing to function.
    foreach($args as $key => $row) {
     if($key == "question") $key = "questionId";
     if($key == "quiz") $key = "quizId";
     if($key == "option") $key = "optionID";
     if($key == "image" && $type == "question") $key = "questionImage";
     if($key == "image" && $type == "quiz")     $key = "quizImage";
     if($key == "title"  && $type == "question") {
       $key = "questionName";
       $row = urldecode($row) + "?";
     }
     if($key == "title"  && $type == "quiz") {
        $key = "quizName";
        $row = urldecode($row);
     }

     if($key == "name"  && $type == "option") {
        $key = "questionName";
        $row = urldecode($row);
     }
     if($row != false) $query[$key]  = $row;
   }

    // A where query is run using $querys key and values as parameters
    // for the search and $type is used to specify the table for the search.
    $this->db->where($query);
    $result = $this->db->get($type);

    // The results array is stored within $return and if only a single row is
    // returned only the first index is returned (thus removing the '[]').
    $return = $result->result_array();
    return ($result->num_rows() == 1) ? $return[0] : $return;

  }

  /* =========================================================================
     This function handles inserting data from the POST request. $type and
     $args are passed and identifies the table, whilst $args contains the data
     to be inserted, once the insert query is run and successful the data
     inserted is returned by this function, if it is unsuccessful "fail"
     is returned.
	   =======================================================================*/

  function insert($type, $args) {

    // An insert query is run with $type indentifying the data and $args containing
    // the data to be inserted.
    $res = $this->db->insert($type, $args);

    // If the query if a success (affected_rows() is more than 0) then the data
    // that has been inserted will be returned else "failed" shall be returned.
    return ($this->db->affected_rows() > 0) ? $args : "failed";

  }

  /* =========================================================================
     This function is called on delete and $type and $args are passed as
     parameters, $type once again identifies the table, whilst $args contains
     the primary key of the row to be deleted, these are both passed to the
     database delete function and once successful the deleted rows details
     are returned.
     =======================================================================*/

  function delete($type, $args) {

    // Rather than altering the database key names I have decided to alter them
    // via if statements mapping the ones in the api to their original values.
    // These keys are altered and stored into an array named $query.
    $query = array();
    foreach($args as $key => $row) {
      if($key == "question") $key = "questionId";
      if($key == "quiz")     $key = "quizId";
      if($key == "option")     $key = "optionID";
      if($row != false)      $query[$key]  = $row;
    }

    // The delete database function is gone the id stored in the $query array
    // identifies the record to be deleted and $type identifies the table it is
    // stored within.
    $this->db->delete($type, $query);

    // "Success" or "Failed" are returned depending if the delete query executed
    // successfully this is decided if 'affected_rows()' is more than 0;
    return ($this->db->affected_rows() > 0) ? "success" : "failed";

  }


  /* =========================================================================
	 	 Lastly the update function handles updating an existing row with new data
     $args contains this data. The $id is derived from $args and this along
     with type are used in the where query to find the row at hand and once
     found $type and $args are used to identify the table and insert data
     respectively through the use the update database function.
	   =======================================================================*/

  function update($type, $args) {

    // $type has been used to derive the primary key field of the table at hand, thus
    // allowing data to be updated correctly e.g. the quiz tables primary key would be
    // quizId, whilst the option tables would be optionID.
    $id = ( $type == "quiz")     ? 'quizId'
        : (($type == "question") ? 'questionId'
        : (($type == "option")   ? 'optionID' : ''));

    // $id is used to identify the primary key name and is also used to access the id
    // number stored within the $args array which is used to query the database.
    $this->db->where($id, $args[$id]);

    // Lastly, the update query is run with $type holding the tables name in which the
    // record lies and $args containing the new information to be added.
    $this->db->update($type, $args);

    // If the update query was correct run (i.e. the affected_rows() is more than 0)
    // the inserted data will be returned else, "failed" will be returned.
    return ($this->db->affected_rows() > 0) ? $args : "failed";

  }

}

?>
