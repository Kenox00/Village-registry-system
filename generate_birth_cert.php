<?php
require_once 'includes/auth.php';
require_once 'pdf/dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

require_login();

// Get birth record ID
$birth_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$birth_id) {
    header('Location: view_births.php?error=Invalid birth record ID');
    exit();
}

// Fetch birth record
$query = "SELECT b.*, u.name as registered_by_name 
          FROM birth_records b 
          JOIN users u ON b.registered_by = u.id 
          WHERE b.id = ?";
$result = execute_query($query, 'i', [$birth_id]);

if (!$result || $result->num_rows == 0) {
    header('Location: view_births.php?error=Birth record not found');
    exit();
}

$birth_record = $result->fetch_assoc();

// Generate certificate HTML content
$certificate_content = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Birth Certificate</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            margin: 20px;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 20px;
        }
        .logo {
            margin-bottom: 15px;
        }
        .logo img {
            width: 80px;
            height: 80px;
        }
        .title {
            font-size: 28px;
            color: #2c3e50;
            margin: 10px 0 5px 0;
            font-weight: bold;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 22px;
            color: #34495e;
            margin: 5px 0 15px 0;
            font-weight: bold;
            text-transform: uppercase;
        }
        .cert-number {
            border: 2px solid #2c3e50;
            display: inline-block;
            padding: 10px 15px;
            margin: 10px 0;
            font-weight: bold;
            font-size: 14px;
            background-color: #f8f9fa;
        }
        .content {
            margin: 30px 0;
        }
        .intro-text {
            font-size: 16px;
            text-align: center;
            margin: 30px 0;
            font-style: italic;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }
        .details-table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
        }
        .details-table .label {
            background: #f8f9fa;
            width: 35%;
            font-weight: bold;
            color: #2c3e50;
        }
        .details-table .value {
            background: white;
        }
        .footer-note {
            margin: 40px 0;
            text-align: center;
            font-size: 14px;
            color: #666;
            font-style: italic;
            padding: 20px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
        }
        .signatures {
            margin-top: 60px;
        }
        .signatures table {
            width: 100%;
        }
        .signatures td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        .signature-line {
            border-top: 2px solid #000;
            width: 200px;
            margin: 40px auto 0 auto;
            padding-top: 10px;
        }
        .signature-line strong {
            font-size: 14px;
        }
        .signature-line small {
            font-size: 12px;
            color: #666;
        }
        .issue-info {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 72px;
            color: rgba(0, 0, 0, 0.05);
            z-index: -1;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="watermark">OFFICIAL</div>
    
    <div class="header">
        <div class="logo">
            <img src="assets/logo.png" alt="Official Logo" />
        </div>
        <h1 class="title">Republic of Rwanda</h1>
        <h2 class="subtitle">Birth Certificate</h2>
        <div class="cert-number">
            Certificate No: BC-' . str_pad($birth_record['id'], 6, '0', STR_PAD_LEFT) . '
        </div>
    </div>

    <div class="content">
        <p class="intro-text">
            This is to certify that according to the records maintained by the Village Birth and Death Register System, 
            the following information has been duly recorded:
        </p>
        
        <table class="details-table">
            <tr>
                <td class="label">Child\'s Full Name:</td>
                <td class="value">' . htmlspecialchars($birth_record['child_name']) . '</td>
            </tr>
            <tr>
                <td class="label">Date of Birth:</td>
                <td class="value">' . date('F d, Y', strtotime($birth_record['dob'])) . '</td>
            </tr>
            <tr>
                <td class="label">Gender:</td>
                <td class="value">' . htmlspecialchars($birth_record['gender']) . '</td>
            </tr>
            <tr>
                <td class="label">Mother\'s Full Name:</td>
                <td class="value">' . htmlspecialchars($birth_record['mother_name']) . '</td>
            </tr>
            <tr>
                <td class="label">Father\'s Full Name:</td>
                <td class="value">' . htmlspecialchars($birth_record['father_name']) . '</td>
            </tr>
            <tr>
                <td class="label">Place of Birth:</td>
                <td class="value">' . htmlspecialchars($birth_record['village']) . ', ' . htmlspecialchars($birth_record['sector']) . '</td>
            </tr>
            <tr>
                <td class="label">Registration Date:</td>
                <td class="value">' . date('F d, Y', strtotime($birth_record['registration_date'])) . '</td>
            </tr>
            <tr>
                <td class="label">Registered By:</td>
                <td class="value">' . htmlspecialchars($birth_record['registered_by_name']) . '</td>
            </tr>
        </table>
        
        <div class="footer-note">
            <p>
                <strong>Important Notice:</strong><br>
                This certificate is issued based on the information provided during registration and maintained 
                in the official village registry. Any alteration, forgery, or unauthorized reproduction of this 
                document is strictly prohibited and punishable by law.
            </p>
        </div>
        
        <div class="signatures">
            <table>
                <tr>
                    <td>
                        <div class="signature-line">
                            <strong>Registrar\'s Signature</strong><br>
                            <small>' . htmlspecialchars($birth_record['registered_by_name']) . '</small><br>
                            <small>Village Registrar</small>
                        </div>
                    </td>
                    <td>
                        <div class="signature-line">
                            <strong>Official Seal</strong><br>
                            <small>Village Register Office</small><br>
                            <small>Ministry of Local Government</small>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="issue-info">
            <p>
                <strong>Certificate Issued On:</strong> ' . date('F d, Y') . '<br>
                <strong>Issuing Authority:</strong> Village Birth and Death Register System<br>
                <strong>Document ID:</strong> VRS-BC-' . $birth_record['id'] . '-' . date('Ymd') . '
            </p>
        </div>
    </div>
</body>
</html>';

// Configure dompdf options
$options = new Options();
$options->set('defaultFont', 'Times-Roman');
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', false);
$options->set('isRemoteEnabled', true); // Enable for loading images

// Create new Dompdf instance
$dompdf = new Dompdf($options);

// Load HTML content
$dompdf->loadHtml($certificate_content);

// Set paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render PDF
$dompdf->render();

// Generate filename
$filename = 'birth_certificate_' . str_replace(' ', '_', strtolower($birth_record['child_name'])) . '_' . date('Y-m-d') . '.pdf';

// Set headers and output PDF
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

// Stream PDF to browser
echo $dompdf->output();
?>
