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
                          {{$user->fullName}} 様
                        </td>
                      </tr>
                      <tr>
                        <td style="padding: 0 0 10px 0; font-size: 14px; line-height: 150%; font-weight: 400; color: #000000; letter-spacing: 0.01em;">
                          JOB MATCHY を利用いただきありがとうございます
                        </td>
                      </tr>
                      <tr>
                        <td style="padding: 0 0 16px 0; font-size: 14px; line-height: 150%; font-weight: 400; color: #000000; letter-spacing: 0.01em;">
                             本マッチング開始するため下記のリンクで支払いはや銀行口座に振り込みお願い致します。
                        </td>
                      </tr>
                      <tr>
                        <td style="padding: 0 0 24px 0;">
                          <a class="button" href="{{$payment_link}}" title="Reset Password" style="width: 100%; background: #4C83EE; text-decoration: none; display: inline-block; padding: 10px 0; color: #fff; font-size: 14px; line-height: 21px; text-align: center; font-weight: bold; border-radius: 7px;">オンライン支払う</a>
                        </td>
                      </tr>
                      <tr>
                        <td style="padding: 0 0 16px 0; font-size: 14px; line-height: 150%; font-weight: 400; color: #000000; letter-spacing: 0.01em;"><hr>
                            銀行名 ：楽天銀行<br>
                            支店名：第一営業支店 251<br>
                            科目：普通<br>
                            口座番号：7983503<br>
                            口座名義　：Ｏｕｒ　Ｗｏｒｋｓ　株式会社 
                            カナ：アウア ワ－クス（カ
                            {{-- Account Name: Nakama Educational Consultancy pvt. ltd <hr> --}}
                      </tr>
                      <tr>
                        <td style="padding: 0 0 8px;">
                          <a style="display: flex; justify-content: space-between; align-items: center; padding: 28px 24px; border-radius: 4px; background-color: #FFF9F9; text-decoration: none;" href="mailto::info@jobmatchy.net">
                            <span style="width: 90%; font-size: 14px; line-height: 150%; font-weight: bold; color: #29426E; letter-spacing: 0.01em;">銀行振込の場合下記の情報を含めてメールで問い合わせください。 info@jobmatchy.net</span>
                            <span style="width: 10%; float: right;">
                              <strong>→</strong>
                            </span>
                          </a>
                        </td>
                      </tr>
                      <tr>
                        <td style="padding: 0 0 60px;">
                      {{-- {{onboarding_video_link}}    --}}
                       <a style="display: flex; padding: 28px 24px; border-radius: 4px; background-color: #FFF9F9; text-decoration: none;" href="">
                      
                            <span style="width: 90%; font-size: 14px; line-height: 150%; font-weight: bold; color: #29426E; letter-spacing: 0.01em;">USER ID: {{ $user->id}}<br>
                                EMAIL: {{ $user->email }}<br>
                                CHAT ROOM ID:{{ $room->id }}
                                </span>
                            <span style="width: 10%; float: right;">
                              
                          </a>
                        </td>
                      </tr>
                       <tr>
                        <td style="padding: 0 0 10px 0; font-size: 14px; line-height: 150%; font-weight: 400; color: #000000; letter-spacing: 0.01em;">
                          PC版: <a href="https://jobmatchy.net" target=_"blank">https://jobmatchy.net</a>
                        </td>
                      </tr>
                      <tr>
                        <td style="padding: 0 0 10px 0; font-size: 14px; line-height: 150%; font-weight: 400; color: #000000; letter-spacing: 0.01em;">
                          このメールはJOBMATCHYから送信されております。ご質問やご不明な点がございましたら、お問い合わせフォームよりお問い合わせください <br>ご質問やご不明な点がございましたらお問い合わせフォームよりお問い合わせくださいませ。
                        </td>
                      </tr>
                      
                        <td style="padding: 0 0 60px;">
                          <a style="display: flex; padding: 28px 24px; border-radius: 4px; background-color: #FFF9F9; text-decoration: none;" href="https://docs.google.com/forms/d/1w6FCDe8Bl1OL1IkTTCeNgEL3AMz__vjBMnb2_lG8Gtk/viewform?edit_requested=true" target="_blank">
                            <span style="width: 90%; font-size: 14px; line-height: 150%; font-weight: bold; color: #29426E; letter-spacing: 0.01em;">お問い合わせフォーム：
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
                          よろしくお願い致します, <br><strong>Job Matchy</strong>
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



