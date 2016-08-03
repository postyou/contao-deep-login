# contao-deep-login
This is a Contao Extension for (frontend) deep-linking login. When a User has a link to a protected sub-site or resource, he/she will be ask to login before the access is granted.

How to use:

1. Create a new Page of Type "login"
2. Create a new Page of Type "error_403", so that users who try accessing a page not allowed to their group get a "not allowed" Page after logging in.
3. (optional) Place a login-modul on the page, so that users how want to access a certain ressource with a (deep-)link can log-in.
Attention: If you have multiple Login-Form Modules on one page, use the DeepLogin-Form FE Module! If you don't the Contao login-form can break deeplogin functionality (with jumpTo set in module settings, or member-group jumpTo setting)

---------------------------------------

Die Verwendeten Icons Stammen aus dem kostenlosen [FAM FAM FAM](http://www.famfamfam.com/lab/icons/silk/) Iconset.
