<?php
// Simple PDF generation class using basic HTML to PDF conversion
// This is a simplified version for demonstration. In production, use dompdf or similar libraries.

class SimplePDF {
    private $html_content;
    private $title;
    
    public function __construct($title = 'Document') {
        $this->title = $title;
        $this->html_content = '';
    }
    
    public function setContent($content) {
        $this->html_content = $content;
    }
    
    public function output($filename = null, $destination = 'I') {
        // For demo purposes, we'll output HTML that can be printed as PDF
        // In production, integrate with dompdf, TCPDF, or similar library
        
        if ($destination == 'D') {
            // Force download
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . ($filename ?: 'document.pdf') . '"');
        } else {
            // Display inline
            header('Content-Type: text/html; charset=utf-8');
        }
        
        echo $this->generatePrintableHTML();
    }
    
    private function generatePrintableHTML() {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>' . htmlspecialchars($this->title) . '</title>
            <style>
                @media print {
                    .no-print { display: none !important; }
                    body { margin: 0; }
                }
                body {
                    font-family: Arial, sans-serif;
                    margin: 20px;
                    line-height: 1.4;
                }
                .certificate-container {
                    max-width: 800px;
                    margin: 0 auto;
                    border: 3px solid #2c3e50;
                    padding: 40px;
                    background: white;
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                }
                .title {
                    font-size: 28px;
                    font-weight: bold;
                    color: #2c3e50;
                    margin-bottom: 10px;
                }
                .subtitle {
                    font-size: 18px;
                    color: #666;
                    margin-bottom: 20px;
                }
                .content {
                    margin: 20px 0;
                }
                .footer {
                    margin-top: 40px;
                    text-align: center;
                    color: #666;
                    font-size: 12px;
                }
                .print-button {
                    text-align: center;
                    margin: 20px 0;
                }
                @page {
                    margin: 1in;
                }
            </style>
            <script>
                function printCertificate() {
                    window.print();
                }
                function downloadPDF() {
                    alert("To save as PDF: Use your browser\'s Print function and select \'Save as PDF\' as destination.");
                    window.print();
                }
            </script>
        </head>
        <body>
            <div class="no-print print-button">
                <button onclick="printCertificate()" style="background: #2c3e50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-right: 10px;">
                    🖨️ Print Certificate
                </button>
                <button onclick="downloadPDF()" style="background: #e74c3c; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
                    📄 Save as PDF
                </button>
                <a href="javascript:history.back()" style="background: #95a5a6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-left: 10px;">
                    ← Back
                </a>
            </div>
            
            <div class="certificate-container">
                ' . $this->html_content . '
            </div>
            
            <div class="no-print footer">
                <p>Generated from Village Birth and Death Register System on ' . date('F d, Y g:i A') . '</p>
            </div>
        </body>
        </html>';
    }
}
?>
