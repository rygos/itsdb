<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\City;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\Log;
use App\Models\OperatingSystem;
use App\Models\Remark;
use App\Models\ServerKind;
use App\Models\Status;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomersController extends Controller
{
    private function cityOptions(): array
    {
        // Keep select option formatting in one place so all customer forms render cities consistently.
        $cities = City::query()->orderBy('name')->get();
        $options = ['' => 'Bitte waehlen'];

        foreach ($cities as $item) {
            $options[$item->id] = strtoupper($item->country_code).' - '.$item->name;
        }

        return $options;
    }

    private function serverKindOptions(): array
    {
        return ['' => ''] + ServerKind::query()->orderBy('name')->pluck('name', 'id')->toArray();
    }

    private function operatingSystemOptions(): array
    {
        return ['' => ''] + OperatingSystem::query()->orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $customers = Customer::query()
            ->with(['city', 'latestProject.status'])
            ->when($search !== '', function ($query) use ($search) {
                $like = '%'.$search.'%';

                // Search covers the identifiers users reach for most often plus the linked city name.
                $query->where(function ($customerQuery) use ($like) {
                    $customerQuery
                        ->where('short_no', 'like', $like)
                        ->orWhere('sap_no', 'like', $like)
                        ->orWhere('name', 'like', $like)
                        ->orWhereHas('city', function ($cityQuery) use ($like) {
                            $cityQuery->where('name', 'like', $like);
                        });
                });
            })
            ->orderBy('short_no')
            ->get();

        return view('customers.index', [
            'customers' => $customers,
            'term' => $search,
        ]);
    }

    public function view(Customer $customer): View
    {
        // Load all relationships up front to avoid hidden N+1 queries in the Blade template.
        $customer->load([
            'city',
            'projects.status',
            'projects.user',
            'servers.serverKind',
            'servers.operatingSystem',
            'credentials.servers',
            'contacts',
            'documents',
        ]);
        $remark = Remark::query()
            ->whereType(1)
            ->where('relation_id', $customer->id)
            ->value('remark');
        $statuses = Status::query()->orderBy('name')->get();
        $finishedStatusId = $statuses
            ->firstWhere('name', 'FINISHED')
            ?->id;

        return view('customers.view', [
            'customer' => $customer,
            'remark' => $remark ?? '',
            'status' => $statuses->pluck('name', 'id'),
            'finishedStatusId' => $finishedStatusId,
            'projects' => $customer->projects,
            'servers' => $customer->servers,
            'credentials' => $customer->credentials,
            'documents' => $customer->documents()->orderBy('original_name')->get(),
            'contacts' => $customer->contacts,
            'logs' => Log::query()->whereContentId($customer->id)->where('section', 'customer')->orderBy('created_at')->get(),
            'serverKindOptions' => $this->serverKindOptions(),
            'operatingSystemOptions' => $this->operatingSystemOptions(),
        ]);
    }

    public function add(): View
    {
        $country = [
            'de' => 'Deutschland',
            'at' => 'Österreich',
            'ch' => 'Schweiz',
            'lu' => 'Luxemburg',
        ];

        return view('customers.add', [
            'citys' => $this->cityOptions(),
            'countrys' => $country,
        ]);
    }

    public function edit(Customer $customer): View
    {
        return view('customers.edit', [
            'customer' => $customer->load('city'),
            'citys' => $this->cityOptions(),
        ]);
    }

    public function city(City $city): View
    {
        $customers = Customer::query()
            ->with(['city', 'latestProject.status'])
            ->where('city_id', $city->id)
            ->orderBy('short_no')
            ->get();

        return view('customers.index', [
            'customers' => $customers,
            'city' => $city,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // Validate explicit input instead of trusting raw request data from the legacy form.
        $validated = $request->validate([
            'short_no' => ['required', 'integer', 'min:1', 'unique:customers,short_no'],
            'sap_no' => ['required', 'string', 'max:255'],
            'dynamics_no' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'integer', 'exists:citys,id'],
        ]);

        $customer = Customer::query()->create([
            // New customers should always belong to the authenticated user, never to a hard-coded fallback.
            'user_id' => (int) $request->user()->id,
            'short_no' => $validated['short_no'],
            'sap_no' => trim($validated['sap_no']),
            'dynamics_no' => trim($validated['dynamics_no']),
            'name' => trim($validated['name']),
            'city_id' => $validated['city'] ?? null,
        ]);

        LogHelper::log('customer', $customer->id, 'Add', 'Create Customer: '.$customer->name);

        return redirect()->route('index');
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        // The customer number is business-relevant and must remain unique even during updates.
        $validated = $request->validate([
            'short_no' => ['required', 'integer', 'min:1', Rule::unique('customers', 'short_no')->ignore($customer->id)],
            'sap_no' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'integer', 'exists:citys,id'],
        ]);

        $customer->short_no = $validated['short_no'];
        $customer->sap_no = trim($validated['sap_no']);
        $customer->name = trim($validated['name']);
        $customer->city_id = $validated['city'] ?? null;
        $customer->save();

        LogHelper::log('customer', $customer->id, 'Update', 'Update Customer Master Data: '.$customer->name);

        return redirect()->route('customers.view', $customer);
    }

    public function store_city(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'country_code' => ['required', 'string', 'size:2'],
        ]);

        City::query()->create([
            'name' => trim($validated['name']),
            'country_code' => strtolower($validated['country_code']),
        ]);

        return redirect()->back();
    }

    public function contact_create(Request $request): RedirectResponse
    {
        // Contacts are created via mass assignment only after validation so the payload stays predictable.
        $validated = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'prefix' => ['nullable', 'string', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'familyname' => ['required', 'string', 'max:255'],
            'phone_mobile' => ['nullable', 'string', 'max:255'],
            'phone_office' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'comments' => ['nullable', 'string', 'max:255'],
        ]);

        $contact = CustomerContact::query()->create([
            'customer_id' => $validated['customer_id'],
            'prefix' => $validated['prefix'] ?? null,
            'name' => $validated['name'] ?? null,
            'familyname' => $validated['familyname'],
            'phone_mobile' => $validated['phone_mobile'] ?? null,
            'phone_office' => $validated['phone_office'] ?? null,
            'email' => $validated['email'] ?? null,
            'comments' => $validated['comments'] ?? null,
        ]);

        LogHelper::log('customer', $contact->customer_id, 'Contact', 'Contact Added: '.$contact->name.' '.$contact->familyname);

        return redirect()->route('customers.view', $contact->customer_id);
    }

    public function contact_update(Request $request): RedirectResponse
    {
        // The update flow intentionally only allows comment changes in the current UI.
        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:customer_contacts,id'],
            'comments' => ['nullable', 'string', 'max:255'],
        ]);

        $contact = CustomerContact::query()->findOrFail($validated['id']);
        $contact->comments = $validated['comments'] ?? null;
        $contact->save();

        LogHelper::log('customer', $contact->customer_id, 'Contact', 'Contact Comment for '.$contact->name.' '.$contact->familyname.' - '.$contact->comments);

        return redirect()->back();
    }

    public function contact_delete(int $id): RedirectResponse
    {
        $contact = CustomerContact::query()->findOrFail($id);

        LogHelper::log('customer', $contact->customer_id, 'Contact', 'Delete Contact: '.$contact->name.' '.$contact->familyname);

        $contact->delete();

        return redirect()->back();
    }
}
