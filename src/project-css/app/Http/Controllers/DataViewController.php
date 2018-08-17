<?php

namespace App\Http\Controllers;

use Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Table;
use App\Libraries\CustomStringHelper;
use App\Libraries\FileViewHelper;
use App\Libraries\FullTextSearchFormatter;
use App\Libraries\TableHelper;

/**
 * The controller is responsible for showing the cards data
 */
class DataViewController extends Controller {
    // various error messages
    public $tableNoRecordsErr = 'Table does not have any records.';
    public $invalidRecordIdErr = 'Invalid Record ID';
    public $noResultsErr = 'Search Yeilded No Results';
    public $invalidSearchStrErr = 'Invalid Search String';

    /**
     * Constructor that associates the middlewares
     */
    public function __construct() {
        // Middleware to check for authenticated
        $this->middleware('auth');
    }

    /**
     * Show the data from the selected table
     */
    public function index($curTable) {
        $table = Table::findOrFail($curTable);

        // check for the number of records
        if ($table->recordCount() == 0) {
          return redirect()->route('home')->withErrors([ $this->tableNoRecordsErr ]);
        }

        // return the index page
        return view('user.data')->with('rcrds', $table->getPage(30))
                                ->with('clmnNmes', $table->getColumnList())
                                ->with('tblNme', $table->tblNme)
                                ->with('tblId', $curTable);
    }

    /**
     * Show a record in the table
     */
    public function show($curTable, $curId) {
        // Check if id is valid
        if (is_null($curId) || !is_numeric($curId)) {
           return redirect()->back()->withErrors([ $this->invalidRecordIdErr ]);
        }

        // Get the table entry in meta table "tables"
        $table = Table::findOrFail($curTable);

        // query database for record
        $rcrds = $table->getRecord($curId);

        // check for the number of records if their is none return with error message
        if (count($rcrds) == 0) { return redirect()->back()->withErrors([ $this->noResultsErr ]); }

        // return the index page
        return view('user.show')->with('rcrds', $rcrds)
                                ->with('clmnNmes', $table->getColumnList())
                                ->with('tblNme', $table->tblNme)
                                ->with('tblId', $curTable)
                                ->with('curId', $curId);
    }

    public function search(Request $request, $curTable, $page = 1) {
        // Get the table entry in meta table "tables"
        $table = Table::findOrFail($curTable);

        if ($request->input('search') != NULL) {
          $search = $request->input('search');
          $request->session()->put('search', $search);
        }
        else {
            $search = $request->session()->get('search');
        }

        $srchStrng = (new FullTextSearchFormatter)->prepareSearch($search);

        // set records per page
        $perPage = 30;

        $rcrds = $table->fullTextQuery($srchStrng, $page, 30);

        $rcrdsCount = count($rcrds);

        // if last query returned exactly 30 items
        // we assume that their are additional pages
        // so we set $lastPage to $page + 1
        $lastPage = ($rcrdsCount == $perPage) ? $page + 1 : $page;

        if ($rcrdsCount == 0) {
          return view('user.search')->with('tblId', $curTable)
                                    ->with('tblNme', $table->tblNme)
                                    ->with('page', $page)
                                    ->with('lastPage', $lastPage)
                                    ->with('rcrds', $rcrds)
                                    ->withErrors([ $this->noResultsErr ]);
        }

        return view('user.search')->with('rcrds', $rcrds)
                                  ->with('clmnNmes', $table->getColumnList())
                                  ->with('tblNme', $table->tblNme)
                                  ->with('tblId', $curTable)
                                  ->with('page', $page)
                                  ->with('lastPage', $lastPage)
                                  ->with('morepages', $page<$lastPage);
    }

    /**
     * view checks the file type and if its plain text or a word document it
     * will run ssnRedact to replace a US style social security number with
     * ###-##-####. Any other files the user will be able to download and then
     * view with a local application.
     */
    public function view($curTable, $recId, $filename) {
      // Get the table entry in meta table "tables"
        $table = Table::findOrFail($curTable);

        $path = (new FileViewHelper)->getFilePath($curTable, $recId, $filename);

        $source = storage_path('app/'.$path);
        $fileMimeType = Storage::getMimeType($path);

        if ((new FileViewHelper)->isSupportedMimeType($fileMimeType)) {
          return view('user.fileviewer')->with('fileContents', (new FileViewHelper)->getFileContents($source))
                                        ->with('tblNme', $table->tblNme)
                                        ->with('tblId', $curTable)
                                        ->with('recId', $recId);
        }
        else {
          // download file if we cannot determine what kind of file it is.
          return Response::make(file_get_contents($source), 200, [
             'Content-Type' => $fileMimeType,
             'Content-Disposition' => 'inline; filename="'.$filename.'"'
         ]);
        }
    }

}
