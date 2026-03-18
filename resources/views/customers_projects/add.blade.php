@extends('layouts.app')
@section('title', 'Add Customer + Project')
@section('content')
    {{ html()->form()->route('customers_projects.store')->id('customer-project-form')->open() }}
    {{ html()->hidden('customer_id')->id('customer_id') }}
    {{ html()->hidden('city_id')->id('city_id') }}
    <div id="prodpagecontainer">
        <table id="pouetbox_prodmain">
            <tbody>
            <tr id="prodheader">
                <th colspan='1'>
                    <span id='title'><big>Add Customer</big></span>
                </th>
            </tr>
            <tr>
                <td>
                    <table id="stattable">
                        <tr>
                            <td>Short No.:</td>
                            <td>{{ html()->text('short_no')->id('short_no') }}</td>
                        </tr>
                        <tr id="existing-customer-row" style="display: none;">
                            <td>Existing Customer:</td>
                            <td><span id="existing-customer-details"></span></td>
                        </tr>
                        <tr class="new-customer-field">
                            <td>SAP No.:</td>
                            <td>{{ html()->text('sap_no')->id('sap_no') }}</td>
                        </tr>
                        <tr class="new-customer-field">
                            <td>Dynamics No.:</td>
                            <td>{{ html()->text('customer_dynamics_no')->id('customer_dynamics_no') }}</td>
                        </tr>
                        <tr class="new-customer-field">
                            <td>Customer Name:</td>
                            <td>{{ html()->text('customer_name')->id('customer_name') }}</td>
                        </tr>
                        <tr class="new-customer-field">
                            <td>Ort:</td>
                            <td>{{ html()->text('city_name')->id('city_name') }}</td>
                        </tr>
                        <tr id="country-row" class="new-customer-field" style="display: none;">
                            <td>Land:</td>
                            <td>
                                <span id="country-display" style="display: none;"></span>
                                {{ html()->select('country_code', $countrys)->id('country_code')->attribute('style', 'display: none;') }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div id="prodpagecontainer">
        <table id="pouetbox_prodmain">
            <tbody>
            <tr id="prodheader">
                <th colspan='1'>
                    <span id='title'><big>Add Project</big></span>
                </th>
            </tr>
            <tr>
                <td>
                    <table id="stattable">
                        <tr>
                            <td>Project Dynamics ID:</td>
                            <td>{{ html()->text('project_dynamics_id') }}</td>
                        </tr>
                        <tr>
                            <td>Project Name:</td>
                            <td>{{ html()->text('project_name') }}</td>
                        </tr>
                        <tr>
                            <td>Start Date:</td>
                            <td>{{ html()->input('date', 'start_date', now()->toDateString()) }}</td>
                        </tr>
                        <tr>
                            <td>End Date:</td>
                            <td>{{ html()->input('date', 'end_date', now()->toDateString()) }}</td>
                        </tr>
                        <tr>
                            <td>Hours:</td>
                            <td>{{ html()->input('number', 'hours')->attribute('min', 0) }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>{{ html()->submit('Submit') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    {{ html()->form()->close() }}

    <script>
        $(document).ready(function() {
            var lookupCustomerTimer = null;
            var lookupCustomerRequest = null;
            var $shortNo = $('#short_no');
            var $existingRow = $('#existing-customer-row');
            var $existingDetails = $('#existing-customer-details');
            var $newCustomerFields = $('.new-customer-field');
            var $customerId = $('#customer_id');
            var $cityName = $('#city_name');
            var $cityId = $('#city_id');
            var $countryRow = $('#country-row');
            var $countryDisplay = $('#country-display');
            var $countrySelect = $('#country_code');

            function resetCustomer() {
                $existingRow.hide();
                $existingDetails.text('');
                $customerId.val('');
                $newCustomerFields.show();
            }

            function showExistingCustomer(data) {
                var details = '(' + data.short_no + ') ' + data.city + ' - ' + data.name;
                if (data.sap_no) {
                    details += ' | SAP: ' + data.sap_no;
                }
                if (data.dynamics_no) {
                    details += ' | Dynamics: ' + data.dynamics_no;
                }

                $existingDetails.text(details);
                $customerId.val(data.id);
                $existingRow.show();
                $newCustomerFields.hide();
                resetCity();
            }

            function lookupCustomer() {
                var shortNo = $shortNo.val().trim();
                if (!shortNo) {
                    resetCustomer();
                    return;
                }

                if (lookupCustomerRequest && lookupCustomerRequest.readyState !== 4) {
                    lookupCustomerRequest.abort();
                }

                lookupCustomerRequest = $.get('{{ route('customers_projects.lookup_customer') }}', { short_no: shortNo })
                    .done(function(data) {
                        if ($shortNo.val().trim() !== shortNo) {
                            return;
                        }

                        if (data && data.found) {
                            showExistingCustomer(data);
                        } else {
                            resetCustomer();
                        }
                    })
                    .fail(function() {
                        if ($shortNo.val().trim() === shortNo) {
                            resetCustomer();
                        }
                    });
            }

            function resetCity() {
                $cityId.val('');
                $countryRow.hide();
                $countryDisplay.hide().text('');
                $countrySelect.hide().val('');
            }

            function lookupCity() {
                if ($newCustomerFields.filter(':visible').length === 0) {
                    return;
                }

                var cityName = $cityName.val().trim();
                if (!cityName) {
                    resetCity();
                    return;
                }

                $.get('{{ route('customers_projects.lookup_city') }}', { name: cityName })
                    .done(function(data) {
                        if (data && data.found) {
                            $cityId.val(data.id);
                            $countryDisplay.text((data.country_code || '').toUpperCase());
                            $countryRow.show();
                            $countryDisplay.show();
                            $countrySelect.hide().val('');
                        } else {
                            $cityId.val('');
                            $countryRow.show();
                            $countryDisplay.hide().text('');
                            $countrySelect.show();
                        }
                    })
                    .fail(function() {
                        resetCity();
                    });
            }

            $shortNo.on('input', function() {
                if (lookupCustomerTimer) {
                    clearTimeout(lookupCustomerTimer);
                }
                lookupCustomerTimer = setTimeout(lookupCustomer, 300);
            });
            $shortNo.on('blur', lookupCustomer);
            $cityName.on('blur', lookupCity);
            $cityName.on('keyup', function() {
                if ($cityId.val()) {
                    resetCity();
                }
            });
        });
    </script>
@endsection
