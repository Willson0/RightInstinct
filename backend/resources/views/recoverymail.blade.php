<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Восстановление доступа</title>
</head>
<body style="margin: 0; padding: 0; background-color: #2D3014;">
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #2D3014;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #fff; margin: 40px auto; border-radius: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.05);">
                <tr>
                    <td style="padding: 32px 40px 24px 40px; text-align: center;">
                        <h2 style="color: #333; font-family: 'Gilroy', Arial, sans-serif; font-size: 64px; line-height: 64px; font-weight: 700; margin: 0 0 24px 0;">Восстановление доступа</h2>
                        <p style="color: #444; font-family: 'Gilroy', Arial, sans-serif; font-size: 16px; margin: 0 0 24px 0;">
                            Вы отправили запрос на восстановление доступа.<br>
                            Для установки нового пароля, пожалуйста, перейдите по кнопке ниже:
                        </p>
                        <a href="https://{{$domain}}/recovery?code={{$code}}"
                           style="display: inline-block; padding: 14px 0; width: 100%; background-color: #787d46; color: #F1EBD8; font-size: 18px; font-family: 'Gilroy', Arial, sans-serif; text-decoration: none; border-radius: 10px; margin-bottom: 20px;">
                            Восстановить доступ
                        </a>
                        <p style="color: #888; font-family: 'Gilroy', Arial, sans-serif; font-size: 14px; margin: 24px 0 0 0;">
                            Если вы не запрашивали восстановление, просто проигнорируйте это письмо.
                        </p>
                        <p style="color: #bbb; font-family: 'Gilroy', Arial, sans-serif; font-size: 13px; margin: 16px 0 0 0;">
                            — Команда «Верное чутьё»
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>


