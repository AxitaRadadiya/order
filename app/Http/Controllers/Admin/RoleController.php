<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
   
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if ($user && $user->hasRole(['super-admin', 'admin', 'distributor'])) {
                return $next($request);
            }
            abort(403, 'Unauthorized action.');
        });
    }

    public function index()
    {
        $user = auth()->user();

        // super-admin/admin sees:
        //  1) default roles
        //  2) ALSO roles created by super-admin themselves
        $isDefaultViewer = $user && $user->hasRole(['super-admin', 'admin']);

        $roles = Role::query()
            ->when($isDefaultViewer, function ($q) use ($user) {
                $defaultRoleNames = ['accounts', 'production', 'sales executive', 'sales manager', 'distributor', 'retailer', 'admin', 'super-admin'];

                $q->where(function ($qq) use ($defaultRoleNames, $user) {
                    $qq->whereIn('name', $defaultRoleNames)
                       ->orWhere('created_by', $user->id);
                });
            })
            ->when(!$isDefaultViewer && $user && $user->hasRole('distributor'), function ($q) use ($user) {
                $q->where('created_by', $user->id);
            })
            ->select('id')
            ->get();


        return view('admin.roles.index', compact('roles'));
    }

   
    public function roleList(Request $request)
    {
        $columns = [0 => 'id', 1 => 'name'];

        $limit     = (int) $request->input('length', 10);
        $start     = (int) $request->input('start',  0);
        $order     = $columns[$request->input('order.0.column')] ?? 'id';
        $dir       = in_array($request->input('order.0.dir'), ['asc','desc'])
                        ? $request->input('order.0.dir') : 'desc';
        $search    = trim((string) $request->input('search.value', ''));

        $query = Role::with('permissions');

        $user = auth()->user();
        $isSuperAdminPanel = $user && $user->hasRole(['super-admin', 'admin']);

        if ($isSuperAdminPanel) {
            // super-admin/admin can see:
            $defaultRoleNames = ['accounts', 'production', 'sales executive', 'sales manager', 'distributor', 'retailer', 'admin', 'super-admin'];

            $query->where(function ($qq) use ($defaultRoleNames, $user) {
                $qq->whereIn('name', $defaultRoleNames)
                   ->orWhere('created_by', $user->id);
            });

            $totalData = Role::where(function ($qq) use ($defaultRoleNames, $user) {
                $qq->whereIn('name', $defaultRoleNames)
                   ->orWhere('created_by', $user->id);
            })->count();
        } elseif ($user && $user->hasRole('distributor')) {

            // distributor can only see roles created by them
            $query->where('created_by', $user->id);
            $totalData = Role::where('created_by', $user->id)->count();
        } 
        // else {
        //     // other non-superadmin users: only show roles created by them
        //     $query->where('created_by', $user->id);
        //     $totalData = Role::where('created_by', $user->id)->count();
        // }



        if ($search !== '') {
            $query->where('name', 'like', "%{$search}%");
        }

        $totalFiltered = $query->count();

        $roles = (clone $query)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();

        $data = [];
        foreach ($roles as $i => $role) {

            // Permission badges
            $permHtml = $role->permissions->isEmpty()
                ? '<span class="text-muted" style="font-size:.78rem;">—</span>'
                : $role->permissions
                    ->map(fn($p) => '<span class="badge badge-info mr-1">' . e($p->name) . '</span>')
                    ->implode('');

            // Action buttons
            $actions = '';
            if (auth()->user() && $role->name !== 'super-admin') {
            if (auth()->user()) {
                $actions .= '<a href="' . route('roles.edit', $role->id) . '" class="btn btn-sm btn-info mr-1"><i class="fa fa-edit"></i></a>';
            }}

            $data[] = [
                'id'          => $start + $i + 1,
                'name'        => '<strong>' . e($role->name) . '</strong>',
                'permissions' => $permHtml,
                'action'      => $actions ?: '<span class="text-muted">—</span>',
            ];
        }

        return response()->json([
            'draw'            => (int) $request->input('draw'),
            'recordsTotal'    => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data'            => $data,
        ]);
    }

    /* ─────────────────────────────────────────
     | CREATE
     ───────────────────────────────────────── */
    public function create()
    {
        $permsQuery = Permission::orderBy('name')->get()
            ->reject(function($p) { return strtolower($p->name) === 'all-modules'; });

        if (auth()->user()->hasRole('distributor')) {
            $permsQuery = $permsQuery->filter(function($p) {
                $name = strtolower($p->name);
                return str_starts_with($name, 'customer-') || str_starts_with($name, 'order-') || str_starts_with($name, 'catalog-') || $name === 'catalog';
            });
        }

        $permissions = $permsQuery->groupBy(function ($p) {
            $parts = explode('-', $p->name);
            return ucfirst($parts[0]);
        });

        return view('admin.roles.create', compact('permissions'));
    }

    /* ─────────────────────────────────────────
     | STORE
     ───────────────────────────────────────── */
    public function store(Request $request)
    {
        $request->validate([
            'name'          => ['required', 'string', 'max:100', 'unique:roles,name'],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        if (auth()->user()->hasRole('distributor')) {
            $allowedPermIds = Permission::where(function($q) {
                $q->where('name', 'like', 'customer-%')
                  ->orWhere('name', 'like', 'order-%')
                  ->orWhere('name', 'like', 'catalog-%')
                  ->orWhere('name', 'catalog');
            })->pluck('id')->toArray();

            $requestedPerms = $request->input('permissions', []);
            if (array_diff($requestedPerms, $allowedPermIds)) {
                return redirect()->back()->withErrors(['permissions' => 'Unauthorized permissions selected.'])->withInput();
            }
        }

        $role = Role::create(['name' => $request->name]);

        if ($request->filled('permissions')) {
            $role->permissions()->sync($request->input('permissions'));
        }

        return redirect()->route('roles.index')
            ->withSuccess('Role "' . $role->name . '" created successfully.');
    }

    /* ─────────────────────────────────────────
     | SHOW  (JSON only — used by API if needed)
     ───────────────────────────────────────── */
    public function show(Role $role)
    {
        return response()->json($role->load('permissions'));
    }

    /* ─────────────────────────────────────────
     | EDIT
     ───────────────────────────────────────── */
    public function edit(Role $role)
    {
        if (auth()->user()->hasRole('distributor') && $role->created_by !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $permsQuery = Permission::orderBy('name')
            ->get()
            ->reject(function($p) { return strtolower($p->name) === 'all-modules'; });

        if (auth()->user()->hasRole('distributor')) {
            $permsQuery = $permsQuery->filter(function($p) {
                $name = strtolower($p->name);
                return str_starts_with($name, 'customer-') || str_starts_with($name, 'order-') || str_starts_with($name, 'catalog-') || $name === 'catalog';
            });
        }

        $permissions = $permsQuery->groupBy(function ($p) {
            $parts = explode('-', $p->name);
            return ucfirst($parts[0]);
        });

        $assignedIds = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'assignedIds'));
    }

    /* ─────────────────────────────────────────
     | UPDATE
     ───────────────────────────────────────── */
    public function update(Request $request, Role $role)
    {
        if (auth()->user()->hasRole('distributor') && $role->created_by !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name'          => ['required', 'string', 'max:100', 'unique:roles,name,' . $role->id],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        if (auth()->user()->hasRole('distributor')) {
            $allowedPermIds = Permission::where(function($q) {
                $q->where('name', 'like', 'customer-%')
                  ->orWhere('name', 'like', 'order-%')
                  ->orWhere('name', 'like', 'catalog-%')
                  ->orWhere('name', 'catalog');
            })->pluck('id')->toArray();

            $requestedPerms = $request->input('permissions', []);
            if (array_diff($requestedPerms, $allowedPermIds)) {
                return redirect()->back()->withErrors(['permissions' => 'Unauthorized permissions selected.'])->withInput();
            }
        }

        $role->update(['name' => $request->name]);
        $role->permissions()->sync($request->input('permissions', []));

        return redirect()->route('roles.index')
            ->withSuccess('Role "' . $role->name . '" updated successfully.');
    }

    /* ─────────────────────────────────────────
     | DESTROY
     ───────────────────────────────────────── */
    public function destroy(Role $role)
    {
        if (auth()->user()->hasRole('distributor') && $role->created_by !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $role->permissions()->detach();
        $role->delete();

        return redirect()->route('roles.index')
            ->withSuccess('Role deleted successfully.');
    }
}
