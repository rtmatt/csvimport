<?php
namespace RTMatt\CSVImport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CSVImportController extends Controller
{

    public function __construct()
    {
        if (config('csvimport.auth')) {
            $this->authorizeUser();
        }

    }


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getIndex()
    {
        $fields = $this->getDefinedImporterFields();
        $layout = 'csvimport::layout';
        if (\File::exists(base_path('resources/views/layouts/admin.blade.php'))) {
            $layout = 'layouts.admin';
        }

        return view('csvimport::index', compact('fields', 'layout'));
    }


    /**
     * Run Imports on Provided Content
     *
     * @param  Request $request
     *
     * @return Response
     */
    public function postIndex(Request $request)
    {
        $import_manager = new CSVImportManager(config('csvimport.import_order'));
        foreach ($request->file() as $key => $csv) {
            $importer_identifier = "\\App\\CSVImports\\" . studly_case($key) . "Importer";
            if ( ! class_exists($importer_identifier)) {
                return redirect()->back()->withErrors($importer_identifier . " does not exist");
            }
            $importer = new $importer_identifier($csv);
            $import_manager->queue($importer, $key);
        }
        $message = $import_manager->run();

        if ($message != '') {
            return redirect()->back()->with([ 'flash_message' => $message ]);
        } else {
            return redirect()->back()->withErrors('No Files Uploaded');
        }

    }


    /**
     * @return array
     */
    protected function getDefinedImporterFields()
    {
        $directory      = app_path('CSVImports');
        $importer_files = array_diff(scandir($directory), [ '.', '..' ,'.gitkeep']);
        $fields         = [ ];
        foreach ($importer_files as $importerName) {
            $clean_name = str_ireplace('Importer.php', '', $importerName);
            $fields[]   = snake_case($clean_name);

        }

        return $fields;
    }


    protected function authorizeUser()
    {
        $user = \Auth::user();
        if(!method_exists($user, 'can_import')){
            throw new CSVIncompatableUserException('Incompatable user model.  can_import method needs to be defined');
        }
        if ($user->can_import()) {
            return;
        }
        abort(401, 'Unauthorized action.');
    }
}