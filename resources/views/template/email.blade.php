<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title></title>
    <style>
        table,
        td,
        div,
        h1,
        p {
            font-weight: 500;
            font-family: Arial, sans-serif;
        }
        .btn {margin: 10px 0px;
            border-radius: 4px;
            text-decoration: none;
            color: #fff !important;
            height: 46px;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: 600;
            background-image: linear-gradient(to right top, #021d68, #052579, #072d8b, #09369d, #093fb0) !important;
        }
        .btn:hover {
            text-decoration: none;
            opacity: .8;
        }
        #code_box{
            width:60%;
            height:50px;
            border: solid 1px;
            margin: 0 auto;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body style="background-color: #f2f6f8; margin:0;padding:0;">
    <table role="presentation"
        style="width:100%;border-collapse:collapse;border:0;border-spacing:0;background:#ffffff;">
        <tr>
            <td align="center" style="padding:0;">
                <table role="presentation"
                    style="width:600px;border-collapse:collapse;border:1px solid #cccccc;border-spacing:0;text-align:left;">
                    <tr style="border-collapse:collapse;border:1px solid #cccccc;border-spacing:0;">
                        <td style="padding:10px 25px;background:#fff; display: flex; align-items: center;">
                             <span style="font-weight: bold; padding-top: 10px;"> DIY Designer </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:36px 30px 42px 30px;">
                            <table role="presentation"
                                style="width:100%;border-collapse:collapse;border:0;border-spacing:0;">
                                <tr>
                                    <td style="padding:0 0 36px 0;color:#153643;">
                                        <p style="font-weight:bold;margin:0 0 20px 0;font-family:Arial,sans-serif;">
                                            Hello,</p>
                                        <p
                                            style="margin:0 0 12px 0;font-size:14px;line-height:24px;font-family:Arial,sans-serif;">
                                            {{$header}}
                                            </p>
                                        <p
                                            style="margin:10px 0 12px 0;font-size:14px;line-height:24px;font-family:Arial,sans-serif;">
                                            {{$content}}
                                            @if($code!==null)
                                                <div id="code_box">
                                                    <span>
                                                        This is your code {{$code}}
                                                    </span>
                                                </div>
                                                <div>
                                                    <p style="margin:10px 0 12px 0;font-size:14px;line-height:24px;font-family:Arial,sans-serif;">If you did not request a password reset, you can safely ignore this email. Only a person with access to your email can reset your account password.</p>
                                                    <p style="margin:10px 0 12px 0;font-size:14px;line-height:24px;font-family:Arial,sans-serif;">For any assistance, you can contact us by sending an email to help@nxt.work</p>
                                                </div>
                                            @endif
                                        </p>

                                        <p style="text-align: center;">
                                        
                                        </p>


                                        <p style="margin:100px 0 12px 0;font-size:14px;font-family:Arial,sans-serif;">
                                            Thank
                                            you, </p>
                                        <p style="margin:0 0 12px 0;font-size:14px;font-family:Arial,sans-serif;">
                                            DIY Designer Team</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>