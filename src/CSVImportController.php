<?php
namespace RTMatt\CSVImport;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use RTMatt\CSVImport\Exceptions\CSVImporterNotFoundException;
use RTMatt\CSVImport\Exceptions\CSVImportInvalidLayoutException;
use RTMatt\CSVImport\Exceptions\CSVIncompatibleUserException;

class CSVImportController extends Controller
{

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $config;


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

        $layout = 'csvimport::layout';
        if ($layout_override = config('csvimport.override_layout_view')) {
            $layout = $this->validateLayoutOverride($layout_override);
        }
        if (\File::exists(base_path('resources/views/layouts/admin.blade.php'))) {
            $layout = 'layouts.admin';
        }

        try {
            $raw_fields = $this->getAvailableImporters();
        } catch (\RTMatt\CSVImport\Exceptions\CSVDirectoryNotFoundExcepton $e) {
            $fields = [ ];
            return view('csvimport::index', compact('fields', 'layout'))->withErrors($e->getMessage());
        }

        $fields = $this->translateImporterFields($raw_fields);

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
            $importer_identifier = config('csvimport.importer_namespace') . studly_case($key) . "Importer";
            if ( ! class_exists($importer_identifier)) {
                throw new CSVImporterNotFoundException("Class " . $importer_identifier . " does not exist");
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


    protected function authorizeUser()
    {
        $user = $this->getCurrentUser();
        if ( ! method_exists($user, 'can_import')) {
            throw new CSVIncompatibleUserException('Incompatible user model.  can_import method needs to be defined');
        }
        if ($user->can_import()) {
            return;
        }
        abort(401, 'Unauthorized action.');
    }


    protected function getAvailableImporters()
    {
        $directory = config('csvimport.importer_directory');

        return CSVImportDirectoryReader::readDirectory($directory);

    }


    private function translateImporterFields($raw_fields)
    {
        $fields = [ ];
        foreach ($raw_fields as $importerName) {
            $clean_name = str_ireplace('Importer.php', '', $importerName);
            $fields[]   = snake_case($clean_name);
        }

        return $fields;

    }


    /**
     * @return mixed
     */
    protected function getCurrentUser()
    {
        return \Auth::user();
    }


    private function validateLayoutOverride($layout_override)
    {
        $base_path = base_path('resources/views/');

        $override_path = $base_path . str_replace('.', '/', $layout_override) . '.blade.php';
        if ( ! \File::exists($override_path)) {
            throw new CSVImportInvalidLayoutException('Layout override file does not exist');
        }

        return $layout_override;

    }

}