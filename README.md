1. Integrated dynamic login and display user role based header.<br>The dynamic login method has been successfully integrated. You can access the login form at the URL below:
https://blog.wcr.is/test-1/<br><br>Additionally, you can embed this form on any other page by using the following shortcode:<br>[wcr_login_form]<br>
Here are the screenshots showing the header based on the user’s login status and roles:<br>- Logged Out: https://imgsh.net/i/09438fc14b<br>- Logged-in (Client, "blog" permission): https://imgsh.net/i/8a1d5e8cf4<br>- Logged-in (Client, "org_admin" permission): https://imgsh.net/i/c466e6ef3c<br><br>You can also assign different user login statuses or roles to specific menu items. See the below screenshots for examples:<br> 1. https://imgsh.net/i/cf6c98fe07<br>2. https://imgsh.net/i/e2fb34f972<br>3. https://imgsh.net/i/75febb03ff<br>
And, here are the credentials that I have used for the above tests<br>
```
email  : dev@sunflower-care.org
password: 123testPass!
user_role: org_admin

email  : jignesh319@yopmail.com
password: C!@Pq:$6hS'>cGa
user_role: blog
```
