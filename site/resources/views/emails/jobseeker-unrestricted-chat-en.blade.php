<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <title></title>
  <!--[if !mso]><!-- -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <!--<![endif]-->
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style type="text/css">
    #outlook a {
      padding: 0;
    }

    .ReadMsgBody {
      width: 100%;
    }

    .ExternalClass {
      width: 100%;
    }

    .ExternalClass * {
      line-height: 100%;
    }

    body {
      margin: 0;
      padding: 0;
      -webkit-text-size-adjust: 100%;
      -ms-text-size-adjust: 100%;
    }

    table,
    td {
      border-collapse: collapse;
      mso-table-lspace: 0pt;
      mso-table-rspace: 0pt;
    }

  </style>
  <!--[if !mso]><!-->
  <style type="text/css">
    @media only screen and (max-width:480px) {
      @-ms-viewport {
        width: 320px;
      }
      @viewport {
        width: 320px;
      }
    }
  </style>
  <!--<![endif]-->
  <!--[if mso]><xml>  <o:OfficeDocumentSettings>    <o:AllowPNG/>    <o:PixelsPerInch>96</o:PixelsPerInch>  </o:OfficeDocumentSettings></xml><![endif]-->
  <!--[if lte mso 11]><style type="text/css">  .outlook-group-fix {    width:100% !important;  }</style><![endif]-->
  <!--[if !mso]><!-->
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet" type="text/css">
  <style type="text/css">
    @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap');
  </style>
  <!--<![endif]-->
  <style type="text/css">
    @media only screen and (max-width:595px) {
      .container {
        width: 100% !important;
      }
      .button {
        display: block !important;
        width: auto !important;
      }
    }
  </style>
</head>

<body style="font-family: 'Inter', sans-serif; background: #E5E5E5;">
  <table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" bgcolor="#F6FAFB">
    <tbody>
      <tr>
        <td valign="top" align="center">
          <table class="container" width="600" cellspacing="0" cellpadding="0" border="0">
            <tbody>
              <tr>
                <td style="padding:48px 0 30px 0; text-align: center; font-size: 14px; color: #4C83EE;">
                  JOB MATCHY
                </td>
              </tr>
              <tr>
                <td class="main-content" style="padding: 48px 30px 40px; color: #000000;" bgcolor="#ffffff">
                  <table width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                      <tr>
                        <td style="padding: 0 0 24px 0; font-size: 18px; line-height: 150%; font-weight: bold; color: #000000; letter-spacing: 0.01em;">
                          Dear, {{$user->fullName}}!
                        </td>
                      </tr>
                      <tr>
                        <td style="padding: 0 0 10px 0; font-size: 14px; line-height: 150%; font-weight: 400; color: #000000; letter-spacing: 0.01em;">
                          Thank you for using JOB MATCHY. 
                        </td>
                      </tr>
                      <tr>
                        <td style="padding: 0 0 16px 0; font-size: 14px; line-height: 150%; font-weight: 400; color: #000000; letter-spacing: 0.01em;">
                          To initiate unrestricted chat please make payment using following link or make  transfer bank account. 
                        </td>
                      </tr>
                      <tr>
                        <td style="padding: 0 0 24px 0;">
                          <a class="button" href="{{$payment_link}}" title="Reset Password" style="width: 100%; background: #4C83EE; text-decoration: none; display: inline-block; padding: 10px 0; color: #fff; font-size: 14px; line-height: 21px; text-align: center; font-weight: bold; border-radius: 7px;">Pay Online</a>
                        </td>
                      </tr>
                      <tr>
                        <td style="padding: 0 0 16px 0; font-size: 14px; line-height: 150%; font-weight: 400; color: #000000; letter-spacing: 0.01em;"><hr>
                          Bank name: Nepal Investment Mega Bank<br>
Account Type: Current Account<br>
Account No: 5001020250009<br>
Account Name: Nakama Educational Consultancy pvt. ltd <hr>
                      </tr>
                      <tr>
                        <td style="padding: 0 0 8px;">
                          <a style="display: flex; justify-content: space-between; align-items: center; padding: 28px 24px; border-radius: 4px; background-color: #FFF9F9; text-decoration: none;" href="mailto::info@jobmatchy.net">
                            <span style="width: 90%; font-size: 14px; line-height: 150%; font-weight: bold; color: #29426E; letter-spacing: 0.01em;">Incase of bank transfer, please contant us with the information below or mail to info@jobmatchy.net</span>
                            <span style="width: 10%; float: right;">
                              <strong>→</strong>
                            </span>
                          </a>
                        </td>
                      </tr>
                      <tr>
                        <td style="padding: 0 0 60px;">
                          <a style="display: flex; padding: 28px 24px; border-radius: 4px; background-color: #FFF9F9; text-decoration: none;" href="">
                            <span style="width: 90%; font-size: 14px; line-height: 150%; font-weight: bold; color: #29426E; letter-spacing: 0.01em;">USER ID:{{$user->id}}<br>
EMAIL:{{$user->email}}<br>
CHAT ROOM ID:{{$room->id}}</span>
                            <span style="width: 10%; float: right;">
                              
                          </a>
                        </td>
                      </tr>
                       <tr>
                        <td style="padding: 0 0 10px 0; font-size: 14px; line-height: 150%; font-weight: 400; color: #000000; letter-spacing: 0.01em;">
                          PC version: <a href="https://jobmatchy.net" target=_"blank">https://jobmatchy.net</a>
                        </td>
                      </tr>
                      <tr>
                        <td style="padding: 0 0 10px 0; font-size: 14px; line-height: 150%; font-weight: 400; color: #000000; letter-spacing: 0.01em;">
                          This email is sent from JOBMATCHY. <br>If you have any questions or concerns, please contact us via the inquiry form.
                        </td>
                      </tr>
                      
                        <td style="padding: 0 0 60px;">
                          <a style="display: flex; padding: 28px 24px; border-radius: 4px; background-color: #FFF9F9; text-decoration: none;" href="https://docs.google.com/forms/d/1w6FCDe8Bl1OL1IkTTCeNgEL3AMz__vjBMnb2_lG8Gtk/viewform?edit_requested=true" target="_blank">
                            <span style="width: 90%; font-size: 14px; line-height: 150%; font-weight: bold; color: #29426E; letter-spacing: 0.01em;">Inquiry Form: Click here
</span>
                          </a>
                        </td>
                      </tr>
                      <tr>
                  
                      <tr>
                        <td style="padding: 0 0 16px;">
                          
                        </td>
                      </tr>
                      <tr>
                        <td style="font-size: 14px; line-height: 170%; font-weight: 400; color: #000000; letter-spacing: 0.01em;">
                          Best regards, <br><strong>Job Matchy</strong>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </td>
              </tr>
              <tr>
                <td style="padding: 24px 0 48px; font-size: 0px;">
                  <!--[if mso | IE]>      <table role="presentation" border="0" cellpadding="0" cellspacing="0">        <tr>          <td style="vertical-align:top;width:300px;">      <![endif]-->
                  <div class="outlook-group-fix" style="padding: 0 0 20px 0; vertical-align: top; display: inline-block; text-align: center; width:100%;">
                    <span style="padding: 0; font-size: 11px; line-height: 15px; font-weight: normal; color: #8B949F;">OUR WORKS CO.,LTD.<br/>
                    </div>
                  </div>
                  <!--[if mso | IE]>      </td></tr></table>      <![endif]-->
                </td>
              </tr>
            </tbody>
          </table>
        </td>
      </tr>
    </tbody>
  </table>
</body>
</html>