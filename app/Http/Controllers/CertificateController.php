<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Customer;
use App\Models\Server;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function update(Request $request){
        $c = Customer::whereId($request->get('customer_id'))->first();
        $c->intermediate_cert_raw = $request->get('intermediate');
        $c->root_cert_raw = $request->get('root');
        $c->save();

        $s = Server::whereId($request->get('server_id'))->first();
        $s->server_cert_raw = $request->get('server');
        $s->private_key_raw = $request->get('private_key');
        $s->save();

        return redirect()->back();
    }

    public function import_pfx(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'server_id' => ['required', 'integer', 'exists:servers,id'],
            'pfx_file' => ['required', 'file', 'max:20480'],
            'pfx_password' => ['nullable', 'string'],
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);
        $server = Server::findOrFail($validated['server_id']);
        $pkcs12 = file_get_contents($request->file('pfx_file')->getRealPath());
        $certificates = [];

        if (!openssl_pkcs12_read($pkcs12, $certificates, $validated['pfx_password'] ?? '')) {
            return redirect()
                ->back()
                ->withErrors(['pfx_file' => 'Die PFX-Datei konnte nicht gelesen werden. Bitte Datei und Passwort pruefen.']);
        }

        $extraCertificates = $this->normalizeExtraCertificates($certificates['extracerts'] ?? []);
        [$intermediateCertificates, $rootCertificates] = $this->splitCertificateChain($extraCertificates);

        $server->server_cert_raw = $certificates['cert'] ?? null;
        $server->private_key_raw = $certificates['pkey'] ?? null;
        $server->save();

        if ($intermediateCertificates !== []) {
            $customer->intermediate_cert_raw = implode("\n", $intermediateCertificates);
        }

        if ($rootCertificates !== []) {
            $customer->root_cert_raw = implode("\n", $rootCertificates);
        }

        $customer->save();

        LogHelper::log('server', $server->id, 'Certificate', 'Import certificates from PFX: '.$request->file('pfx_file')->getClientOriginalName());

        return redirect()->back();
    }

    private function normalizeExtraCertificates($extraCertificates): array
    {
        if (is_string($extraCertificates)) {
            preg_match_all('/-----BEGIN CERTIFICATE-----(.*?)-----END CERTIFICATE-----/s', $extraCertificates, $matches);

            return array_map(function ($certificateBody) {
                return "-----BEGIN CERTIFICATE-----".$certificateBody."-----END CERTIFICATE-----";
            }, $matches[1] ?? []);
        }

        return is_array($extraCertificates) ? array_values(array_filter($extraCertificates)) : [];
    }

    private function splitCertificateChain(array $certificates): array
    {
        $intermediateCertificates = [];
        $rootCertificates = [];

        foreach ($certificates as $certificate) {
            $parsed = openssl_x509_parse($certificate);
            $subject = $parsed['subject']['CN'] ?? null;
            $issuer = $parsed['issuer']['CN'] ?? null;

            if ($subject !== null && $subject === $issuer) {
                $rootCertificates[] = $certificate;
                continue;
            }

            $intermediateCertificates[] = $certificate;
        }

        return [$intermediateCertificates, $rootCertificates];
    }
}
