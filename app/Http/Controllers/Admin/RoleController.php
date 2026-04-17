<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
   
    public function index()
    {
        $roles = Role::select('id')->get();

        return view('admin.roles.index', compact('roles'));
    }

   
    public function roleList(Request $request)
    {
        $columns = [0 => 'id', 1 => 'name'];

        $totalData = Role::count();
        $limit     = (int) $request->input('length', 10);
        $start     = (int) $request->input('start',  0);
        $order     = $columns[$request->input('order.0.column')] ?? 'id';
        $dir       = in_array($request->input('order.0.dir'), ['asc','desc'])
                        ? $request->input('order.0.dir') : 'desc';
        $search    = trim((string) $request->input('search.value', ''));

        $query = Role::with('permissions');

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
            if (auth()->user()) {
                $actions .= '<a href="' . route('roles.edit', $role->id) . '" class="btn btn-xs btn-primary mr-1"><i class="fas fa-pen"></i> Edit</a>';
            }

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
        // Anyone authenticated can access role creation (removed super-admin restriction)
        // Group permissions by prefix (e.g. "user-list" → group "User")
        $permissions = Permission::orderBy('name')->get()
            ->groupBy(function ($p) {
                // Take first segment before "-" as the group label
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
        // removed super-admin only restriction for storing roles
        $request->validate([
            'name'          => ['required', 'string', 'max:100', 'unique:roles,name'],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

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
        // removed super-admin only restriction for editing roles
        $permissions = Permission::orderBy('name')->get()
            ->groupBy(function ($p) {
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
        // removed super-admin only restriction for updating roles
        $request->validate([
            'name'          => ['required', 'string', 'max:100', 'unique:roles,name,' . $role->id],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

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
        // removed super-admin only restriction for deleting roles
        $role->permissions()->detach();
        $role->delete();

        return redirect()->route('roles.index')
            ->withSuccess('Role deleted successfully.');
    }
}
