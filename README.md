# login-example-annis
An example application how to implement custom login with ANNIS (as described in http://korpling.github.io/ANNIS/3.6/user-guide/advanced-custom-login.html). Please also see the documentation of the adminstration REST-API for how to create (temporary) users: http://korpling.github.io/ANNIS/3.6/developer-guide/rest-api/admin.html#user-managment

In order to make this work you have protect the "protected" directory e.g. by configuring Shibboleth authentification for this directory.
The user identify must be provided with the "REMOTE_USER" variable.
