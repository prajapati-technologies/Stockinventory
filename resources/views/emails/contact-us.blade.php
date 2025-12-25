<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us Message</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 5px 5px 0 0;
            text-align: center;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
            border-top: none;
        }
        .info-box {
            background: white;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #667eea;
            border-radius: 4px;
        }
        .label {
            font-weight: bold;
            color: #555;
            display: inline-block;
            min-width: 120px;
        }
        .issue-box {
            background: white;
            padding: 20px;
            margin: 15px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #777;
            font-size: 12px;
            border-top: 1px solid #ddd;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>New Contact Us Message</h2>
        <p style="margin: 0;">From: {{ $userRole }}</p>
    </div>
    
    <div class="content">
        <div class="info-box">
            <div style="margin-bottom: 10px;">
                <span class="label">Name:</span>
                <span>{{ $name }}</span>
            </div>
            <div style="margin-bottom: 10px;">
                <span class="label">Phone Number:</span>
                <span>{{ $phoneNumber }}</span>
            </div>
            <div>
                <span class="label">User Role:</span>
                <span>{{ $userRole }}</span>
            </div>
        </div>
        
        <div class="issue-box">
            <div class="label" style="display: block; margin-bottom: 10px;">Issue/Message:</div>
            <div style="white-space: pre-wrap; line-height: 1.8;">{{ $issue }}</div>
        </div>
    </div>
    
    <div class="footer">
        <p>This is an automated message from Stock Management System.</p>
        <p>Please respond to the user at their provided phone number: <strong>{{ $phoneNumber }}</strong></p>
    </div>
</body>
</html>
