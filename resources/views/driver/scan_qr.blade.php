@extends('layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <style>
        .qr-scan-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            min-height: 80vh;
            background-color: #f8f9fa;
        }

        .qr-scan-card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
            text-align: center;
            max-width: 600px;
            width: 100%;
        }

        .qr-scan-card h2 {
            color: #007bff;
            font-size: 2.2em;
            margin-bottom: 20px;
        }

        #qr-reader {
            width: 100%;
            max-width: 400px;
            /* Adjust as needed */
            margin: 0 auto 20px auto;
            border: 2px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            display: block;
            /* Ensure it's visible */
        }

        /* Style for the video feed within the scanner */
        #qr-reader video {
            width: 100% !important;
            height: auto !important;
        }

        /* Hide the "Powered by ScanApp" link and other dashboard elements */
        #qr-reader__dashboard {
            /* display: none !important; */
        }

        .qr-reader-region a {
            /* display: none !important; Hide the link if it's rendered outside dashboard */
        }

        .scan-result {
            margin-top: 20px;
            font-size: 1.1em;
            font-weight: bold;
        }

        .scan-result.success {
            color: #28a745;
            /* Green */
        }

        .scan-result.error {
            color: #dc3545;
            /* Red */
        }

        .scan-result.info {
            color: #007bff;
            /* Blue */
        }

        .manual-input {
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }

        .manual-input h3 {
            margin-bottom: 15px;
            color: #343a40;
        }

        .manual-input form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            align-items: center;
        }

        .manual-input input[type="text"] {
            width: 80%;
            padding: 12px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 1em;
        }

        .manual-input button {
            padding: 12px 25px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }

        .manual-input button:hover {
            background-color: #0056b3;
        }
    </style>
@endsection

@section('content')
    <div class="qr-scan-container">
        <div class="qr-scan-card">
            <h2>Scan Booking QR Code</h2>
            <div id="qr-reader"></div>
            <div id="qr-reader-results" class="scan-result"></div>
            {{-- Flash messages from Laravel (e.g., success/error from controller) --}}
            {{-- @include('partials.flash-messages') --}}

            <div class="manual-input">
                <h3>Or Enter Booking UUID Manually</h3>
                <form id="manualScanForm" action="{{ route('driver.scan.qr.process') }}" method="POST">
                    @csrf
                    <input type="text" name="qr_data"
                        placeholder="Enter Booking UUID (e.g., {'bookingId': '...' }) or JSON" required>
                    {{-- Hidden input for the expected booking UUID from the dashboard --}}
                    <input type="hidden" name="expected_booking_uuid" id="expectedBookingUuidInput"
                        value="{{ $uuid }}">
                    <button type="submit">Process UUID</button>
                </form>
            </div>
            <a href="{{ route('driver.dashboard') }}" class="btn-primary"
                style="background-color: #6c757d; margin-top: 30px; display: inline-block;">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        let html5QrcodeScanner;
        let onceScanned = false; // Flag to prevent multiple submissions

        function onScanSuccess(decodedText, decodedResult) {
            if (onceScanned) {
                console.log("Scan already processed, ignoring new scan.");
                return; // Prevent processing if already submitted
            }

            console.log(`Code matched = ${decodedText}`, decodedResult);
            document.getElementById('qr-reader-results').innerText = `Scanned: ${decodedText}`;
            document.getElementById('qr-reader-results').className = 'scan-result success'; // Add success class

            onceScanned = true; // Set flag to true

            // Stop the scanner immediately and then submit the data
            html5QrcodeScanner.clear().then(() => {
                console.log("QR Code scanner cleared and stopped.");
                submitScannedData(decodedText);
            }).catch((err) => {
                console.error("Error stopping QR scanner:", err);
                submitScannedData(decodedText); // Still try to submit even if stop fails
            });
        }

        function onScanFailure(error) {
            // handle scan failure, usually just log it. Avoid flooding console.
            // console.warn(`Code scan error = ${error}`);
            // Provide user feedback that scanning is active, only if not already scanned
            if (!onceScanned) {
                const resultsDiv = document.getElementById('qr-reader-results');
                if (resultsDiv.innerText === '' || resultsDiv.className !== 'scan-result info') {
                    resultsDiv.innerText = `Scanning... (Point camera at QR code)`;
                    resultsDiv.className = 'scan-result info';
                }
            }
        }

        function submitScannedData(data) {
            const form = document.getElementById('manualScanForm');
            const qrDataInput = form.querySelector('input[name="qr_data"]');
            const expectedBookingUuidInput = document.getElementById('expectedBookingUuidInput');

            qrDataInput.value = data; // Set the scanned data to the input field

            // Set the expected booking UUID from the URL
            // const urlParams = new URLSearchParams(window.location.search);
            // const expectedUuid = urlParams.get('expected_booking_uuid');
            // console.log(urlParams);

            // if (expectedUuid) {
            //     expectedBookingUuidInput.value = expectedUuid;
            // } else {
            //     // If no expected UUID is passed, ensure the input is empty or null
            //     expectedBookingUuidInput.value = '';
            // }

            form.submit(); // Submit the form
        }

        document.addEventListener('DOMContentLoaded', (event) => {
            // Initialize the scanner when the DOM is fully loaded
            html5QrcodeScanner = new Html5QrcodeScanner(
                "qr-reader", {
                    fps: 10,
                    qrbox: {
                        width: 250,
                        height: 250
                    },
                    facingMode: {
                        exact: "environment"
                    },
                    supportedScanTypes: [
                        Html5QrcodeScanType.SCAN_TYPE_CAMERA,
                        Html5QrcodeScanType.SCAN_TYPE_FILE
                    ],
                },
                /* verbose= */
                false
            );

            // Render the scanner. This will prompt for camera access.
            html5QrcodeScanner.render(onScanSuccess, onScanFailure)
                .then(() => {
                    console.log("QR scanner rendered. Waiting for camera permission.");
                })
                .catch((err) => {
                    console.error("Failed to render QR scanner:", err);
                    document.getElementById('qr-reader-results').innerText =
                        `Error: Could not start camera. ${err}`;
                    document.getElementById('qr-reader-results').className = 'scan-result error';
                });

            // Add a cleanup function when leaving the page (good practice to release camera)
            window.addEventListener('beforeunload', () => {
                if (html5QrcodeScanner && html5QrcodeScanner.getState() !== 0) {
                    // If scanner is initialized and running
                    console.log("Stopping QR scanner on page unload.");
                    html5QrcodeScanner.clear().catch(console.error); // Use clear() for a full stop
                }
            });
        });
    </script>
@endsection
