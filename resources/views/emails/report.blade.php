<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h2 style="color: #2ecc71; margin-top: 0;">Sales Report</h2>
        <p>Hello,</p>
        <p>Please find attached the sales report in <strong>Excel</strong> format.</p>
        <p><strong>Sent by:</strong> {{ $userName }} ({{ $userRole }})</p>
        <p><strong>Date:</strong> {{ now()->format('d M Y, h:i A') }}</p>
    </div>
    
    <div style="background-color: #e8f5e9; padding: 15px; border-radius: 5px; border-left: 4px solid #2ecc71;">
        <p style="margin: 0;"><strong>Note:</strong> This is an automated email. Please do not reply to this email.</p>
    </div>
    
    <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px;">
        <p style="margin: 0;">© {{ date('Y') }} Stock Management System. All rights reserved.</p>
    </div>
</body>
</html>

