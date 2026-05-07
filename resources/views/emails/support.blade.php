<!DOCTYPE html>
<html>
<head>
    <title>Support Request</title>
</head>
<body>
    <h2>New Support Request Received</h2>
    
    <p><strong>User Details:</strong></p>
    <ul>
        <li><strong>Name:</strong> {{ $supportData['name'] }}</li>
        <li><strong>Email:</strong> {{ $supportData['email'] }}</li>
        <li><strong>Phone:</strong> {{ $supportData['phone'] ?? 'N/A' }}</li>
        <li><strong>User ID:</strong> {{ $supportData['user_id'] }}</li>
    </ul>
    
    <p><strong>Request Details:</strong></p>
    <ul>
        <li><strong>Subject:</strong> {{ $supportData['subject'] }}</li>
        <li><strong>Job ID:</strong> {{ $supportData['job_id'] ?? 'N/A' }}</li>
        <li><strong>Description:</strong></li>
    </ul>
    
    <div style="background: #f5f5f5; padding: 15px; border-radius: 5px;">
        {{ $supportData['description'] }}
    </div>
    
    <br>
    <p><em>This email was generated automatically from the support system.</em></p>
</body>
</html>