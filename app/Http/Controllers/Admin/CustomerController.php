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
        $query = Customer::with(['address', 'bankDetail']);
        return view('admin.customer.index');
    }

    public function create()
    {
        $countries = Country::orderBy('name')->get();
        $states = State::orderBy('name')->get();
        $cities = City::orderBy('name')->get();
        // Only allow selecting retail/distributor roles in customer creation
        $roles = Role::whereIn('name', ['retailer', 'distributor'])->orderBy('name')->get();
        return view('admin.customer.create', compact('countries', 'states', 'cities', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'phone'        => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'website'      => 'nullable|url|max:255',
            'password'     => 'required|min:6',
            'role_id' => 'nullable|exists:roles,id',
            'gst_number'   => 'nullable|string|max:20',
            'pan_number'   => 'nullable|string|max:15',
            'credit_limit' => 'nullable|numeric|min:0',
            'discount'     => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            // 1. Create Customer and assign role
            $customer = Customer::create([
                'name'            => $request->name,
                'company_name'    => $request->company_name,
                'email'           => $request->email,
                'phone'           => $request->phone,
                'website'         => $request->website,
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
        return view('admin.customer.edit', compact('customer', 'countries', 'states', 'cities', 'roles'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email,' . $customer->id,
            'phone'        => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'website'      => 'nullable|url|max:255',
            'password'     => 'nullable|min:6',
            'role_id'      => 'nullable|exists:roles,id',
            'gst_number'   => 'nullable|string|max:20',
            'pan_number'   => 'nullable|string|max:15',
            'credit_limit' => 'nullable|numeric|min:0',
            'discount'     => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            // 1. Update Customer
            $customerData = [
                'name'            => $request->name,
                'company_name'    => $request->company_name,
                'email'           => $request->email,
                'phone'           => $request->phone,
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
        
        $columns = ['id', 'name', 'email', 'phone', 'company_name', 'status'];

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

        $query = Customer::whereIn('role_id', [7, 8]);

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                  ->orWhere('email', 'like', "%{$searchValue}%")
                  ->orWhere('company_name', 'like', "%{$searchValue}%")
                  ->orWhere('phone', 'like', "%{$searchValue}%");
            });
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

            $data[] = [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'company_name' => $customer->company_name,
                'status' => $statusBadge,
                'action' => view('admin.customer.partials.actions', compact('customer'))->render(),
            ];
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }
}