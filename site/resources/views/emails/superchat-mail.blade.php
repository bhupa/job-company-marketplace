<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation for JOBMATCHY</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">

    <h2 style="color: #333;">Thank you for your payment to JOBMATCHY.</h2>

    <p>We have confirmed the payment for the following contract and would like to provide you with the details:</p>

    <ul>
        <li><strong>Name:</strong> {{ $user['fullName']}}</li>
        <li><strong>Super chat payment</strong></li>
       
    </ul>
   // Bank Details 
    <p><strong>Bank Name:</strong> Credit Card Payment or Bank Transfer</p>
    <p><strong>Account Number:</strong> Account Number </p>

    {{-- <p>If you are enrolled in automatic renewal, the next service fee will be automatically deducted in the month of contract expiration.</p>
    <p>If you wish to stop automatic renewal, please proceed through the control panel.</p> --}}

    <p><strong>Control Panel - PC Version:</strong> <a href="[Customized URL]">Customized URL</a></p>

    <p>This email is sent from JOBMATCHY (Customized URL). If you have any questions or concerns, please contact us via the inquiry form.</p>

    <p><strong>Inquiry Form:</strong> <a href="https://docs.google.com/forms/d/1w6FCDe8Bl1OL1IkTTCeNgEL3AMz__vjBMnb2_lG8Gtk/viewform?edit_requested=true">Inquiry Form</a></p>

    <p>Best regards,<br>[Your Company Name]</p>

</body>
</html>

