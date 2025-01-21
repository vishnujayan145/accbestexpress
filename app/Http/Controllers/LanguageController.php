<?php

namespace App\Http\Controllers;

use App\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    //    Important properties
    public $parentModel = Language::class;
    public $parentRoute = 'language';
    public $parentView = "admin.language";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];
        if (Cache::get('total_language') && Cache::get('total_language') != null) {
            $data['total_language'] = Cache::get('total_language');
        } else {
            $data['total_language'] = $this->parentModel::count();
            Cache::put('total_language', $data['total_language']);
        }

        if (Cache::get('total_trashed_language') && Cache::get('total_trashed_language') != null) {
            $data['total_trashed_language'] = Cache::get('total_trashed_language');
        } else {
            $data['total_trashed_language'] = $this->parentModel::onlyTrashed()->count();
            Cache::put('total_trashed_language', $data['total_trashed_language']);
        }
        $items = $this->parentModel::orderBy('created_at', 'desc')->paginate(60)->onEachSide(1);
        return view($this->parentView . '.index', $data)->with('items', $items);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view($this->parentView . '.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:languages',
            'code' => 'required|alpha_dash|string|unique:languages',
            'country_name' => 'required|string'
        ]);
        $name = strtolower($request->name);
        $code = strtolower($request->code);
        if ($code == 'main') {
            Session::flash('error', 'Main is not an language name. Try another one');
            return redirect()->back();
        }
        $language_dir = resource_path() . DIRECTORY_SEPARATOR . 'lang';
        $languages_path = $language_dir . DIRECTORY_SEPARATOR . $code;
        DB::beginTransaction();
        try {
            // copy main folder and past it on language folder
            $copy_dir = $language_dir . DIRECTORY_SEPARATOR . 'main';
            File::copyDirectory($copy_dir, $languages_path);
            $this->parentModel::create([
                'name' => $name,
                'code' => $code,
                'country_name' => $request->country_name,
                'create_by' => auth()->user()->email,
            ]);
            Cache::forget('total_language');
            Cache::forget('languages');
            Session::flash('success', "Successfully  Create");
            DB::commit();
        } catch (\Exception $e) {
            if (File::isDirectory($languages_path)) {
                File::deleteDirectory($languages_path);
            }
            Session::flash('error', $e->getMessage());
            DB::rollback();
        }
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $item = $this->parentModel::find($request->id);
        if (empty($item)) {
            Session::flash('error', "Item not found");
            return redirect()->back();
        }
        return view($this->parentView . '.show')->with('items', $item);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $items = $this->parentModel::find($id);
        if (empty($items)) {
            Session::flash('error', "Item not found");
            return redirect()->back();
        }
        return view($this->parentView . '.edit')->with('item', $items);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|string|unique:languages,name,' . $id,
            'code' => 'required|string|alpha_dash|unique:languages,code,' . $id,
            'country_name' => 'required|string'
        ]);

        $name = strtolower($request->name);
        $code = strtolower($request->code);
        if ($code == 'main') {
            Session::flash('error', 'Main is not an language name. Try another one');
            return redirect()->back();
        }
        $items = $this->parentModel::find($id);
        $language_dir = resource_path() . DIRECTORY_SEPARATOR . 'lang';
        $new_path = $language_dir . DIRECTORY_SEPARATOR . $code;
        $old_path = $language_dir . DIRECTORY_SEPARATOR . $items->code;

        DB::beginTransaction();
        try {
            if ($old_path != $new_path) {
                rename($old_path, $new_path);
            }
            $items->name = $name;
            $items->code = $code;
            $items->country_name = $request->country_name;
            $items->update_by = auth()->user()->email;
            $items->save();
            Cache::forget('languages');
            Session::flash('success', "Update Successfully");
            DB::commit();
        } catch (\Exception $e) {
            if (File::isDirectory($new_path)) {
                rename($new_path, $old_path);
            }
            Session::flash('error', $e->getMessage());
            DB::rollBack();
        }
        return redirect()->route($this->parentRoute);
    }

    public function pdf(Request $request)
    {
        $item = $this->parentModel::find($request->id);
        if (empty($item)) {
            Session::flash('error', "Item not found");
            return redirect()->back();
        }
        $now = new \DateTime();
        $date = $now->format(Config('settings.date_format') . ' h:i:s');
        $extra = array(
            'current_date_time' => $date,
            'module_name' => 'Langeuage Manage'
        );
        $pdf = PDF::loadView($this->parentView . '.pdf', ['items' => $item,  'extra' => $extra])->setPaper('a4', 'landscape');
        //return $pdf->stream('invoice.pdf');
        return $pdf->download($extra['current_date_time'] . '_' . $extra['module_name'] . '.pdf');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $items = $this->parentModel::find($id);
        $items->delete_by = auth()->user()->email;
        $language_dir = resource_path() . DIRECTORY_SEPARATOR . 'lang';
        $old_path = $language_dir . DIRECTORY_SEPARATOR . $items->code;

        $items->code = $items->id . '_' . $items->code;
        $new_path = $language_dir . DIRECTORY_SEPARATOR . $items->code;

        try {
            rename($old_path, $new_path);
            $items->save();
            $items->delete();
            Cache::forget('total_trashed_language');
            Cache::forget('total_language');
            Cache::forget('languages');
            Session::flash('success', "Successfully Trashed");
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
        }
        return redirect()->back();
    }
    public function trashed()
    {
        $data = [];
        if (Cache::get('total_language') && Cache::get('total_language') != null) {
            $data['total_language'] = Cache::get('total_language');
        } else {
            $data['total_language'] = $this->parentModel::count();
            Cache::put('total_language', $data['total_language']);
        }

        if (Cache::get('total_trashed_language') && Cache::get('total_trashed_language') != null) {
            $data['total_trashed_language'] = Cache::get('total_trashed_language');
        } else {
            $data['total_trashed_language'] = $this->parentModel::onlyTrashed()->count();
            Cache::put('total_trashed_language', $data['total_trashed_language']);
        }
        $items = $this->parentModel::onlyTrashed()->paginate(60);
        return view($this->parentView . '.trashed', $data)->with("items", $items);
    }
    public function restore($id)
    {
        $items = $this->parentModel::onlyTrashed()->where('id', $id)->first();
        try {
            $items->restore();
            Cache::forget('total_trashed_language');
            Cache::forget('total_language');
            Cache::forget('languages');
            Session::flash('success', 'Successfully Restore');
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
        }
        return redirect()->back();
    }

    public function kill($id)
    {
        $items = $this->parentModel::withTrashed()->where('id', $id)->first();
        try {
            if ($items->code != 'main') {
                $language_dir = resource_path() . DIRECTORY_SEPARATOR . 'lang';
                $languages_path = $language_dir . DIRECTORY_SEPARATOR . $items->code;
                if ($items->forceDelete()) {
                    if (File::isDirectory($languages_path)) {
                        File::deleteDirectory($languages_path);
                    }
                }
            }
            Session::flash('success', 'Permanently Delete');
            Cache::forget('total_trashed_language');
            Cache::forget('total_language');
            Cache::forget('languages');
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
        }
        return redirect()->back();
    }

    public function activeSearch(Request $request)
    {
        $request->validate([
            'search' => 'min:1'
        ]);
        $data = [];
        if (Cache::get('total_language') && Cache::get('total_language') != null) {
            $data['total_language'] = Cache::get('total_language');
        } else {
            $data['total_language'] = $this->parentModel::count();
            Cache::put('total_language', $data['total_language']);
        }

        if (Cache::get('total_trashed_language') && Cache::get('total_trashed_language') != null) {
            $data['total_trashed_language'] = Cache::get('total_trashed_language');
        } else {
            $data['total_trashed_language'] = $this->parentModel::onlyTrashed()->count();
            Cache::put('total_trashed_language', $data['total_trashed_language']);
        }
        $search = $request["search"];
        $items = $this->parentModel::where('name', 'like', '%' . $search . '%')
            ->orWhere('location', 'like', '%' . $search . '%')
            ->orWhere('description', 'like', '%' . $search . '%')
            ->paginate(60);
        return view($this->parentView . '.index', $data)
            ->with('items', $items);
    }

    public function trashedSearch(Request $request)
    {
        $request->validate([
            'search' => 'min:1'
        ]);
        $data = [];
        if (Cache::get('total_language') && Cache::get('total_language') != null) {
            $data['total_language'] = Cache::get('total_language');
        } else {
            $data['total_language'] = $this->parentModel::count();
            Cache::put('total_language', $data['total_language']);
        }
        if (Cache::get('total_trashed_language') && Cache::get('total_trashed_language') != null) {
            $data['total_trashed_language'] = Cache::get('total_trashed_language');
        } else {
            $data['total_trashed_language'] = $this->parentModel::onlyTrashed()->count();
            Cache::put('total_trashed_language', $data['total_trashed_language']);
        }
        $search = $request["search"];
        $items = $this->parentModel::where('name', 'like', '%' . $search . '%')
            ->onlyTrashed()
            ->orWhere('location', 'like', '%' . $search . '%')
            ->onlyTrashed()
            ->orWhere('description', 'like', '%' . $search . '%')
            ->onlyTrashed()
            ->paginate(60);
        return view($this->parentView . '.trashed',  $data)
            ->with('items', $items);
    }

    //    Fixed Method for all
    public function activeAction(Request $request)
    {
        $request->validate([
            'items' => 'required'
        ]);
        if ($request->apply_comand_top == 3 || $request->apply_comand_bottom == 3) {
            foreach ($request->items["id"] as $id) {
                $this->destroy($id);
            }
            return redirect()->back();
        } elseif ($request->apply_comand_top == 2 || $request->apply_comand_bottom == 2) {
            foreach ($request->items["id"] as $id) {
                $this->kill($id);
            }
            return redirect()->back();
        } else {
            Session::flash('error', "Something is wrong.Try again");
            return redirect()->back();
        }
    }

    public function trashedAction(Request $request)
    {
        $request->validate([
            'items' => 'required'
        ]);
        if ($request->apply_comand_top == 1 || $request->apply_comand_bottom == 1) {
            foreach ($request->items["id"] as $id) {
                $this->restore($id);
            }
        } elseif ($request->apply_comand_top == 2 || $request->apply_comand_bottom == 2) {
            foreach ($request->items["id"] as $id) {
                $this->kill($id);
            }
            return redirect()->back();
        } else {
            Session::flash('error', "Something is wrong.Try again");
            return redirect()->back();
        }
        return redirect()->back();
    }

    /**
     * This function attatch default language 
     *
     * @author      Md. Al-Mahmud <mamun120520@gmail.com>
     * @version     1.0
     * @see         
     * @since       09/27/2022
     * Time         23:39:01
     * @param       Language $language
     * @return      
     */
    public function attatchLang(Language $language)
    {
        if (env('DEMO_MODE')) {
            Session::flash('error', 'In demo mode actions are disabled');
        } else {

            # code...   
            DB::beginTransaction();
            try {
                DB::table('languages')->where('is_default', 1)->update(['is_default' => 0]);
                $is_default = ((bool)$language->is_default) ? false : true;
                if ($language->update(['is_default' => $is_default])) {
                    if ($is_default) {
                        config(['app.locale' => $language->code]);
                    }
                    if ($is_default) {
                        Session::flash('success', 'Application default language set successfully');
                    } else {
                        Session::flash('success', 'Application default language remove successfully');
                    }
                    DB::commit();
                } else {
                    DB::rollBack();
                    Session::flash('error', 'Something is wrong');
                }
            } catch (\Exception $e) {
                DB::rollBack();
                Session::flash('error', $e->getMessage());
            }
        }
    }
    #end

    /**
     * This function 
     *
     * @author      Md. Al-Mahmud <mamun120520@gmail.com>
     * @version     1.0
     * @see         
     * @since       09/28/2022
     * Time         23:10:52
     * @param       
     * @return      
     */
    public function configureLang(Language $language)
    {
        # code...   
        $code = strtolower($language->code);
        $language_dir = resource_path() . DIRECTORY_SEPARATOR . 'lang';
        $languages_path = $language_dir . DIRECTORY_SEPARATOR . $code;
        // current root file
        $current_root_file = $languages_path . DIRECTORY_SEPARATOR . 'root.php';
        // Main rooth file
        $main_dir = $language_dir . DIRECTORY_SEPARATOR . 'main';
        $main_file_path = $main_dir . DIRECTORY_SEPARATOR . 'root.php';

        try {
            if (file_exists($current_root_file) &&  file_exists($main_file_path)) {
                // current root file
                $current_file_items = require($current_root_file);
                // root current file
                $root_file_items = require($main_file_path);
                return view('admin.language.config', compact('current_file_items', 'language', 'root_file_items'));
            }
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
            return redirect()->back();
        }
    }
    #end

    /**
     * This function store language info on file
     *
     * @author      Md. Al-Mahmud <mamun120520@gmail.com>
     * @version     1.0
     * @see         
     * @since       10/01/2022
     * Time         17:14:47
     * @param       Request $request, Language $language
     * @return      
     */
    public function configureLangStore(Request $request, Language $language)
    {
        # code...   
        $code = strtolower($language->code);

        $language_dir = resource_path() . DIRECTORY_SEPARATOR . 'lang';
        $main_dir = $language_dir . DIRECTORY_SEPARATOR . 'main';
        $main_file_path = $main_dir . DIRECTORY_SEPARATOR . 'root.php';

        $languages_path = $language_dir . DIRECTORY_SEPARATOR . $code;
        // current root file
        $current_root_file = $languages_path . DIRECTORY_SEPARATOR . 'root.php';

        try {
            if (file_exists($current_root_file) && file_exists($main_file_path)) {
                // current root file
                $current_file_items = require($current_root_file);
                // root current file
                $root_file_items = require($main_file_path);
                $php_text = "<?php " . PHP_EOL . "return[" . PHP_EOL;
                foreach ($request->except('_token') as $key => $items) {
                    $php_inner = "[" . PHP_EOL;
                    foreach ($items as $keyI => $item) {
                        $update_item = ($item) ? $item : $root_file_items[$key][$keyI];
                        $php_inner .= "'" . $keyI . "'=>'" . $update_item . "'," . PHP_EOL;
                    }
                    $php_text .= "'$key'" . "=>" . $php_inner . "]," . PHP_EOL;
                }
                $php_text .= "];" . PHP_EOL;
                file_put_contents($current_root_file, $php_text);
                Session::flash('success', 'Language configration successfuly updated');
            }
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
        }
        return redirect()->back();
    }
    #end


}
