<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription Print Option</title>
    <style>
        .box {
            float: left;
            border: 3px solid #199bbf !important;
            font-size: 12px;
        }

        .header {
            float: left;
            background: #AD235E;
            color: #FFFFFF;
        }

        body {
            font-family: verdana !important;
            font-weight: normal;
            font-size: 14px;
        }

        p {
            padding: 0px;
            margin: 0px;
        }

        .drug-table {
            width: 90%;
            border-collapse: collapse;
        }

        .drug-table th,
        .drug-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        /* Styles for the print selection box */
        .print-selection {
            width: 30%;
            font-family: verdana;
            margin: 0 auto;
        }

        .print-selection select,
        .print-selection input {
            margin-top: 10px;
        }

        .print-selection input {
            cursor: pointer;
            background-color: #199bbf;
            color: white;
            padding: 5px;
            border: none;
            font-size: 12px;
        }

        /* Hide footer during printing */
        @media screen {
            .footer {
                display: none;
            }
        }

        @media print {
            .footer {
                position: fixed;
                bottom: 0;
            }
        }

        @media print {
            .footer {
                display: none;
            }

            body {
                font-size: 12px;
            }
        }
    </style>
</head>

<body>

    <div id="printOptions" class="print-selection">
        <div style="background-color: #199bbf; padding: 2px 5px; color: #fff; font-size: 12px;">
            Select Type to print:
        </div>
        <div style="border: 2px solid #199bbf; padding: 10px;">
            <select id="printType">
                <option value="a4h">A4 With Header</option>
                <option value="a4">A4 Without Header</option>
                <option value="a5h">A5 With Header</option>
                <option value="a5">A5 Without Header</option>
            </select>
            <input type="button" value="Print" onclick="printType()" />
        </div>
    </div>

    <div id="prescriptionContent" style="display: none;">
        <div class="content" style="width: 100%; min-height: 300px;">
            <div style="width: 100%; height: 300px; margin-bottom: 4px;">
                <div id="prescriptionHeader">
                    <div style="padding-right:15px;padding-top:15px;float:right;width:5%;">

                        <img class="retina-ready" width="60" height="60" alt="" src="https://www.hygeiaes.co/img/logo-big.png">
                    </div>
                    <div style="padding:15px 10px 0px 20px;float:left;width:35%;">


                        <div class="box-header-drname">

                            <p><b>Dr. John Doe
                                    <div id="companyName"></div>
                                </b>
                            </p>
                            <p></p>

                        </div>
                    </div>
                    </div>
                    <div class="box-top" style="width: 98%; float: left; margin-top: 200px; margin: 1%; border-bottom: 2px solid #199bbf; border-top: 2px solid #199bbf; padding: 5px 0;">
                        <div id="patientName" style="width: 33%; float: left; padding-top: 3px;"></div>
                        <div id="patientInfo" style="width: 30%; float: left; padding-top: 3px;" align="center"></div>
                        <div id="prescriptionDate" style="width: 36%; float: right; padding-top: 3px; text-align: right; vertical-align: middle;"></div>
                    </div>

                    <div style="width: 98%; float: left; margin: 0% 1% 1% 1%; border-bottom: 1px solid #ccc; text-align: right; padding-bottom: 10px; font-size: 11px;">
                        <div style="width: auto; float: right;">
                            <div style="width: auto; float: left; text-align: center !important; padding-right: 15px;">
                                <img src='https://www.hygeiaes.co/img/Morning.png'> Morning
                            </div>
                            <div style="width: auto; float: left; text-align: center !important; padding-right: 15px;">
                                <img src='https://www.hygeiaes.co/img/Noon.png'> Afternoon
                            </div>
                            <div style="width: auto; float: left; text-align: center !important; padding-right: 15px;">
                                <img src='https://www.hygeiaes.co/img/Evening.png'> Evening
                            </div>
                            <div style="width: auto; float: left; text-align: center !important; padding-right: 15px;">
                                <img src='https://www.hygeiaes.co/img/Night.png'> Night
                            </div>
                        </div>
                    </div>
                

                <div style="float: left; width: 98%; margin: 0 1%;">
                    <table style="width: 90%;" class="drug-table" align="center">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="20%" style="text-align: left; padding: 5px 0;">Drug Name</th>
                                <th width="4%">Days</th>
                                <th style="width: 5%; text-align: center !important;" title="drugmorning"><img src='https://www.hygeiaes.co/img/Morning.png'></th>
                                <th style="width: 5%; text-align: center !important;" title="drugafternoon"><img src='https://www.hygeiaes.co/img/Noon.png'></th>
                                <th style="width: 5%; text-align: center !important;" title="drugevening"><img src='https://www.hygeiaes.co/img/Evening.png'></th>
                                <th style="width: 5%; text-align: center !important;" title="drugnight"><img src='https://www.hygeiaes.co/img/Night.png'></th>
                                <th width="8%">AF/BF</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="drugTableBody">
                            <!-- Dynamic content will be inserted here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Check URL and show content accordingly
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            const printType = urlParams.get('print_type');
            const url = window.location.href;
            const prescriptionTemplateId = url.split('/').pop(); // Extract ID from URL
            const reportUrl = `https://login-users.hygeiaes.com/prescription/getPrintPrescriptionById/${prescriptionTemplateId}`;
            if (['a4h', 'a5h'].includes(printType)) {
                // Hide the header if print_type is 'a4h' or 'a5h'
                document.getElementById('prescriptionHeader').style.display = 'block';
            } else {
                // Otherwise, show the header
                document.getElementById('prescriptionHeader').style.display = 'none';
            }
            // Check URL and show content accordingly
            if (printType) {
                document.getElementById('printOptions').style.display = 'none';
                document.getElementById('prescriptionContent').style.display = 'block';
                // Fetch and display the prescription details on page load
                loadPrescriptionDetails(reportUrl);
            }
        };
        const conditionMap = {
            '1': 'Before Food',
            '2': 'After Food',
            '3': 'With Food',
            '4': 'SOS',
        };

        function loadPrescriptionDetails(reportUrl) {
            $.ajax({
                url: reportUrl,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
  console.log("API Response:", data);

  if (data && data.prescription_details) {
    const prescription = data.prescription;
    const drugDetails = Object.values(data.prescription_details);

    // Patient Info
    document.getElementById('companyName').textContent = prescription.employee_corporate_name || '';
    document.getElementById('patientName').innerHTML = `Name: <b>${prescription.employee_firstname || ''} ${prescription.employee_lastname || ''}</b>`;
    document.getElementById('patientInfo').innerHTML = `Age: ${prescription.employee_age}, ${prescription.employee_gender}`;

    const date = new Date(prescription.prescription_date);
    const formattedDate = `${String(date.getDate()).padStart(2, '0')}-${String(date.getMonth() + 1).padStart(2, '0')}-${date.getFullYear()}`;
    document.getElementById('prescriptionDate').innerHTML = `Date: <b>${formattedDate}</b>`;

    // Render Table
    const drugTableBody = document.getElementById('drugTableBody');
    drugTableBody.innerHTML = ''; // Clear

    drugDetails.forEach((detail, index) => {
      const af_bf_display = conditionMap[detail.intake_condition] || 'N/A';

      const row = document.createElement('tr');
      row.innerHTML = `
        <td style="text-align:center;">${index + 1}</td>
        <td>${detail.drug_name || 'N/A'}</td>
        <td style="text-align:center;">${detail.prescribed_days}</td>
        <td style="text-align:center;">${detail.morning || '0'}</td>
        <td style="text-align:center;">${detail.afternoon || '0'}</td>
        <td style="text-align:center;">${detail.evening || '0'}</td>
        <td style="text-align:center;">${detail.night || '0'}</td>
        <td style="text-align:center;">${af_bf_display}</td>
        <td style="text-align:center;">${detail.remarks || ''}</td>
      `;
      drugTableBody.appendChild(row);
    });
  } else {
    console.log("No prescription details found or invalid data.");
  }
}
,
                error: function(xhr, status, error) {
                    console.error('Error fetching prescription report: ' + error);
                }
            });
        }


        function printType() {
            const selectedType = document.getElementById('printType').value;
            let currentUrl = window.location.href;

            // Check if the current URL already has query parameters
            if (currentUrl.indexOf('?') !== -1) {
                // If there are query parameters, append &print_type=selectedType
                currentUrl += `&print_type=${selectedType}`;
            } else {
                // If there are no query parameters, append ?print_type=selectedType
                currentUrl += `?print_type=${selectedType}`;
            }
            if (selectedType) {
                // Dynamically fetch the prescriptionId from the URL path
                const urlPath = window.location.pathname; // Get the full URL path
                const pathParts = urlPath.split('/'); // Split the path by '/'
                const prescriptionId = pathParts[pathParts.length - 1]; // Get the last part, which is the prescriptionId

                // Construct the URL with the selected print_type (append print_type to the URL)
                const url = currentUrl; // Log the URL (optional, for debugging)
                console.log("Redirecting to:", url);

                // Redirect the user to the new URL
                window.location.href = url;
                //window.open(url, '_blank');
            } else {
                console.log("Please select a print type.");
            }
        }
    </script>

</body>

</html>