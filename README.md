# ClearWay

Before you run need to do some settings and configuration:

1. All the code should locate at the XAMPP folder: `/htdocs/WEB2202/ClearWay/`
2. Create a new database in XAMPP called `clearway`, then insert the `clear_way.sql`
3. 
4. Set up the send email feature:

   * Go to `/app/send_email.php` and replace some value:
     * `<REPLACE_YOUR_GMAIL_SMTP_EMAIL_HERE>` * 2

       ```php
       $mail->Username   = '<REPLACE_YOUR_GMAIL_SMTP_EMAIL_HERE>';
       ```

       ```php
       $mail->setFrom('<REPLACE_YOUR_GMAIL_SMTP_EMAIL_HERE>', 'ClearWay');
       ```
     * `<REPLACE_YOUR_GMAIL_SMTP_PASSWORD_HERE>`

       ```php
       $mail->Password   = '<REPLACE_YOUR_GMAIL_SMTP_PASSWORD_HERE>';
       ```
     * `<REPLACE_YOUR_PHPMailer_PATH_HERE>` * 3

       ```php
       require '<REPLACE_YOUR_PHPMailer_PATH_HERE>/PHPMailer/src/Exception.php';
       require '<REPLACE_YOUR_PHPMailer_PATH_HERE>/PHPMailer/src/PHPMailer.php';
       require '<REPLACE_YOUR_PHPMailer_PATH_HERE>/PHPMailer/src/SMTP.php';
       ```

# Reference

* To get your Gmail SMTP password: [Google Account Help: Sign in with app passwords](https://support.google.com/accounts/answer/185833?hl=en)
* How to install PHPMailer: [PHPMailer]()
