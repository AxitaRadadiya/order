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
use Illuminate\Support\Facades\Storage;

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
        $roles = Role::whereIn('name', ['retailer', 'distributor'])->orderBy('name')->get();
        $distributors = Customer::whereHas('role', function ($q) { $q->where('name', 'distributor'); })->get(['id','company_name','name']);

        $isDistributorPanel = false;
        $currentDistributorId = null;
        try {
            $u = auth()->user();
            if ($u && $u->hasRole('distributor')) {
                $isDistributorPanel = true;
                $currentDistributorId = $u->id;
                $roles = Role::where('name', 'retailer')->get();
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
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'mobile'        => 'nullable|digits:10',
            'company_name' => 'nullable|string|max:255',
            'website'      => 'nullable|url|max:255',
            // 'password'     => 'required|min:6',
            'distributor_id' => 'nullable|exists:users,id',
            'role_id' => 'nullable|exists:roles,id',
            'gst_number'   => 'nullable|string|max:20',
            'pan_number'   => 'nullable|string|max:15',
            'credit_limit' => 'nullable|numeric|min:0',
            'discount'     => 'nullable|numeric|min:0|max:100',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'shop_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'pan_card_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'gst_certificate_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'google_location_link' => 'nullable|url|max:255',
        ]);

        $role = null;
        if ($request->filled('role_id')) {
            $role = Role::find($request->input('role_id'));
        }
        if ($role && strtolower($role->name) === 'retailer') {
            $request->validate(['distributor_id' => 'required|exists:users,id']);
        }

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
            $currentUser = auth()->user();
            $autoVerified = false;
            if ($currentUser && ($currentUser->hasRole('distributor') || $currentUser->hasRole('super-admin') || $currentUser->hasRole('superadmin'))) {
                $autoVerified = true;
            }

            $profileImage = null;
            $shopImage = null;
            $panImage = null;
            $gstImage = null;

            if ($request->hasFile('profile_image')) {
                $profileImage = $request->file('profile_image')
                    ->store('profiles', 'public');
            }

            if ($request->hasFile('shop_image')) {
                $shopImage = $request->file('shop_image')
                    ->store('users/shop', 'public');
            }

            if ($request->hasFile('pan_card_image')) {
                $panImage = $request->file('pan_card_image')
                    ->store('users/pan', 'public');
            }

            if ($request->hasFile('gst_certificate_image')) {
                $gstImage = $request->file('gst_certificate_image')
                    ->store('users/gst', 'public');
            }

            $customer = Customer::create([
                'first_name'      => $request->first_name,
                'last_name'       => $request->last_name,
                'name'            => trim($request->first_name . ' ' . $request->last_name),
                'company_name'    => $request->company_name,
                'email'           => $request->email,
                'mobile'           => $request->mobile,
                'website'         => $request->website,
                'distributor_id'  => $request->input('distributor_id'),
                'password'        => Hash::make('12345678'),
                'role_id'         => $request->input('role_id') ?? null,
                'payment_terms'   => $request->payment_terms,
                'gst_treatment'   => $request->gst_treatment,
                'gst_number'      => $request->gst_number,
                'pan_number'      => $request->pan_number,
                'place_of_supply' => $request->place_of_supply,
                'discount'        => $request->discount ?? 0,
                'credit_limit'    => $request->credit_limit ?? 0,
                'distributor_verified' => $autoVerified,
                'distributor_verified_at' => $autoVerified ? now() : null,
                'google_location_link' => $request->google_location_link,
                'profile_image' => $profileImage,
                'shop_image' => $shopImage,
                'pan_card_image' => $panImage,
                'gst_certificate_image' => $gstImage,
            ]);

            if ($request->filled('role_id')) {
                $customer->assignRole((int) $request->input('role_id'));
            }

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

            Address::create(array_merge([
                'user_id'            => $customer->id,
                'billing_attention'  => $request->billing_attention,
                'billing_street'     => $request->billing_street,
                'billing_city'       => $request->billing_city,
                'billing_state'      => $request->billing_state,
                'billing_pin_code'   => $request->billing_pin_code,
                'billing_country'    => $request->billing_country ?? 'India',
                'billing_gst_number' => $request->billing_gst_number,
                'same_as'            => $isSameAs,
            ], $shippingData));

            BankDetail::create([
                'user_id'     => $customer->id,
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
        $customer->load(['role', 'address', 'bankDetail']);

        // Show users created by this customer (only meaningful for distributor profiles)
        $createdUsers = collect();
        if (strtolower(optional($customer->role)->name ?? '') === 'distributor') {
            $createdUsers = Customer::with('role')
                ->where('created_by', $customer->id)
                ->orderBy('id', 'desc')
                ->get();
        }

        return view('admin.customer.show', compact('customer', 'createdUsers'));
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
                $isDistributorPanel = true;
                $currentDistributorId = $u->id;
                $roles = Role::where('name', 'retailer')->get();
                $distributors = collect([['id' => $u->id, 'company_name' => $u->company_name, 'name' => $u->name]]);

                if ($customer->id !== $u->id && $customer->distributor_id !== $u->id) {
                    abort(403);
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        try {
            if (! is_null($customer->status)) {
                $customer->status = $customer->status ? '1' : '0';
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return view('admin.customer.edit', compact('customer', 'countries', 'states', 'cities', 'roles', 'distributors', 'isDistributorPanel', 'currentDistributorId'));
    }

    public function update(Request $request, Customer $customer)
    {
        try {
            $u = auth()->user();
            if ($u && $u->hasRole('distributor')) {
                $retailerRole = Role::where('name', 'retailer')->first();
                if ($retailerRole) {
                    $request->merge(['role_id' => $retailerRole->id]);
                }
                $request->merge(['distributor_id' => $u->id]);

                if ($customer->id !== $u->id && $customer->distributor_id !== $u->id) {
                    abort(403);
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email,' . $customer->id,
            'mobile'        => 'nullable|digits:10',
            'company_name' => 'nullable|string|max:255',
            'website'      => 'nullable|url|max:255',
            // 'password'     => 'nullable|min:6',
            'distributor_id' => 'nullable|exists:users,id',
            'role_id'      => 'nullable|exists:roles,id',
            'status'       => 'required|in:0,1',
            'gst_number'   => 'nullable|string|max:20',
            'pan_number'   => 'nullable|string|max:15',
            'credit_limit' => 'nullable|numeric|min:0',
            'discount'     => 'nullable|numeric|min:0|max:100',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'shop_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'pan_card_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'gst_certificate_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'google_location_link' => 'nullable|url|max:255',
        ]);

        DB::beginTransaction();
        try {
            $roleId = $request->input('role_id', $customer->role_id);
            $role   = $roleId ? Role::find($roleId) : null;
            if ($role && strtolower($role->name) === 'retailer') {
                $request->validate(['distributor_id' => 'required|exists:users,id']);
            }

            $profileImage = $customer->profile_image;
            $shopImage = $customer->shop_image;
            $panImage = $customer->pan_card_image;
            $gstImage = $customer->gst_certificate_image;

            // Remove Images
            if ($request->profile_image_remove == 1) {
                if ($profileImage) {
                    Storage::disk('public')->delete($profileImage);
                }
                $profileImage = null;
            }

            if ($request->shop_image_remove == 1) {
                if ($shopImage) {
                    Storage::disk('public')->delete($shopImage);
                }
                $shopImage = null;
            }

            if ($request->pan_card_image_remove == 1) {
                if ($panImage) {
                    Storage::disk('public')->delete($panImage);
                }
                $panImage = null;
            }

            if ($request->gst_certificate_image_remove == 1) {
                if ($gstImage) {
                    Storage::disk('public')->delete($gstImage);
                }
                $gstImage = null;
            }

            if ($request->hasFile('profile_image')) {
                if ($profileImage && Storage::disk('public')->exists($profileImage)) {
                    Storage::disk('public')->delete($profileImage);
                }
                $profileImage = $request->file('profile_image')
                    ->store('profiles', 'public');
            }

            if ($request->hasFile('shop_image')) {
                if ($shopImage && Storage::disk('public')->exists($shopImage)) {
                    Storage::disk('public')->delete($shopImage);
                }
                $shopImage = $request->file('shop_image')
                    ->store('users/shop', 'public');
            }

            if ($request->hasFile('pan_card_image')) {
                if ($panImage && Storage::disk('public')->exists($panImage)) {
                    Storage::disk('public')->delete($panImage);
                }
                $panImage = $request->file('pan_card_image')
                    ->store('users/pan', 'public');
            }

            if ($request->hasFile('gst_certificate_image')) {
                if ($gstImage && Storage::disk('public')->exists($gstImage)) {
                    Storage::disk('public')->delete($gstImage);
                }
                $gstImage = $request->file('gst_certificate_image')
                    ->store('users/gst', 'public');
            }

            // 1. Update Customer
            $customerData = [
                'first_name'      => $request->first_name,
                'last_name'       => $request->last_name,
                'name'            => trim($request->first_name . ' ' . $request->last_name),
                'company_name'    => $request->company_name,
                'email'           => $request->email,
                'mobile'          => $request->mobile,
                'distributor_id'  => $request->filled('distributor_id') ? $request->distributor_id : ($customer->distributor_id ?? null),
                'website'         => $request->website,
                'role_id'         => $request->filled('role_id') ? $request->role_id : ($customer->role_id ?? null),
                'status'          => $request->status === '1' ? 1 : 0,
                'payment_terms'   => $request->payment_terms,
                'gst_treatment'   => $request->gst_treatment,
                'gst_number'      => $request->gst_number,
                'pan_number'      => $request->pan_number,
                'place_of_supply' => $request->place_of_supply,
                'discount'        => $request->discount ?? 0,
                'credit_limit'    => $request->credit_limit ?? 0,
                'google_location_link' => $request->google_location_link,
                'profile_image' => $profileImage,
                'shop_image' => $shopImage,
                'pan_card_image' => $panImage,
                'gst_certificate_image' => $gstImage,
            ];
            if ($request->filled('password')) {
                $customerData['password'] = Hash::make($request->password);
            }
            $customer->update($customerData);

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

        $draw   = intval($request->get('draw'));
        $start  = intval($request->get('start', 0));
        $length = intval($request->get('length', 10));
        $searchValue = $request->input('search.value');

        $order       = $request->input('order.0');
        $orderColumn = 'id';
        $orderDir    = 'desc';
        if ($order) {
            $colIndex    = intval($order['column']);
            $orderColumn = $columns[$colIndex] ?? 'id';
            $orderDir    = $order['dir'] ?? 'desc';
        }

        $roleIds = Role::whereIn('name', ['retailer', 'distributor'])->pluck('id')->toArray();
        $query   = Customer::whereIn('role_id', $roleIds);

        try {
            $u = auth()->user();
            if ($u && $u->hasRole('distributor')) {
                $retailerRole = Role::where('name', 'retailer')->first();
                if ($retailerRole) {
                    $query = Customer::where('role_id', $retailerRole->id)
                        ->where('distributor_id', $u->id);
                } else {
                    $query = Customer::where('distributor_id', $u->id);
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                  ->orWhere('email', 'like', "%{$searchValue}%")
                  ->orWhere('company_name', 'like', "%{$searchValue}%")
                  ->orWhere('mobile', 'like', "%{$searchValue}%");
            });
        }

        $filterCountry = $request->input('country');
        $filterState   = $request->input('state');
        $filterCity    = $request->input('city');
        $filterStatus  = $request->input('status');

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
            if ($filterStatus === 'active')   $query->where('status', 1);
            elseif ($filterStatus === 'inactive') $query->where('status', 0);
        }

        $recordsTotal    = Customer::count();
        $recordsFiltered = $query->count();

        $customers = $query->orderBy($orderColumn, $orderDir)
            ->skip($start)->take($length)->get();

        $data = [];
        foreach ($customers as $customer) {
            $statusBadge = '';
            if (!is_null($customer->status)) {
                $statusBadge = $customer->status
                    ? '<span class="badge badge-success">Active</span>'
                    : '<span class="badge badge-danger">Inactive</span>';
            }

            $viewUrl   = route('customers.show', $customer->id);
            $editUrl   = route('customers.edit', $customer->id);
            $deleteUrl = route('customers.destroy', $customer->id);

            $actions = '<div class="btn-group" style="position: relative; left: 10px;">
                <button type="button" class="btn btn-sm btn-info" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Actions">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <div class="dropdown-menu action-dropdown" role="menu">';

            $actions .= '<a class="dropdown-item" href="' . $viewUrl . '">View</a>';
            $actions .= '<a class="dropdown-item" href="' . $editUrl . '">Edit</a>';

            try {
                $u = auth()->user();
                if ($u && $u->hasRole('distributor')
                    && $customer->role_id
                    && strtolower(optional($customer->role)->name ?? '') === 'retailer'
                    && $customer->distributor_id == $u->id
                    && empty($customer->distributor_verified)
                ) {
                    $verifyUrl = route('customers.verify.distributor', $customer->id);
                    $actions  .= '<form method="POST" action="' . $verifyUrl . '" style="display:inline;">
                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                        <button type="submit" class="dropdown-item text-success">Verify Retailer</button>
                    </form>';
                }
            } catch (\Throwable $e) {
                // ignore
            }

            $actions .= '
                <form method="POST" action="' . $deleteUrl . '" style="display:inline;">
                    <input type="hidden" name="_token" value="' . csrf_token() . '">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="dropdown-item deleteButton">Delete</button>
                </form>
            ';
            $actions .= '</div></div>';

            $data[] = [
                'id'           => $customer->id,
                'name'         => $customer->name,
                'email'        => $customer->email,
                'mobile'       => $customer->mobile,
                'company_name' => $customer->company_name,
                'status'       => $statusBadge,
                'action'       => $actions,
            ];
        }

        return response()->json([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }

    public function getCustomer($id)
    {
        $customer = Customer::with('address')->find($id);
        if (!$customer) {
            return response()->json([], 404);
        }

        $addr     = $customer->address;
        $billing  = '';
        $shipping = '';
        if ($addr) {
            $billing = trim(
                ($addr->billing_street   ?? '') . ' ' .
                ($addr->billing_city     ?? '') . ' ' .
                ($addr->billing_state    ?? '') . ' ' .
                ($addr->billing_country  ?? '') . ' - ' .
                ($addr->billing_pin_code ?? '')
            );
            $shipping = trim(
                ($addr->shipping_street   ?? $addr->billing_street   ?? '') . ' ' .
                ($addr->shipping_city     ?? $addr->billing_city     ?? '') . ' ' .
                ($addr->shipping_state    ?? $addr->billing_state    ?? '') . ' ' .
                ($addr->shipping_country  ?? $addr->billing_country  ?? '') . ' - ' .
                ($addr->shipping_pin_code ?? $addr->billing_pin_code ?? '')
            );
        }

        return response()->json([
            'billing_address'  => $billing,
            'shipping_address' => $shipping,
        ]);
    }

    public function verifyByDistributor(Request $request, Customer $customer)
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('distributor')) {
            abort(403);
        }

        try {
            if ($customer->distributor_id !== $user->id) {
                abort(403);
            }
            if (! $customer->role || strtolower($customer->role->name) !== 'retailer') {
                abort(403);
            }
        } catch (\Throwable $e) {
            abort(403);
        }

        $customer->update([
            'distributor_verified'    => true,
            'distributor_verified_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Retailer verified.');
    }
}