<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Collection;
use App\User;
use App\Table;
use Auth;

class UploadController extends Controller{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
        $this->middleware('auth');
    }

    /**
    * Show the table index page
    */
    public function index($curTable) {
      // Get the table entry in meta table "tables"
      $curTable = Table::find($curTable);
      if(!$curTable->hasAccess){
        return redirect()->route('home')->withErrors(['Table is disabled']);
      }

      // return the index page
      return view('admin/upload')->with('tblNme',$curTable->tblNme)
                                 ->with('tblId',$curTable);
    }

    /**
     *  Store Files
     *
     * @return Redirect
     */
    public function storeFiles(Request $request, $curTable)
    {
      // Get the table entry in meta table "tables"
      $curTable = Table::find($curTable);
      if(!$curTable->hasAccess){
        return redirect()->route('home')->withErrors(['Table is disabled']);
      }

      // Request the file input named 'attachments'
      $files = $request->file('attachments');
      $upFldNme = $request->upFldNme;

      //If the array is not empty
      if ($files[0] != '') {
        foreach($files as $file) {
          // Set the destination path
          $destinationPath = $curTable->tblNme . '/' . $upFldNme;
          Storage::makeDirectory($destinationPath);
          // Get the orginal filname or create the filename of your choice
          $filename = $file->getClientOriginalName();
          // Copy the file in our upload folder
          //$file->move($destinationPath, $filename);
          $file->storeAs($destinationPath,$filename);
        }
      }
      // Retrun a redirection or a view
      return redirect('table');
    }
}