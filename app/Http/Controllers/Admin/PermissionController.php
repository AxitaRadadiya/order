<?php

namespace App\Http\Controllers\Admin;

use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Permission checks handled via local role/permission models; remove Spatie middleware dependency.
    }

    public function index(): View
    {
        return view('admin.permissions.index', [
            'permissions' => Permission::orderBy('id','DESC')->paginate(15)
        ]);
    }

    public function create(): View
    {
        return view('admin.permissions.create', [
            'permissions' => Permission::get()
        ]);
    }


    public function store(Request $request): RedirectResponse
    {
        $this->validate(request(), [
            'name' => 'required|unique:permissions'
        ]);
        $permissions = Permission::create(['name' => $request->name]);
        return redirect()->route('permissions.index')
                ->withSuccess('New Permission is added successfully.');
    }


    public function edit(string $id)
    {
        $permission = Permission::findOrFail($id);
        return view('admin.permissions.edit', compact('permission'));
    }


    public function update(Request $request, string $id)
    {
        $this->validate(request(), [
            'name' => 'required|unique:permissions,name,'.$id
        ]);
        $permission = Permission::findOrFail($id);
        $permission->name = $request->name;
        $permission->save();
        return redirect()->route('permissions.index')
                ->withSuccess('Permission is updated successfully.');
    }


    public function destroy(string $id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();
        return redirect()->route('permissions.index')
                ->withSuccess('Permission is deleted successfully.');

    }

    public function permissionsList(Request $request)
    {
        $columns = array( 
            0 =>'id', 
            1 =>'name',
            2 =>'action'          
        );  
        $totalData = Permission::count();            
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        
        $search = $request->input('search.value');
        if(empty($search))
        {   
            $institutes = Permission::offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
        }else{
            $institutes = Permission::where('name', 'like', "%{$search}%")
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
        }


        $data = array();
        if(!empty($institutes))
        {
            $i = 1;
            foreach ($institutes as $key=>$institute)
            {
               
                $nestedData['id'] = $i;
                $nestedData['name'] = $institute->name;
                  
                $i++;
                 
                $nestedDataEdit = '';
                $nestedDataDelete = '';
                
                if (auth()->user()->can('permissions-edit')) {                
                    $nestedDataEdit = '<a href="'.route('permissions.edit',$institute->id).'" class="btn btn-success btn-sm"><i class="fa fa-edit"></i></a> &nbsp;';
               }

               if (auth()->user()->can('permissions-delete')) { 
    
                $nestedDataDelete = '<form action="' . route('permissions.destroy', $institute->id) . '" method="POST" class="deleteForm d-inline">
                                ' . csrf_field() . '
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-danger btn-sm deleteButton">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                            &nbsp;';
                }
               
                $nestedData['action'] ="$nestedDataEdit"."$nestedDataDelete";
                
                $data[] = $nestedData;
            }
 
        }
        //print_r($data);die;
        $json_data = array(
        "draw"            => intval($request->input('draw')),  
        "recordsTotal"    => intval($totalData),  
        "recordsFiltered" => intval($totalFiltered), 
        "data"            => $data
        );            
        echo json_encode($json_data);
    }
}
