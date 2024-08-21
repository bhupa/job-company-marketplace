<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation for JOBMATCHY</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">

    <h2 style="color: #333;"> {{trans("lang.subscription_email_template.subscription_start.title", [], getUserLanguage($user))}},</h2>

    <p>{{trans("lang.subscription_email_template.subscription_start.description", [], getUserLanguage($user))}}</p>

    <ul>
         <li><strong>{{trans("lang.subscription_email_template.subscription_start.name", [], getUserLanguage($user))}}:</strong> {{ $user['fullName']}}</li>
        <li><strong>{{trans("lang.subscription_email_template.subscription_start.contract_details", [], getUserLanguage($user))}}:</strong> {{trans("lang.subscription_email_template.subscription_start.contract_details_text", [], getUserLanguage($user))}}</li>
        <li><strong>{{trans("lang.subscription_email_template.subscription_start.contract_period", [], getUserLanguage($user))}}:</strong> {{ $plan['contractStartEnd']}} ({{ $plan['contractPeriod']}})</li>
         <br>
         <br>
          <li><strong>{{trans("lang.subscription_email_template.subscription_start.payment_method", [], getUserLanguage($user))}}:</strong> {{ $plan['paymentMethod']}}</li>
        <li><strong>{{trans("lang.subscription_email_template.subscription_start.payment_amount", [], getUserLanguage($user))}}:</strong> {{ $plan['paymentAmount']}} ( {{trans("lang.include", [], getUserLanguage($user))}})</li>
        <li><strong>{{trans("lang.subscription_email_template.subscription_start.payment_confirmation_date", [], getUserLanguage($user))}}:</strong> {{ $plan['paymentConfirmation']}}</li>
        <li><strong>{{trans("lang.subscription_email_template.subscription_start.issuer", [], getUserLanguage($user))}}:</strong> {{trans("lang.subscription_email_template.subscription_start.issuer_text", [], getUserLanguage($user))}}</li>
       
    
    </ul>

    <p>{{trans("lang.subscription_email_template.subscription_start.auto_renew", [], getUserLanguage($user))}}</p>
    <hr>
    <p>{{trans("lang.subscription_email_template.subscription_start.auto_renew_stop", [], getUserLanguage($user))}}</p>
    
    <p>{{trans("lang.subscription_email_template.subscription_start.pcVersion", [], getUserLanguage($user))}}</p>
   <a href="{{env('APP_URL')}}">{{env('APP_URL')}}</a>
     <hr>
     <p>
     {{trans("lang.subscription_email_template.subscription_start.email_sent", [], getUserLanguage($user))}}
     <a href=""> {{trans("lang.subscription_email_template.subscription_start.customized_URL", [], getUserLanguage($user))}}</a>
     </p>
    
   
    <p><strong>{{trans("lang.subscription_email_template.subscription_start.inquiry_form", [], getUserLanguage($user))}}:</strong> 
    <a href="https://docs.google.com/forms/d/1w6FCDe8Bl1OL1IkTTCeNgEL3AMz__vjBMnb2_lG8Gtk/viewform?edit_requested=true
   ">{{trans("lang.subscription_email_template.subscription_start.inquiry_form", [], getUserLanguage($user))}}</a></p>

    <p> {{trans("lang.best_regards", [], getUserLanguage($user))}},<br>{{env('APP_Name')}}</p>

</body>
</html>

