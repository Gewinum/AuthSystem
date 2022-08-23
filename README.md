# AuthSystem - absolutely simple auth plugin
[![](https://poggit.pmmp.io/shield.state/AuthSystem)](https://poggit.pmmp.io/p/AuthSystem)
<a href="https://poggit.pmmp.io/p/AuthSystem"><img src="https://poggit.pmmp.io/shield.state/AuthSystem"></a>

AuthSystem is simplest PMMP4 authorise plugin. You can use it with ease. It supports xbox live auth, and also password minimal and maximal lengths are configurable.

It uses library libasynql, so we can say everything there is asynchronous.

It stores passwords as hashs, not in raw. password_hash() function with PASSWORD_DEFAULT algo is used.