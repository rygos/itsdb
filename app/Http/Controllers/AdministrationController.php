<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\City;
use App\Models\Customer;
use App\Models\Server;
use App\Models\Status;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdministrationController extends Controller
{
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
            'cities' => City::query()->orderBy('name')->get(),
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

        if (!empty($validated['password'])) {
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
            ->route('administration.index', ['tab' => 'statuses'])
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
            ->route('administration.index', ['tab' => 'statuses'])
            ->with('status', 'Status aktualisiert.');
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

        $validated = $request->validate([
            'csv_file' => ['required', 'file'],
            'fallback_country_code' => ['nullable', 'string', 'size:2'],
        ]);

        [$header, $rows] = $this->readCsvRows($validated['csv_file']);
        $headerMap = $this->buildHeaderMap($header);
        $importedCount = 0;
        $skippedCount = 0;

        DB::transaction(function () use ($rows, $headerMap, $request, &$importedCount, &$skippedCount) {
            $seenShortNumbers = [];

            foreach ($rows as $row) {
                $shortNo = trim((string) $this->csvValue($row, $headerMap, 'Kd.Nummer'));
                if ($shortNo === '' || !is_numeric($shortNo)) {
                    $skippedCount++;
                    continue;
                }

                $shortNo = (int) $shortNo;
                if (isset($seenShortNumbers[$shortNo])) {
                    $skippedCount++;
                    continue;
                }
                $seenShortNumbers[$shortNo] = true;

                if (Customer::query()->where('short_no', $shortNo)->exists()) {
                    $skippedCount++;
                    continue;
                }

                $cityId = $this->resolveCityId(
                    trim((string) $this->csvValue($row, $headerMap, 'Ort')),
                    strtolower((string) $request->input('fallback_country_code', 'de'))
                );

                $customer = new Customer();
                $customer->short_no = $shortNo;
                $customer->user_id = $request->user()->id;
                $customer->sap_no = trim((string) $this->csvValue($row, $headerMap, 'SAP-Nr.'));
                $customer->dynamics_no = 'x';
                $customer->name = 'Unbekannt';
                $customer->city_id = $cityId;
                $customer->save();

                $importedCount++;
            }
        });

        return redirect()
            ->route('administration.index', ['tab' => 'administration', 'subtab' => 'import'])
            ->with('status', $importedCount . ' Kunden importiert, ' . $skippedCount . ' uebersprungen.');
    }

    public function importOrbisUServers(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->hasPermission('administration', 'editable'), 403);

        $validated = $request->validate([
            'csv_file' => ['required', 'file'],
        ]);

        [$header, $rows] = $this->readCsvRows($validated['csv_file']);
        $headerMap = $this->buildHeaderMap($header);
        $importedCount = 0;
        $skippedCount = 0;

        DB::transaction(function () use ($rows, $headerMap, $request, &$importedCount, &$skippedCount) {
            foreach ($rows as $row) {
                $hostname = trim((string) $this->csvValue($row, $headerMap, 'VM-Hostname'));
                $shortNo = $this->extractLeadingNumber((string) $this->csvValue($row, $headerMap, 'Kunde'));

                if ($hostname === '' || !$shortNo) {
                    $skippedCount++;
                    continue;
                }

                $customer = $this->findOrCreateCustomerForServerImport(
                    $request->user()->id,
                    $shortNo,
                    (string) $this->csvValue($row, $headerMap, 'Kundename (SAP-Nr.)')
                );

                $importUpdatedAt = $this->parseImportTimestamp((string) $this->csvValue($row, $headerMap, 'Aktualisiert'));
                $server = Server::query()->where('servername', $hostname)->first();

                if ($server && $importUpdatedAt && $server->updated_at && $importUpdatedAt->lessThanOrEqualTo($server->updated_at)) {
                    $skippedCount++;
                    continue;
                }

                $payload = [
                    'type' => $this->mapServerType((string) $this->csvValue($row, $headerMap, 'Umgebung')),
                    'servername' => $hostname,
                    'int_ip' => trim((string) $this->csvValue($row, $headerMap, 'VM-IP-Addresse')),
                    'db_sid' => trim((string) $this->csvValue($row, $headerMap, 'DB-SID')),
                    'customer_id' => $customer->id,
                    'user_id' => $request->user()->id,
                ];

                if ($server) {
                    $server->fill($payload);
                    $server->save();
                } else {
                    Server::query()->create($payload);
                }

                $importedCount++;
            }
        });

        return redirect()
            ->route('administration.index', ['tab' => 'administration', 'subtab' => 'import'])
            ->with('status', $importedCount . ' Server importiert, ' . $skippedCount . ' uebersprungen.');
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
            ->route('administration.index', ['tab' => 'administration', 'subtab' => 'import'])
            ->with('status', 'Land fuer Ort aktualisiert.');
    }

    private function readCsvRows(UploadedFile $file): array
    {
        $content = file_get_contents($file->getRealPath());
        $content = $this->normalizeCsvContent($content ?: '');
        $lines = preg_split("/\r\n|\n|\r/", trim($content));
        $rows = array_map(static fn ($line) => str_getcsv($line, ';'), $lines ?: []);

        $header = array_shift($rows) ?: [];

        return [$header, $rows];
    }

    private function normalizeCsvContent(string $content): string
    {
        return mb_convert_encoding($content, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
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
        if ($cityName === '') {
            return null;
        }

        $existingCity = City::query()->whereRaw('LOWER(name) = ?', [mb_strtolower($cityName)])->first();
        if ($existingCity) {
            return $existingCity->id;
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
}
