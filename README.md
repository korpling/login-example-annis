# login-example-annis
An example application how to implement custom login with ANNIS (as described in http://korpling.github.io/ANNIS/3.6/user-guide/advanced-custom-login.html)

In order to make this work you have protect the "protected" directory e.g. by configuring Shibboleth authentification for this directory.
The user identify must be provided with the "REMOTE_USER" variable.
