<?php
require_once 'includes/auth.php';
require_once 'pdf/SimplePDF.php';
require_login();

// Get death record ID
$death_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$death_id) {
    header('Location: view_deaths.php?error=Invalid death record ID');
    exit();
}

// Fetch death record
$query = "SELECT d.*, u.name as registered_by_name 
          FROM death_records d 
          JOIN users u ON d.registered_by = u.id 
          WHERE d.id = ?";
$result = execute_query($query, 'i', [$death_id]);

if (!$result || $result->num_rows == 0) {
    header('Location: view_deaths.php?error=Death record not found');
    exit();
}

$death_record = $result->fetch_assoc();

// Generate certificate HTML content
$certificate_content = '
<div class="header">
    <h1 class="title">REPUBLIC OF RWANDA</h1>
    <h2 class="subtitle">DEATH CERTIFICATE</h2>
    <div style="border: 2px solid #2c3e50; display: inline-block; padding: 10px; margin: 10px;">
        <strong>Certificate No: DC-' . str_pad($death_record['id'], 6, '0', STR_PAD_LEFT) . '</strong>
    </div>
</div>

<div class="content">
    <p style="font-size: 16px; text-align: center; margin: 30px 0;">
        This is to certify that according to the records maintained by the Village Register System:
    </p>
    
    <table style="width: 100%; border-collapse: collapse; margin: 30px 0;">
        <tr>
            <td style="padding: 15px; border: 1px solid #ddd; background: #f8f9fa; width: 30%; font-weight: bold;">
                Deceased Person\'s Name:
            </td>
            <td style="padding: 15px; border: 1px solid #ddd;">
                ' . htmlspecialchars($death_record['deceased_name']) . '
            </td>
        </tr>
        <tr>
            <td style="padding: 15px; border: 1px solid #ddd; background: #f8f9fa; font-weight: bold;">
                Date of Death:
            </td>
            <td style="padding: 15px; border: 1px solid #ddd;">
                ' . date('F d, Y', strtotime($death_record['dod'])) . '
            </td>
        </tr>
        <tr>
            <td style="padding: 15px; border: 1px solid #ddd; background: #f8f9fa; font-weight: bold;">
                Place of Death:
            </td>
            <td style="padding: 15px; border: 1px solid #ddd;">
                ' . htmlspecialchars($death_record['village']) . ', ' . htmlspecialchars($death_record['sector']) . '
            </td>
        </tr>
        <tr>
            <td style="padding: 15px; border: 1px solid #ddd; background: #f8f9fa; font-weight: bold;">
                Cause of Death:
            </td>
            <td style="padding: 15px; border: 1px solid #ddd;">
                ' . nl2br(htmlspecialchars($death_record['cause_of_death'])) . '
            </td>
        </tr>
        <tr>
            <td style="padding: 15px; border: 1px solid #ddd; background: #f8f9fa; font-weight: bold;">
                Family Contact:
            </td>
            <td style="padding: 15px; border: 1px solid #ddd;">
                ' . htmlspecialchars($death_record['family_contact']) . '
            </td>
        </tr>
        <tr>
            <td style="padding: 15px; border: 1px solid #ddd; background: #f8f9fa; font-weight: bold;">
                Registration Date:
            </td>
            <td style="padding: 15px; border: 1px solid #ddd;">
                ' . date('F d, Y', strtotime($death_record['registration_date'])) . '
            </td>
        </tr>
    </table>
    
    <div style="margin: 50px 0; text-align: center;">
        <p style="font-size: 14px; color: #666;">
            This certificate is issued based on the information provided during registration.<br>
            Any alteration or forgery of this document is punishable by law.
        </p>
    </div>
    
    <div style="margin-top: 60px;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; text-align: center;">
                    <div style="border-top: 2px solid #000; width: 200px; margin: 0 auto; padding-top: 10px;">
                        <strong>Registrar\'s Signature</strong><br>
                        <small>' . htmlspecialchars($death_record['registered_by_name']) . '</small>
                    </div>
                </td>
                <td style="width: 50%; text-align: center;">
                    <div style="border-top: 2px solid #000; width: 200px; margin: 0 auto; padding-top: 10px;">
                        <strong>Official Seal</strong><br>
                        <small>Village Register Office</small>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    
    <div style="margin-top: 40px; text-align: center; font-size: 12px; color: #666;">
        <p>
            <strong>Certificate issued on:</strong> ' . date('F d, Y') . '<br>
            <strong>Issuing Authority:</strong> Village Birth and Death Register System
        </p>
    </div>
</div>';

// Create PDF
$pdf = new SimplePDF('Death Certificate - ' . $death_record['deceased_name']);
$pdf->setContent($certificate_content);

// Check if download is requested
$download = isset($_GET['download']) && $_GET['download'] == '1';
$filename = 'Death_Certificate_' . str_replace(' ', '_', $death_record['deceased_name']) . '_' . date('Y-m-d') . '.pdf';

$pdf->output($filename, $download ? 'D' : 'I');
?>
