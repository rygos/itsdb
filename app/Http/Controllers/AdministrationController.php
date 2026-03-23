<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\City;
use App\Models\Composer;
use App\Models\Credential;
use App\Models\Customer;
use App\Models\OperatingSystem;
use App\Models\Project;
use App\Models\Server;
use App\Models\ServerKind;
use App\Models\Status;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdministrationController extends Controller
{
    private const MISSING_CITIES_ANCHOR = '#missing-cities';

    private const IMPORT_PREVIEW_SESSION_KEY = 'import_preview';

    private function administrationRoute(string $subtab, ?string $anchor = null): string
    {
        return route('administration.index', ['tab' => 'administration', 'subtab' => $subtab]).($anchor ?? '');
    }

    public function index(Request $request): View
    {
        abort_unless($request->user()?->hasPermission('administration', 'visible'), 403);

        $tab = $request->get('tab', 'users');
        $subtab = $request->get('subtab', 'import');

        return view('administration.index', [
            'tab' => $tab,
            'subtab' => $subtab,
            'users' => User::query()->orderBy('name')->get(),
            'statuses' => Status::query()->orderBy('name')->get(),
            'cities' => City::query()->withCount('customers')->orderBy('name')->get(),
            'serverKinds' => ServerKind::query()->withCount('servers')->orderBy('name')->get(),
            'operatingSystems' => OperatingSystem::query()->withCount('servers')->orderBy('name')->get(),
            'customersWithoutCity' => Customer::query()->whereNull('city_id')->orderBy('short_no')->get(),
            'serversWithoutOperatingSystem' => Server::query()
                ->with('customer')
                ->whereNull('operating_system_id')
                ->orderBy('servername')
                ->get(),
            'serversWithoutServerKind' => Server::query()
                ->with('customer')
                ->whereNull('server_kind_id')
                ->orderBy('servername')
                ->get(),
            'projectsWithoutHours' => Project::query()
                ->with(['customer', 'status', 'user'])
                ->whereNull('hours')
                ->orderBy('name')
                ->get(),
            'duplicateSapCustomers' => Customer::query()
                ->whereIn('sap_no', $this->duplicateCustomerValues('sap_no'))
                ->orderBy('sap_no')
                ->orderBy('short_no')
                ->get(),
            'duplicateShortCustomers' => Customer::query()
                ->whereIn('short_no', $this->duplicateCustomerValues('short_no'))
                ->orderBy('short_no')
                ->orderBy('sap_no')
                ->get(),
            'credentialsWithoutServers' => Credential::query()
                ->whereDoesntHave('servers')
                ->orderBy('type')
                ->orderBy('username')
                ->get(),
            'composeWithoutContainers' => Composer::query()
                ->whereDoesntHave('rel')
                ->orderBy('title')
                ->get(),
            'registrationEnabled' => AppSetting::getBoolean('registration_enabled', config('app.registration_enabled')),
            'permissionAreas' => User::permissionAreas(),
            'permissionLevels' => User::permissionLevels(),
        ]);
    }

    public function editUser(Request $request, User $user): View
    {
        abort_unless($request->user()?->hasPermission('administration', 'administration'), 403);

        return view('administration.user-edit', [
            'editUser' => $user,
            'permissionAreas' => User::permissionAreas(),
            'permissionLevels' => User::permissionLevels(),
        ]);
    }

    public function updateUser(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()?->hasPermission('administration', 'administration'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('users', 'name')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        foreach (array_keys(User::permissionAreas()) as $area) {
            $user->{User::permissionColumn($area)} = User::resolvePermissionLevel($request->input("permissions.$area", []));
        }

        $user->save();

        return redirect()
            ->route('administration.index', ['tab' => 'users'])
            ->with('status', 'Benutzer aktualisiert.');
    }

    public function storeStatus(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->hasPermission('administration', 'editable'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:status,name'],
        ]);

        Status::create($validated);

        return redirect()
            ->to($this->administrationRoute('master-data'))
            ->with('status', 'Status angelegt.');
    }

    public function updateStatus(Request $request, Status $status): RedirectResponse
    {
        abort_unless($request->user()?->hasPermission('administration', 'editable'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('status', 'name')->ignore($status->id)],
        ]);

        $status->update($validated);

        return redirect()
            ->to($this->administrationRoute('master-data'))
            ->with('status', 'Status aktualisiert.');
    }

    public function storeServerKind(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->hasPermission('administration', 'editable'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:server_kinds,name'],
        ]);

        ServerKind::query()->create([
            'name' => trim($validated['name']),
        ]);

        return redirect()
            ->to($this->administrationRoute('master-data'))
            ->with('status', 'Serverart angelegt.');
    }

    public function updateServerKind(Request $request, ServerKind $serverKind): RedirectResponse
    {
        abort_unless($request->user()?->hasPermission('administration', 'editable'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('server_kinds', 'name')->ignore($serverKind->id)],
        ]);

        $serverKind->update([
            'name' => trim($validated['name']),
        ]);

        return redirect()
            ->to($this->administrationRoute('master-data'))
            ->with('status', 'Serverart aktualisiert.');
    }

    public function storeOperatingSystem(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->hasPermission('administration', 'editable'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:operating_systems,name'],
        ]);

        OperatingSystem::query()->create([
            'name' => trim($validated['name']),
        ]);

        return redirect()
            ->to($this->administrationRoute('master-data'))
            ->with('status', 'Betriebssystem angelegt.');
    }

    public function updateOperatingSystem(Request $request, OperatingSystem $operatingSystem): RedirectResponse
    {
        abort_unless($request->user()?->hasPermission('administration', 'editable'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('operating_systems', 'name')->ignore($operatingSystem->id)],
        ]);

        $operatingSystem->update([
            'name' => trim($validated['name']),
        ]);

        return redirect()
            ->to($this->administrationRoute('master-data'))
            ->with('status', 'Betriebssystem aktualisiert.');
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->hasPermission('administration', 'administration'), 403);

        $registrationEnabled = $request->boolean('registration_enabled');

        AppSetting::put('registration_enabled', $registrationEnabled ? '1' : '0');
        config(['app.registration_enabled' => $registrationEnabled]);

        return redirect()
            ->route('administration.index', ['tab' => 'administration', 'subtab' => 'settings'])
            ->with('status', 'Einstellungen gespeichert.');
    }

    public function importCustomers(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->hasPermission('administration', 'editable'), 403);

        if ($this->isConfirmImport($request, 'customers')) {
            $payload = $this->requireImportPreview($request, 'customers');
            $result = $this->executeCustomerImportPlan($request, $payload['plan']);

            return redirect()
                ->route('administration.index', ['tab' => 'administration', 'subtab' => 'import'])
                ->with('status', $result['created'].' Kunden importiert, '.$result['skipped'].' uebersprungen.');
        }

        $validated = $request->validate([
            'csv_file' => ['required', 'file'],
            'fallback_country_code' => ['nullable', 'string', 'size:2'],
        ]);

        [$header, $rows] = $this->readCsvRows($validated['csv_file']);
        $plan = $this->buildCustomerImportPlan($rows, $this->buildHeaderMap($header), strtolower((string) $request->input('fallback_country_code', 'de')));

        if ($request->input('import_mode') !== 'preview') {
            $result = $this->executeCustomerImportPlan($request, $plan);

            return redirect()
                ->route('administration.index', ['tab' => 'administration', 'subtab' => 'import'])
                ->with('status', $result['created'].' Kunden importiert, '.$result['skipped'].' uebersprungen.');
        }

        return $this->redirectWithImportPreview(
            'customers',
            'Kundenimport',
            $plan,
            [
                'fallback_country_code' => strtolower((string) $request->input('fallback_country_code', 'de')),
            ]
        );
    }

    public function importOrbisUServers(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->hasPermission('administration', 'editable'), 403);

        if ($this->isConfirmImport($request, 'orbisu_servers')) {
            $payload = $this->requireImportPreview($request, 'orbisu_servers');
            $result = $this->executeOrbisuImportPlan($request, $payload['plan']);

            return redirect()
                ->route('administration.index', ['tab' => 'administration', 'subtab' => 'import'])
                ->with('status', $result['created'].' Server importiert, '.$result['skipped'].' uebersprungen.');
        }

        $validated = $request->validate([
            'csv_file' => ['required', 'file'],
        ]);

        [$header, $rows] = $this->readCsvRows($validated['csv_file']);
        $plan = $this->buildOrbisuImportPlan($rows, $this->buildHeaderMap($header));

        if ($request->input('import_mode') !== 'preview') {
            $result = $this->executeOrbisuImportPlan($request, $plan);

            return redirect()
                ->route('administration.index', ['tab' => 'administration', 'subtab' => 'import'])
                ->with('status', $result['created'].' Server importiert, '.$result['skipped'].' uebersprungen.');
        }

        return $this->redirectWithImportPreview('orbisu_servers', 'OrbisU Server Import', $plan);
    }

    public function importOasServers(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->hasPermission('administration', 'editable'), 403);

        if ($this->isConfirmImport($request, 'oas_servers')) {
            $payload = $this->requireImportPreview($request, 'oas_servers');
            $result = $this->executeOasImportPlan($request, $payload['plan']);

            return redirect()
                ->route('administration.index', ['tab' => 'administration', 'subtab' => 'import'])
                ->with('status', $result['created'].' OAS-Server importiert, '.$result['skipped'].' uebersprungen.');
        }

        $validated = $request->validate([
            'csv_file' => ['required', 'file'],
        ]);

        [$header, $rows] = $this->readCsvRows($validated['csv_file']);
        $plan = $this->buildOasImportPlan($rows, $this->buildHeaderMap($header));

        if ($request->input('import_mode') !== 'preview') {
            $result = $this->executeOasImportPlan($request, $plan);

            return redirect()
                ->route('administration.index', ['tab' => 'administration', 'subtab' => 'import'])
                ->with('status', $result['created'].' OAS-Server importiert, '.$result['skipped'].' uebersprungen.');
        }

        return $this->redirectWithImportPreview('oas_servers', 'OAS Import', $plan);
    }

    public function updateCity(Request $request, City $city): RedirectResponse
    {
        abort_unless($request->user()?->hasPermission('administration', 'editable'), 403);

        $validated = $request->validate([
            'country_code' => ['required', 'string', 'size:2'],
        ]);

        $city->update([
            'country_code' => strtolower($validated['country_code']),
        ]);

        return redirect()
            ->route('administration.index', ['tab' => 'administration', 'subtab' => 'master-data'])
            ->with('status', 'Land fuer Ort aktualisiert.');
    }

    public function storeCity(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->hasPermission('administration', 'editable'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'country_code' => ['required', 'string', 'size:2'],
        ]);

        City::query()->create([
            'name' => trim($validated['name']),
            'country_code' => strtolower($validated['country_code']),
        ]);

        return redirect()
            ->to($this->administrationMasterDataUrlWithAnchor())
            ->with('status', 'Ort angelegt.');
    }

    public function updateCustomerCity(Request $request, Customer $customer): RedirectResponse
    {
        abort_unless($request->user()?->hasPermission('administration', 'editable'), 403);

        $validated = $request->validate([
            'city_id' => ['nullable', 'integer', 'exists:citys,id'],
            'city_name' => ['nullable', 'string', 'max:255'],
        ]);

        $cityId = $validated['city_id'] ?? null;

        if (! $cityId && ! empty($validated['city_name'])) {
            $cityId = $this->findCityIdByName($validated['city_name']);
        }

        abort_unless($cityId, 422, 'Ort nicht gefunden.');

        $customer->update([
            'city_id' => (int) $cityId,
        ]);

        return redirect()
            ->to($this->administrationMasterDataUrlWithAnchor())
            ->with('status', 'Ort fuer Kunden aktualisiert.');
    }

    private function administrationMasterDataUrlWithAnchor(): string
    {
        return $this->administrationRoute('quality', self::MISSING_CITIES_ANCHOR);
    }

    private function duplicateCustomerValues(string $column): array
    {
        return Customer::query()
            ->select($column)
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->groupBy($column)
            ->havingRaw('COUNT(*) > 1')
            ->pluck($column)
            ->all();
    }

    private function readCsvRows(UploadedFile $file): array
    {
        $path = $file->getRealPath();
        abort_unless($path, 422, 'Datei konnte nicht gelesen werden.');

        $handle = fopen($path, 'rb');
        abort_unless($handle !== false, 422, 'Datei konnte nicht gelesen werden.');

        $header = [];
        $rows = [];

        while (($row = fgetcsv($handle, separator: ';')) !== false) {
            // Normalize encoding cell-by-cell because import files come from multiple external systems.
            $row = array_map(
                fn ($value) => $this->normalizeCsvCell((string) $value),
                $row
            );

            if ($header === []) {
                $header = $row;

                continue;
            }

            if ($row === [null] || $row === ['']) {
                continue;
            }

            $rows[] = $row;
        }

        fclose($handle);

        return [$header, $rows];
    }

    private function normalizeCsvCell(string $value): string
    {
        return trim(mb_convert_encoding($value, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252'));
    }

    private function buildHeaderMap(array $header): array
    {
        $map = [];

        foreach ($header as $index => $column) {
            $normalized = trim((string) $column);
            if ($normalized !== '') {
                $map[$normalized] = $index;
            }
        }

        return $map;
    }

    private function csvValue(array $row, array $headerMap, string $column): ?string
    {
        $index = $headerMap[$column] ?? null;

        return $index === null ? null : ($row[$index] ?? null);
    }

    private function resolveCityId(string $cityName, string $fallbackCountryCode): ?int
    {
        $cityName = trim($cityName);

        if ($cityName === '') {
            return null;
        }

        // Reuse existing city records whenever possible so imports do not create case-based duplicates.
        $existingCityId = $this->findCityIdByName($cityName);
        if ($existingCityId) {
            return $existingCityId;
        }

        $city = City::query()->create([
            'name' => $cityName,
            'country_code' => $fallbackCountryCode ?: 'de',
        ]);

        return $city->id;
    }

    private function extractLeadingNumber(string $value): ?int
    {
        if (preg_match('/^\s*(\d+)/', $value, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    private function extractProjectShortNumber(string $value): ?int
    {
        $value = trim($value);

        if (preg_match('/^\s*(\d+)\s*\//', $value, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    private function findOrCreateCustomerForServerImport(int $userId, int $shortNo, string $customerLabel): Customer
    {
        [$name, $sapNo] = $this->parseCustomerLabel($customerLabel);

        $customer = Customer::query()->firstOrNew(['short_no' => $shortNo]);
        $customer->user_id = $customer->user_id ?: $userId;
        $customer->dynamics_no = $customer->dynamics_no ?: 'x';
        $customer->sap_no = $sapNo !== '' ? $sapNo : ($customer->sap_no ?: '');
        $customer->name = $name !== '' ? $name : ($customer->name ?: 'Unbekannt');
        $customer->save();

        return $customer;
    }

    private function parseCustomerLabel(string $value): array
    {
        $value = trim($value);

        if (preg_match('/^(.*)\(([^()]*)\)\s*$/', $value, $matches)) {
            return [trim($matches[1]), trim($matches[2])];
        }

        return [$value, ''];
    }

    private function parseImportTimestamp(string $value): ?Carbon
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y.m', $value)->endOfMonth();
        } catch (\Throwable $exception) {
            return null;
        }
    }

    private function mapServerType(string $value): string
    {
        $value = mb_strtolower(trim($value));

        return match (true) {
            str_contains($value, 'produktiv') => 'Produktiv',
            str_contains($value, 'test') => 'Test',
            str_contains($value, 'entwick') => 'Entwicklungs',
            str_contains($value, 'schul') => 'Schulungs',
            str_contains($value, 'integr') => 'Integration',
            str_contains($value, 'auswert') => 'Auswerte',
            default => '',
        };
    }

    private function mapOasServerType(string $value): string
    {
        return match (mb_strtolower(trim($value))) {
            'education' => 'Schulungs',
            'produktion' => 'Produktiv',
            'test' => 'Test',
            default => '',
        };
    }

    private function resolveOasServerKindId(string $value): ?int
    {
        $normalized = $this->normalizeOasModuleKey($value);

        if ($normalized === '') {
            return null;
        }

        $serverKindName = [
            'costaccounting' => 'OAS CostAccounting',
            'ehealthxds' => 'OAS EHealth-XDS',
            'fhir' => 'OAS FHIR',
            'fhirbackend' => 'OAS FHIR-Backend',
            'fluidmanagement' => 'OAS Fluidmanagement',
            'medicationbatch' => 'OAS Medication Batch',
            'orbisconnectivityserver' => 'OAS Orbis Connectivity Server',
            'sap' => 'OAS SAP',
            'speech' => 'OAS Speech',
            'singlewithoutmedicationbatch' => 'OAS Std. Single WO Med.',
            'orbisu' => 'Orbis U',
        ][$normalized] ?? null;

        if (! $serverKindName) {
            return null;
        }

        return ServerKind::query()->where('name', $serverKindName)->value('id');
    }

    private function normalizeOasModuleKey(string $value): string
    {
        $value = mb_strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '', $value) ?? '';

        return $value;
    }

    private function findCityIdByName(string $cityName): ?int
    {
        // City names are matched case-insensitively because import sources are inconsistent.
        return City::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower(trim($cityName))])
            ->value('id');
    }

    private function isConfirmImport(Request $request, string $type): bool
    {
        return $request->input('import_mode') === 'confirm' && $request->input('preview_type') === $type;
    }

    private function requireImportPreview(Request $request, string $type): array
    {
        $preview = $request->session()->get(self::IMPORT_PREVIEW_SESSION_KEY);

        abort_unless(
            is_array($preview)
            && ($preview['type'] ?? null) === $type
            && ($preview['token'] ?? null) === $request->input('preview_token'),
            422,
            'Import-Vorschau nicht mehr gueltig. Bitte Vorschau erneut erzeugen.'
        );

        $request->session()->forget(self::IMPORT_PREVIEW_SESSION_KEY);

        return $preview;
    }

    private function redirectWithImportPreview(string $type, string $label, array $plan, array $options = []): RedirectResponse
    {
        $summary = collect($plan)->countBy('action')->all();
        $token = (string) str()->uuid();

        session()->put(self::IMPORT_PREVIEW_SESSION_KEY, [
            'token' => $token,
            'type' => $type,
            'label' => $label,
            'summary' => $summary,
            'plan' => $plan,
            'options' => $options,
        ]);

        return redirect()
            ->route('administration.index', ['tab' => 'administration', 'subtab' => 'import'])
            ->with('status', 'Vorschau fuer '.$label.' erstellt.');
    }

    private function buildCustomerImportPlan(array $rows, array $headerMap, string $fallbackCountryCode): array
    {
        $seenShortNumbers = [];
        $plan = [];

        foreach ($rows as $row) {
            $shortNoValue = trim((string) $this->csvValue($row, $headerMap, 'Kd.Nummer'));
            $sapNo = trim((string) $this->csvValue($row, $headerMap, 'SAP-Nr.'));
            $cityName = trim((string) $this->csvValue($row, $headerMap, 'Ort'));

            if ($shortNoValue === '' || ! is_numeric($shortNoValue)) {
                $plan[] = $this->previewItem('conflict', 'Ungueltige Kd.Nummer', ['short_no' => $shortNoValue, 'sap_no' => $sapNo]);

                continue;
            }

            $shortNo = (int) $shortNoValue;
            if (isset($seenShortNumbers[$shortNo])) {
                $plan[] = $this->previewItem('conflict', 'Doppelte Kd.Nummer in Datei', ['short_no' => $shortNo, 'sap_no' => $sapNo]);

                continue;
            }
            $seenShortNumbers[$shortNo] = true;

            if (Customer::query()->where('short_no', $shortNo)->exists()) {
                $plan[] = $this->previewItem('skip', 'Kunde existiert bereits', ['short_no' => $shortNo, 'sap_no' => $sapNo]);

                continue;
            }

            $plan[] = [
                'action' => 'new',
                'title' => 'Neuer Kunde',
                'details' => [
                    'short_no' => $shortNo,
                    'sap_no' => $sapNo,
                    'city' => $cityName !== '' ? $cityName : '-',
                ],
                'payload' => [
                    'short_no' => $shortNo,
                    'sap_no' => $sapNo,
                    'city_name' => $cityName,
                    'fallback_country_code' => $fallbackCountryCode,
                ],
            ];
        }

        return $plan;
    }

    private function executeCustomerImportPlan(Request $request, array $plan): array
    {
        $created = 0;
        $skipped = 0;

        DB::transaction(function () use ($plan, $request, &$created, &$skipped) {
            foreach ($plan as $item) {
                if (($item['action'] ?? null) !== 'new') {
                    $skipped++;

                    continue;
                }

                $payload = $item['payload'] ?? [];
                $cityId = $this->resolveCityId(
                    (string) ($payload['city_name'] ?? ''),
                    (string) ($payload['fallback_country_code'] ?? 'de')
                );

                Customer::query()->create([
                    'short_no' => (int) $payload['short_no'],
                    'user_id' => (int) $request->user()->id,
                    'sap_no' => (string) $payload['sap_no'],
                    'dynamics_no' => 'x',
                    'name' => 'Unbekannt',
                    'city_id' => $cityId,
                ]);

                $created++;
            }
        });

        return ['created' => $created, 'skipped' => $skipped];
    }

    private function buildOrbisuImportPlan(array $rows, array $headerMap): array
    {
        $plan = [];

        foreach ($rows as $row) {
            $hostname = trim((string) $this->csvValue($row, $headerMap, 'VM-Hostname'));
            $shortNo = $this->extractLeadingNumber((string) $this->csvValue($row, $headerMap, 'Kunde'));

            if ($hostname === '' || ! $shortNo) {
                $plan[] = $this->previewItem('conflict', 'Pflichtfelder fehlen', ['server' => $hostname ?: '-', 'short_no' => $shortNo ?: '-']);

                continue;
            }

            $importUpdatedAt = $this->parseImportTimestamp((string) $this->csvValue($row, $headerMap, 'Aktualisiert'));
            $server = Server::query()->where('servername', $hostname)->first();

            if ($server && $importUpdatedAt && $server->updated_at && $importUpdatedAt->lessThanOrEqualTo($server->updated_at)) {
                $plan[] = $this->previewItem('skip', 'Server in Datenbank ist neuer oder gleich aktuell', ['server' => $hostname, 'short_no' => $shortNo]);

                continue;
            }

            $payload = [
                'short_no' => $shortNo,
                'customer_label' => (string) $this->csvValue($row, $headerMap, 'Kundename (SAP-Nr.)'),
                'type' => $this->mapServerType((string) $this->csvValue($row, $headerMap, 'Umgebung')),
                'servername' => $hostname,
                'int_ip' => trim((string) $this->csvValue($row, $headerMap, 'VM-IP-Addresse')),
                'db_sid' => trim((string) $this->csvValue($row, $headerMap, 'DB-SID')),
            ];

            $plan[] = [
                'action' => $server ? 'update' : 'new',
                'title' => $server ? 'Server wird aktualisiert' : 'Neuer Server',
                'details' => ['server' => $hostname, 'short_no' => $shortNo, 'type' => $payload['type'] ?: '-'],
                'payload' => $payload,
            ];
        }

        return $plan;
    }

    private function executeOrbisuImportPlan(Request $request, array $plan): array
    {
        $created = 0;
        $skipped = 0;

        DB::transaction(function () use ($plan, $request, &$created, &$skipped) {
            foreach ($plan as $item) {
                if (! in_array($item['action'] ?? null, ['new', 'update'], true)) {
                    $skipped++;

                    continue;
                }

                $payload = $item['payload'] ?? [];
                $customer = $this->findOrCreateCustomerForServerImport(
                    (int) $request->user()->id,
                    (int) $payload['short_no'],
                    (string) $payload['customer_label']
                );

                $serverPayload = [
                    'type' => $payload['type'],
                    'server_kind_id' => null,
                    'operating_system_id' => null,
                    'servername' => $payload['servername'],
                    'int_ip' => $payload['int_ip'],
                    'db_sid' => $payload['db_sid'],
                    'customer_id' => $customer->id,
                    'user_id' => (int) $request->user()->id,
                ];

                $server = Server::query()->where('servername', $payload['servername'])->first();
                if ($server) {
                    $server->fill($serverPayload);
                    $server->save();
                } else {
                    Server::query()->create($serverPayload);
                }

                $created++;
            }
        });

        return ['created' => $created, 'skipped' => $skipped];
    }

    private function buildOasImportPlan(array $rows, array $headerMap): array
    {
        $plan = [];

        foreach ($rows as $row) {
            $hostname = trim((string) $this->csvValue($row, $headerMap, 'Hostname'));
            $ipAddress = trim((string) $this->csvValue($row, $headerMap, 'IP-Adresse'));
            $shortNo = $this->extractProjectShortNumber((string) $this->csvValue($row, $headerMap, 'Projekt / SAP Nr'));

            if ($hostname === '' || $ipAddress === '' || ! $shortNo) {
                $plan[] = $this->previewItem('conflict', 'Pflichtfelder fehlen', ['server' => $hostname ?: '-', 'short_no' => $shortNo ?: '-']);

                continue;
            }

            $customer = Customer::query()->where('short_no', $shortNo)->first();
            if (! $customer) {
                $plan[] = $this->previewItem('conflict', 'Kunde fuer Short-Nummer nicht gefunden', ['server' => $hostname, 'short_no' => $shortNo]);

                continue;
            }

            $payload = [
                'type' => $this->mapOasServerType((string) $this->csvValue($row, $headerMap, 'Typ')),
                'server_kind_id' => $this->resolveOasServerKindId((string) $this->csvValue($row, $headerMap, 'OAS-Modul')),
                'servername' => $hostname,
                'int_ip' => $ipAddress,
                'db_sid' => trim((string) $this->csvValue($row, $headerMap, 'Datenbank')),
                'customer_id' => $customer->id,
            ];

            $server = Server::query()->where('servername', $hostname)->first();
            $plan[] = [
                'action' => $server ? 'update' : 'new',
                'title' => $server ? 'OAS-Server wird aktualisiert' : 'Neuer OAS-Server',
                'details' => ['server' => $hostname, 'short_no' => $shortNo, 'customer' => $customer->name],
                'payload' => $payload,
            ];
        }

        return $plan;
    }

    private function executeOasImportPlan(Request $request, array $plan): array
    {
        $created = 0;
        $skipped = 0;

        DB::transaction(function () use ($plan, $request, &$created, &$skipped) {
            foreach ($plan as $item) {
                if (! in_array($item['action'] ?? null, ['new', 'update'], true)) {
                    $skipped++;

                    continue;
                }

                $payload = $item['payload'] ?? [];
                $serverPayload = [
                    'type' => $payload['type'],
                    'server_kind_id' => $payload['server_kind_id'],
                    'operating_system_id' => null,
                    'servername' => $payload['servername'],
                    'int_ip' => $payload['int_ip'],
                    'db_sid' => $payload['db_sid'],
                    'customer_id' => $payload['customer_id'],
                    'user_id' => (int) $request->user()->id,
                ];

                $server = Server::query()->where('servername', $payload['servername'])->first();
                if ($server) {
                    $server->fill($serverPayload);
                    $server->save();
                } else {
                    Server::query()->create($serverPayload);
                }

                $created++;
            }
        });

        return ['created' => $created, 'skipped' => $skipped];
    }

    private function previewItem(string $action, string $title, array $details): array
    {
        return [
            'action' => $action,
            'title' => $title,
            'details' => $details,
        ];
    }
}
