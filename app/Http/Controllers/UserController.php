<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\CustomerType;
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
    }

    public function index(): View
    {
        $adminType = CustomerType::firstOrCreate(['name' => 'admin']);

        return view('admin.users.index', [
            'users' => User::with(['role'])
                ->where('customer_type_id', $adminType->id)
                ->orderBy('id')
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->storeRules());

        // ensure admin customer type exists and default new users to admin type
        $adminType = CustomerType::firstOrCreate(['name' => 'admin']);

        $payload = $this->userPayload($request, $validated);
        if (! isset($payload['customer_type_id'])) {
            $payload['customer_type_id'] = $adminType->id;
        }

        $user = User::create($payload);

        $user->assignRole((int) $validated['role_id']);

        $loginUser = Auth::user();
        Log::info('user.created', [
            'actor_id' => $loginUser->id,
            'actor_name' => $loginUser->name,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'properties' => $request->except(['_token', 'password', 'password_confirmation']),
        ]);

        return redirect()->route('users.index')
            ->withSuccess('New user added successfully.');
    }

    public function show(User $user): View
    {
        $user->load([
            'role',
        ]);

        return view('admin.users.show', [
            'user' => $user,
        ]);
    }

    public function edit($id): View
    {
        $user = User::with([
            'role',
        ])->findOrFail($id);

        return view('admin.users.edit', array_merge(
            ['user' => $user],
            $this->formData($user)
        ));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $user = User::findOrFail($id);

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
                'actor_id' => $loginUser->id,
                'actor_name' => $loginUser->name,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'changes' => $changes,
            ]);
        }

        return redirect()->route('users.index')
            ->withSuccess('User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $loginUser = Auth::user();

        Log::info('user.deleted', [
            'actor_id' => $loginUser->id,
            'actor_name' => $loginUser->name,
            'user_id' => $user->id,
            'user_name' => $user->name,
        ]);

        $user->syncRoles([]);
        $user->delete();

        return redirect()->route('users.index')
            ->withSuccess('User deleted successfully.');
    }

    public function userList(Request $request)
    {
        $adminType = CustomerType::firstOrCreate(['name' => 'admin']);
        $query = User::with(['role'])->where('customer_type_id', $adminType->id);

        $totalData = $query->count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
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
            $viewUrl = route('users.show', $u->id);
            $editUrl = route('users.edit', $u->id);
            $deleteUrl = route('users.destroy', $u->id);

            $actionHtml = '<a href="' . $viewUrl . '" class="btn btn-sm btn-info" title="View"><i class="fas fa-eye"></i></a> ';
            $actionHtml .= '<a href="' . $editUrl . '" class="btn btn-sm btn-primary" title="Edit"><i class="fas fa-edit"></i></a> ';
            $actionHtml .= '<form method="POST" action="' . $deleteUrl . '" style="display:inline-block;margin:0;padding:0;">';
            $actionHtml .= '<input type="hidden" name="_token" value="' . csrf_token() . '">';
            $actionHtml .= '<input type="hidden" name="_method" value="DELETE">';
            $actionHtml .= '<button type="submit" class="btn btn-sm btn-danger deleteButton" title="Delete"><i class="fas fa-trash"></i></button>';
            $actionHtml .= '</form>';

            $avatarHtml = '<div class="user-name-cell">'
                . '<span>' . e($u->name) . '</span>'
                . '</div>';

            $data[] = [
                'id' => $start + $i + 1,
                'name' => $avatarHtml,
                'email' => $u->email,
                'mobile' => $u->mobile,
                'role' => optional($u->role)->name,
                'status' => $u->status ? 'Active' : 'Inactive',
                'action' => $actionHtml,
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }

    protected function formData(?User $user = null): array
    {
        return [
            'roles' => Role::orderBy('name')->get(),
        ];
    }

    protected function rules(?User $user = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email' . ($user ? ',' . $user->id : '')],
            'mobile' => ['nullable', 'string', 'max:15'],
            'role_id' => ['required', 'exists:roles,id'],
            'status' => ['required', 'in:0,1'],
            'note' => ['nullable', 'string', 'max:1000'],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function storeRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'role_id' => ['required', 'exists:roles,id'],
            'status' => ['required', 'in:0,1'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }

    protected function userPayload(Request $request, array $validated, ?User $user = null): array
    {
        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'mobile' => $validated['mobile'] ?? null,
            'role_id' => (int) $validated['role_id'],
            'status' => (int) ($validated['status'] ?? 1),
            'note' => $validated['note'] ?? null,
            'is_active' => $request->boolean('is_active', (int) ($validated['status'] ?? 1) === 1),
            'customer_type_id' => $request->input('customer_type_id') ?? null,
        ];

        if (! $user || $request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        return $data;
    }
}
