# Free hosting par live karne ke steps (InfinityFree jaisi hosting)

1. **vendor folder banayein (apne computer par):** project folder mein terminal kholen aur chalayen:
   `composer install --no-dev`
   (Composer nahi hai to getcomposer.org se install karein.) Is se `vendor/` folder ban jayega.
2. **Database:** hosting panel mein MySQL database banayein, phpMyAdmin kholen, `database/job_portal.sql` import karein.
3. **.env:** `.env.example` ko copy karke `.env` banayein aur apni DB/mail values likhein (APP_BASE_PATH likhne ki zaroorat nahi, khud detect hota hai).
4. **Upload:** File Manager / FTP se sab files `htdocs` folder mein upload karein, saath mein `vendor/` aur `.env` bhi.
5. **Test:** site kholen, register karein, employer se job post karein, applicant se apply karein, accept -> interview schedule -> hire test karein.

Note: free hosting par email (SMTP) aksar block hoti hai. Isse app crash nahi hoti, lekin email nahi jati
(password reset email bhi). Notifications site ke andar phir bhi kaam karengi.
