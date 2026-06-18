<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class UserController extends Controller
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

    public function index(): View
    {
        $query = User::with(['role']);

        if (auth()->user()->hasRole('super-admin')) {
            $query->where(function ($q) {
                $q->where('created_by', auth()->id())
                  ->orWhereNull('created_by');
            });
            $excludeRoleIds = Role::whereIn('name', ['retailer', 'distributor'])->pluck('id')->toArray();
            $query->when(!empty($excludeRoleIds), fn($q) => $q->whereNotIn('role_id', $excludeRoleIds));
        } elseif (auth()->user()->hasRole('distributor')) {
            $query->where('created_by', auth()->id());
        } else {
            $query->where('created_by', auth()->id());
            $excludeRoleIds = Role::whereIn('name', ['retailer', 'distributor'])->pluck('id')->toArray();
            $query->when(!empty($excludeRoleIds), fn($q) => $q->whereNotIn('role_id', $excludeRoleIds));
        }

        return view('admin.users.index', [
            'users' => $query->orderBy('id')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->storeRules());

        $payload = $this->userPayload($request, $validated);

        $user = User::create($payload);

        $user->assignRole((int) $validated['role_id']);

        $loginUser = Auth::user();
        Log::info('user.created', [
            'actor_id'   => $loginUser->id,
            'actor_name' => $loginUser->name,
            'user_id'    => $user->id,
            'user_name'  => $user->name,
            'properties' => $request->except(['_token', 'password', 'password_confirmation']),
        ]);

        return redirect()->route('users.index')
            ->withSuccess('New user added successfully.');
    }

    public function show(User $user): View
    {
        // Super-admin/admin can view any user. Distributors are limited to their own created users.
        if ($user->created_by !== auth()->id()) {
            if (! auth()->user()->hasRole(['super-admin', 'admin'])) {
                abort(403, 'Unauthorized action.');
            }
        }


        $user->load(['role']);

        // Fetch all users created by this profile user
        $createdUsers = User::with('role')
            ->where('created_by', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.users.show', [
            'user'         => $user,
            'createdUsers' => $createdUsers,
        ]);
    }

    public function edit($id): View
    {
        $user = User::with(['role'])->findOrFail($id);

        // Super-admin/admin can edit any user. Distributors are limited to their own created users.
        if ($user->created_by !== auth()->id()) {
            if (! auth()->user()->hasRole(['super-admin', 'admin'])) {
                abort(403, 'Unauthorized action.');
            }
        }

        return view('admin.users.edit', array_merge(

            ['user' => $user],
            $this->formData($user)
        ));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        // Super-admin/admin can update any user. Distributors are limited to their own created users.
        if ($user->created_by !== auth()->id()) {
            if (! auth()->user()->hasRole(['super-admin', 'admin'])) {
                abort(403, 'Unauthorized action.');
            }
        }


        $validated = $request->validate($this->rules($user));

        $data = $this->userPayload($request, $validated, $user);

        $original = $user->getOriginal();

        $user->update($data);
        $user->assignRole((int) $validated['role_id']);

        $changes = [];
        foreach ($data as $field => $newVal) {
            if ($field === 'password') {
                continue;
            }

            if (array_key_exists($field, $original) && (string) $original[$field] !== (string) $newVal) {
                $changes[$field] = ['old' => $original[$field], 'new' => $newVal];
            }
        }

        $loginUser = Auth::user();
        if (! empty($changes)) {
            Log::info('user.updated', [
                'actor_id'   => $loginUser->id,
                'actor_name' => $loginUser->name,
                'user_id'    => $user->id,
                'user_name'  => $user->name,
                'changes'    => $changes,
            ]);
        }

        return redirect()->route('users.index')
            ->withSuccess('User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        // Super-admin/admin can delete any user. Distributors are limited to their own created users.
        if ($user->created_by !== auth()->id()) {
            if (! auth()->user()->hasRole(['super-admin', 'admin'])) {
                abort(403, 'Unauthorized action.');
            }
        }


        $loginUser = Auth::user();

        Log::info('user.deleted', [
            'actor_id'   => $loginUser->id,
            'actor_name' => $loginUser->name,
            'user_id'    => $user->id,
            'user_name'  => $user->name,
        ]);

        $user->syncRoles([]);
        $user->delete();

        return redirect()->route('users.index')
            ->withSuccess('User deleted successfully.');
    }

    public function userList(Request $request)
    {
        $query = User::with(['role']);

        if (auth()->user()->hasRole('super-admin')) {
            $query->where(function ($q) {
                $q->where('created_by', auth()->id())
                  ->orWhereNull('created_by');
            });
            $excludeRoleIds = Role::whereIn('name', ['retailer', 'distributor'])->pluck('id')->toArray();
            $query->when(!empty($excludeRoleIds), fn($q) => $q->whereNotIn('role_id', $excludeRoleIds));
        } elseif (auth()->user()->hasRole('distributor')) {
            $query->where('created_by', auth()->id());
        } else {
            $query->where('created_by', auth()->id());
            $excludeRoleIds = Role::whereIn('name', ['retailer', 'distributor'])->pluck('id')->toArray();
            $query->when(!empty($excludeRoleIds), fn($q) => $q->whereNotIn('role_id', $excludeRoleIds));
        }

        $totalData = $query->count();
        $totalFiltered = $totalData;

        $limit  = $request->input('length');
        $start  = $request->input('start');
        $search = $request->input('search.value');

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%");
            });

            $totalFiltered = $query->count();
        }

        $users = $query->offset($start)
            ->limit($limit)
            ->orderBy('id', 'desc')
            ->get();

        $data = [];

        foreach ($users as $i => $u) {
            $actions = '<div class="btn-group" style="position: relative; left: 10px;">
                    <button type="button" class="btn btn-sm btn-info" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Actions">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="dropdown-menu action-dropdown" role="menu">';

            $actions .= '<a class="dropdown-item" href="' . route('users.show', $u->id) . '" id="userInfo" data-userid="' . $u->id . '">View</a>';
            $actions .= '<a class="dropdown-item" href="' . route('users.edit', $u->id) . '">Edit</a>';
            $actions .= '
                    <form action="' . route('users.destroy', $u->id) . '" method="POST" class="deleteForm" style="display:inline;">
                        ' . csrf_field() . '
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="dropdown-item deleteButton">Delete</button>
                    </form>
                ';
            $actions .= '</div></div>';

            $avatarHtml = '<div class="user-name-cell">'
                . '<span>' . e($u->name) . '</span>'
                . '</div>';

            $data[] = [
                'id'     => $start + $i + 1,
                'name'   => $avatarHtml,
                'email'  => $u->email,
                'mobile' => $u->mobile,
                'role'   => optional($u->role)->name,
                'status' => $u->status ? 'Active' : 'Inactive',
                'action' => $actions,
            ];
        }

        return response()->json([
            'draw'            => intval($request->input('draw')),
            'recordsTotal'    => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data'            => $data,
        ]);
    }

    protected function formData(?User $user = null): array
    {
        $rolesQuery = Role::orderBy('name');
        if (auth()->user()->hasRole('distributor')) {
            $rolesQuery->where('created_by', auth()->id());
        } else {
            $rolesQuery->whereNotIn('name', ['retailer', 'distributor']);
        }

        return [
            'roles' => $rolesQuery->get(),
        ];
    }

    protected function rules(?User $user = null): array
    {
        $roleRule = 'exists:roles,id';
        if (auth()->user()->hasRole('distributor')) {
            $roleRule = 'exists:roles,id,created_by,' . auth()->id();
        }

        return [
            'first_name'  => ['required', 'string', 'max:255'],
            'last_name'   => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'unique:users,email' . ($user ? ',' . $user->id : '')],
            'mobile'    => ['required', 'string', 'digits:10'],
            'role_id'   => ['required', $roleRule],
            'status'    => ['required', 'in:0,1'],
            'note'      => ['nullable', 'string', 'max:1000'],
            // 'password'  => [$user ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function storeRules(): array
    {
        $roleRule = 'exists:roles,id';
        if (auth()->user()->hasRole('distributor')) {
            $roleRule = 'exists:roles,id,created_by,' . auth()->id();
        }

        return [
            'first_name'  => ['required', 'string', 'max:255'],
            'last_name'   => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'mobile'    => ['required', 'string', 'digits:10'],
            'role_id'  => ['required', $roleRule],
            'status'   => ['required', 'in:0,1'],
            // 'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    protected function userPayload(Request $request, array $validated, ?User $user = null): array
    {
        $data = [
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'name'       => trim($validated['first_name'] . ' ' . $validated['last_name']),
            'email'      => $validated['email'],
            'mobile'     => $validated['mobile'] ?? null,
            'role_id'    => (int) $validated['role_id'],
            'status'     => (int) ($validated['status'] ?? 1),
            'note'       => $validated['note'] ?? null,
            'is_active'  => $request->boolean('is_active', (int) ($validated['status'] ?? 1) === 1),
            'created_by' => $user ? $user->created_by : auth()->id(),
        ];

        if (! $user) {
            $data['password'] = Hash::make('12345678');
        }

        return $data;
    }
}