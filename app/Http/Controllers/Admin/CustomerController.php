<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User as Customer;
use App\Models\Address;
use App\Models\BankDetail;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $countries = Country::orderBy('name')->get();
        $states = State::orderBy('name')->get();
        $cities = City::orderBy('name')->get();
        return view('admin.customer.index', compact('countries','states','cities'));
    }

    public function create()
    {
        $countries = Country::orderBy('name')->get();
        $states = State::orderBy('name')->get();
        $cities = City::orderBy('name')->get();
        // Default: allow selecting retail/distributor roles in customer creation
        $roles = Role::whereIn('name', ['retailer', 'distributor'])->orderBy('name')->get();
        $distributors = Customer::whereHas('role', function ($q) { $q->where('name', 'distributor'); })->get(['id','company_name','name']);

        // If the logged-in user is a distributor, restrict creation to retailers
        $isDistributorPanel = false;
        $currentDistributorId = null;
        try {
            $u = auth()->user();
            if ($u && $u->hasRole('distributor')) {
                $isDistributorPanel = true;
                $currentDistributorId = $u->id;
                $roles = Role::where('name', 'retailer')->get();
                // distributors list is not needed for distributor panel
                $distributors = collect([['id' => $u->id, 'company_name' => $u->company_name, 'name' => $u->name]]);
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return view('admin.customer.create', compact('countries', 'states', 'cities', 'roles', 'distributors', 'isDistributorPanel', 'currentDistributorId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'mobile'        => 'nullable|digits:10',
            'company_name' => 'nullable|string|max:255',
            'website'      => 'nullable|url|max:255',
            'password'     => 'required|min:6',
            'distributor_id' => 'nullable|exists:users,id',
            'role_id' => 'nullable|exists:roles,id',
            'gst_number'   => 'nullable|string|max:20',
            'pan_number'   => 'nullable|string|max:15',
            'credit_limit' => 'nullable|numeric|min:0',
            'discount'     => 'nullable|numeric|min:0|max:100',
        ]);

        // If role is retailer, distributor selection must be provided
        $role = null;
        if ($request->filled('role_id')) {
            $role = Role::find($request->input('role_id'));
        }
        if ($role && strtolower($role->name) === 'retailer') {
            $request->validate(['distributor_id' => 'required|exists:users,id']);
        }

        // If current user is distributor, force created customer to be retailer under them
        try {
            $u = auth()->user();
            if ($u && $u->hasRole('distributor')) {
                $retailerRole = Role::where('name', 'retailer')->first();
                if ($retailerRole) {
                    $request->merge(['role_id' => $retailerRole->id]);
                }
                $request->merge(['distributor_id' => $u->id]);
            }
        } catch (\Throwable $e) {
            // ignore
        }

        DB::beginTransaction();
        try {
            // 1. Create Customer and assign role
            $customer = Customer::create([
                'name'            => $request->name,
                'company_name'    => $request->company_name,
                'email'           => $request->email,
                'mobile'           => $request->mobile,
                'website'         => $request->website,
                'distributor_id'  => $request->input('distributor_id'),
                'password'        => Hash::make($request->password),
                'role_id'         => $request->input('role_id') ?? null,
                'payment_terms'   => $request->payment_terms,
                'gst_treatment'   => $request->gst_treatment,
                'gst_number'      => $request->gst_number,
                'pan_number'      => $request->pan_number,
                'place_of_supply' => $request->place_of_supply,
                'discount'        => $request->discount ?? 0,
                'credit_limit'    => $request->credit_limit ?? 0,
            ]);

            // If a role was provided, ensure it's assigned via helper
            if ($request->filled('role_id')) {
                $customer->assignRole((int) $request->input('role_id'));
            }

            // 2. Resolve shipping fields
            $isSameAs     = $request->boolean('same_as');
            $shippingData = $isSameAs ? [
                'shipping_attention'  => $request->billing_attention,
                'shipping_street'     => $request->billing_street,
                'shipping_city'       => $request->billing_city,
                'shipping_state'      => $request->billing_state,
                'shipping_pin_code'   => $request->billing_pin_code,
                'shipping_country'    => $request->billing_country,
                'shipping_gst_number' => $request->billing_gst_number,
            ] : [
                'shipping_attention'  => $request->shipping_attention,
                'shipping_street'     => $request->shipping_street,
                'shipping_city'       => $request->shipping_city,
                'shipping_state'      => $request->shipping_state,
                'shipping_pin_code'   => $request->shipping_pin_code,
                'shipping_country'    => $request->shipping_country,
                'shipping_gst_number' => $request->shipping_gst_number,
            ];

            // 3. Create Address
            Address::create(array_merge([
                'user_id'        => $customer->id,
                'billing_attention'  => $request->billing_attention,
                'billing_street'     => $request->billing_street,
                'billing_city'       => $request->billing_city,
                'billing_state'      => $request->billing_state,
                'billing_pin_code'   => $request->billing_pin_code,
                'billing_country'    => $request->billing_country ?? 'India',
                'billing_gst_number' => $request->billing_gst_number,
                'same_as'            => $isSameAs,
            ], $shippingData));

            // 4. Create Bank Detail
            BankDetail::create([
                'user_id' => $customer->id,
                'bank_name'   => $request->bank_name,
                'account_no'  => $request->account_no,
                'ifsc_code'   => $request->ifsc_code,
                'branch_name' => $request->branch_name,
            ]);

            DB::commit();
            return redirect()->route('customers.index')
                ->with('success', 'Customer created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function show(Customer $customer)
    {
        $customer->load(['address', 'bankDetail']);
        return view('admin.customer.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $customer->load(['address', 'bankDetail']);
        $countries = Country::orderBy('name')->get();
        $states = State::orderBy('name')->get();
        $cities = City::orderBy('name')->get();
        $roles = Role::whereIn('name', ['retailer', 'distributor'])->orderBy('name')->get();
        $distributors = Customer::whereHas('role', function ($q) { $q->where('name', 'distributor'); })->get(['id','company_name','name']);

        $isDistributorPanel = false;
        $currentDistributorId = null;
        try {
            $u = auth()->user();
            if ($u && $u->hasRole('distributor')) {
                // Distributor editing — restrict role selection to retailer and prefill distributor
                $isDistributorPanel = true;
                $currentDistributorId = $u->id;
                $roles = Role::where('name', 'retailer')->get();
                $distributors = collect([['id' => $u->id, 'company_name' => $u->company_name, 'name' => $u->name]]);

                // Ensure distributor can only edit their own retailers (or themselves)
                if ($customer->id !== $u->id && $customer->distributor_id !== $u->id) {
                    abort(403);
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return view('admin.customer.edit', compact('customer', 'countries', 'states', 'cities', 'roles', 'distributors', 'isDistributorPanel', 'currentDistributorId'));
    }

    public function update(Request $request, Customer $customer)
    {
        // If current user is distributor, force role to retailer and distributor_id to current distributor
        try {
            $u = auth()->user();
            if ($u && $u->hasRole('distributor')) {
                $retailerRole = Role::where('name', 'retailer')->first();
                if ($retailerRole) {
                    $request->merge(['role_id' => $retailerRole->id]);
                }
                $request->merge(['distributor_id' => $u->id]);

                // Prevent editing customers not assigned to this distributor
                if ($customer->id !== $u->id && $customer->distributor_id !== $u->id) {
                    abort(403);
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email,' . $customer->id,
            'mobile'        => 'nullable|digits:10',
            'company_name' => 'nullable|string|max:255',
            'website'      => 'nullable|url|max:255',
            'password'     => 'nullable|min:6',
            'distributor_id' => 'nullable|exists:users,id',
            'role_id'      => 'nullable|exists:roles,id',
            'gst_number'   => 'nullable|string|max:20',
            'pan_number'   => 'nullable|string|max:15',
            'credit_limit' => 'nullable|numeric|min:0',
            'discount'     => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            // If role is being set to retailer, distributor selection must be provided
            $roleId = $request->input('role_id', $customer->role_id);
            $role = $roleId ? Role::find($roleId) : null;
            if ($role && strtolower($role->name) === 'retailer') {
                $request->validate(['distributor_id' => 'required|exists:users,id']);
            }

            // 1. Update Customer
            $customerData = [
                'name'            => $request->name,
                'company_name'    => $request->company_name,
                'email'           => $request->email,
                'mobile'           => $request->mobile,
                'distributor_id'  => $request->filled('distributor_id') ? $request->distributor_id : ($customer->distributor_id ?? null),
                'website'         => $request->website,
                'role_id'         => $request->filled('role_id') ? $request->role_id : ($customer->role_id ?? null),
                'payment_terms'   => $request->payment_terms,
                'gst_treatment'   => $request->gst_treatment,
                'gst_number'      => $request->gst_number,
                'pan_number'      => $request->pan_number,
                'place_of_supply' => $request->place_of_supply,
                'discount'        => $request->discount ?? 0,
                'credit_limit'    => $request->credit_limit ?? 0,
            ];
            if ($request->filled('password')) {
                $customerData['password'] = Hash::make($request->password);
            }
            $customer->update($customerData);

            // 2. Resolve shipping fields
            $isSameAs     = $request->boolean('same_as');
            $shippingData = $isSameAs ? [
                'shipping_attention'  => $request->billing_attention,
                'shipping_street'     => $request->billing_street,
                'shipping_city'       => $request->billing_city,
                'shipping_state'      => $request->billing_state,
                'shipping_pin_code'   => $request->billing_pin_code,
                'shipping_country'    => $request->billing_country,
                'shipping_gst_number' => $request->billing_gst_number,
            ] : [
                'shipping_attention'  => $request->shipping_attention,
                'shipping_street'     => $request->shipping_street,
                'shipping_city'       => $request->shipping_city,
                'shipping_state'      => $request->shipping_state,
                'shipping_pin_code'   => $request->shipping_pin_code,
                'shipping_country'    => $request->shipping_country,
                'shipping_gst_number' => $request->shipping_gst_number,
            ];

            Address::updateOrCreate(
                ['user_id' => $customer->id],
                array_merge([
                    'billing_attention'  => $request->billing_attention,
                    'billing_street'     => $request->billing_street,
                    'billing_city'       => $request->billing_city,
                    'billing_state'      => $request->billing_state,
                    'billing_pin_code'   => $request->billing_pin_code,
                    'billing_country'    => $request->billing_country ?? 'India',
                    'billing_gst_number' => $request->billing_gst_number,
                    'same_as'            => $isSameAs,
                ], $shippingData)
            );

            // 4. Update or Create Bank Detail
            BankDetail::updateOrCreate(
                ['user_id' => $customer->id],
                [
                    'bank_name'   => $request->bank_name,
                    'account_no'  => $request->account_no,
                    'ifsc_code'   => $request->ifsc_code,
                    'branch_name' => $request->branch_name,
                ]
            );

            DB::commit();
            return redirect()->route('customers.index')
                ->with('success', 'Customer updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function destroy(Customer $customer)
    {
        $customer->delete(); 
        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
    public function list(Request $request)
    {
        
        $columns = ['id', 'name', 'email', 'mobile', 'company_name', 'status'];

        $draw = intval($request->get('draw'));
        $start = intval($request->get('start', 0));
        $length = intval($request->get('length', 10));
        $searchValue = $request->input('search.value');

        $order = $request->input('order.0');
        $orderColumn = 'id';
        $orderDir = 'desc';
        if ($order) {
            $colIndex = intval($order['column']);
            $orderColumn = $columns[$colIndex] ?? 'id';
            $orderDir = $order['dir'] ?? 'desc';
        }
            // By default list customers who are assigned retailer/distributor roles
            $roleIds = Role::whereIn('name', ['retailer', 'distributor'])->pluck('id')->toArray();
            $query = Customer::whereIn('role_id', $roleIds);

            // If logged-in user is a distributor, show only their retailers
            try {
                $u = auth()->user();
                if ($u && $u->hasRole('distributor')) {
                    $retailerRole = Role::where('name', 'retailer')->first();
                    if ($retailerRole) {
                        $query = Customer::where('role_id', $retailerRole->id)
                            ->where('distributor_id', $u->id);
                    } else {
                        // fallback: restrict by distributor_id
                        $query = Customer::where('distributor_id', $u->id);
                    }
                }
            } catch (\Throwable $e) {
                // ignore and use default query
            }
        //$query = Customer::whereIn('role_id', [7, 8]);

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                  ->orWhere('email', 'like', "%{$searchValue}%")
                  ->orWhere('company_name', 'like', "%{$searchValue}%")
                  ->orWhere('mobile', 'like', "%{$searchValue}%");
            });
        }

        // Apply filters from request (country, state, city, status)
        $filterCountry = $request->input('country');
        $filterState = $request->input('state');
        $filterCity = $request->input('city');
        $filterStatus = $request->input('status');

        if ($filterCountry) {
            $query->whereHas('address', function ($qa) use ($filterCountry) {
                $qa->where('billing_country', $filterCountry)
                   ->orWhere('shipping_country', $filterCountry);
            });
        }
        if ($filterState) {
            $query->whereHas('address', function ($qa) use ($filterState) {
                $qa->where('billing_state', $filterState)
                   ->orWhere('shipping_state', $filterState);
            });
        }
        if ($filterCity) {
            $query->whereHas('address', function ($qa) use ($filterCity) {
                $qa->where('billing_city', $filterCity)
                   ->orWhere('shipping_city', $filterCity);
            });
        }
        if (!is_null($filterStatus) && $filterStatus !== '') {
            if ($filterStatus === 'active') $query->where('status', 1);
            elseif ($filterStatus === 'inactive') $query->where('status', 0);
        }

        $recordsTotal = Customer::count();
        $recordsFiltered = $query->count();

        $customers = $query->orderBy($orderColumn, $orderDir)
            ->skip($start)->take($length)->get();

        $data = [];
        foreach ($customers as $customer) {
            $statusBadge = '';
            if ($customer->status === null) {
                $statusBadge = '';
            } else {
                $statusBadge = $customer->status
                    ? '<span class="badge badge-success">Active</span>'
                    : '<span class="badge badge-danger">Inactive</span>';
            }
            $viewUrl = route('customers.show', $customer->id);
            $editUrl = route('customers.edit', $customer->id);
            $deleteUrl = route('customers.destroy', $customer->id);

            $actions = '<div class="btn-group" style="position: relative; left: 10px;">
                <button type="button" class="btn btn-sm btn-info" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Actions">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <div class="dropdown-menu action-dropdown" role="menu">';

            $actions .= '<a class="dropdown-item" href="' . $viewUrl . '">View</a>';
            $actions .= '<a class="dropdown-item" href="' . $editUrl . '">Edit</a>';
            $actions .= '
                <form method="POST" action="' . $deleteUrl . '" style="display:inline;">
                    <input type="hidden" name="_token" value="' . csrf_token() . '">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="dropdown-item deleteButton">Delete</button>
                </form>
            ';
            $actions .= '</div></div>';

            $data[] = [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'mobile' => $customer->mobile,
                'company_name' => $customer->company_name,
                'status' => $statusBadge,
                'action' => $actions,
            ];
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function getCustomer($id)
    {
        $customer = Customer::with('address')->find($id);
        if (!$customer) {
            return response()->json([], 404);
        }

        $addr = $customer->address;
        $billing = '';
        $shipping = '';
        if ($addr) {
            $billing = trim(
                ($addr->billing_street  ?? '') . ' ' .
                ($addr->billing_city    ?? '') . ' ' .
                ($addr->billing_state   ?? '') . ' ' .
                ($addr->billing_country ?? '') . ' - ' .
                ($addr->billing_pin_code ?? '')
            );

            $shipping = trim(
                ($addr->shipping_street  ?? $addr->billing_street  ?? '') . ' ' .
                ($addr->shipping_city    ?? $addr->billing_city    ?? '') . ' ' .
                ($addr->shipping_state   ?? $addr->billing_state   ?? '') . ' ' .
                ($addr->shipping_country ?? $addr->billing_country ?? '') . ' - ' .
                ($addr->shipping_pin_code ?? $addr->billing_pin_code ?? '')
            );
        }

        return response()->json([
            'billing_address' => $billing,
            'shipping_address' => $shipping,
        ]);
    }
}