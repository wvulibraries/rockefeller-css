<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Import the table and collection models
use App\Table;
use App\Collection;
use Illuminate\Support\Facades\Auth;

/**
* The controller is responsible for showing the cards data
*/
class DataViewController extends Controller {

  /**
   * Constructor that associates the middlewares
   *
   * @return void
   */
  public function __construct(){
    // Middleware to check for authenticated
    $this->middleware('auth');
  }

  /**
  * Show the data from the selected table
  */
  public function index(Request $request, $curTable){
    // Get the table entry in meta table "tables"
    $curTable = Table::find($curTable);
    if(!$curTable->hasAccess){
      return redirect()->route('home')->withErrors(['Table is disabled']);
    }

    /**
    * set passed values to variables
    */
    $search = $request->input('search');
    $tblCol = $request->input('tblCol');
    $id = $request->input('id');

    if ((strlen($search) != '0') && (strlen($tblCol) != '0')) {
      $numOfRcrds = DB::table($curTable->tblNme)
                      ->where($tblCol, 'LIKE', $search)
                      ->count();
    }
    else {
      $numOfRcrds = DB::table($curTable->tblNme)->count();
    }

    // check for the number of records
    if ($numOfRcrds == 0){
      return redirect()->route('home')->withErrors(['Table does not have any records.']);
    }

    // Get the records of the table using the name of the table we are currently using
    if ((strlen($search) != '0') && (strlen($tblCol) != '0')) {
      $rcrds = DB::table($curTable->tblNme)
                  ->where($tblCol, 'LIKE', $search)
                  ->paginate(30);
      $rcrds->appends(array(
          'tblCol' => $tblCol,
          'search' => $search,
      ));
    }
    // elseif (strlen($id) != '0') {
    //   $rcrds = DB::table($curTable->tblNme)
    //               ->where('id', 'LIKE', $id);
    // }
    else {
      $rcrds = DB::table($curTable->tblNme)->paginate(30);
    }

    // retrieve the column names
    $clmnNmes = DB::getSchemaBuilder()->getColumnListing($curTable->tblNme);

    // return the index page
    return view('user.data')->with('rcrds',$rcrds)
                            ->with('clmnNmes',$clmnNmes)
                            ->with('tblNme',$curTable->tblNme)
                            ->with('tblId',$curTable);
  }




}