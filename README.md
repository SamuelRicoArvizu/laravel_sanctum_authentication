<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

## Pasos de Instalación

1.- Primero clona el proyecto y cambia el directorio

```shell
git clone https://github.com/SamuelRicoArvizu/laravel_sanctum_authentication.git
cd laravel_sanctum_authentication
```

2.- Instalar las dependencias

```shell
composer install
```

3.- Copie `.env.example` a `.env`

```shell
cp .env.example .env
```

4.- Generar clave de aplicación

```shell
php artisan key:generate
```

5.- Inicie el servidor web

```shell
php artisan serve
```

## Migración y siembra de base de datos

1.- Puede ejecutar las migraciones y sembradores (seeders) juntos simplemente ejecutando el siguiente comando

```shell
php artisan migrate:fresh --seed
```

**O tambien**

Puede ejecutarlos por separado usando los siguientes comandos

2.- Ejecutar Migraciones

```shell
php artisan migrate:fresh
```

Ahora su base de datos tiene tablas esenciales para la administracion de usuarios y roles.

3.- Ejecutar sembradores (seeders) de base de datos

Ejecute `db:seed` y tendrá su primer usuario administrador, algunos roles esenciales en la tabla de roles y la relación correctamente configurada.

```shell
php artisan db:seed
```

Tenga en cuenta que el usuario administrador predeterminado es **admin@project.com** y la contraseña predeterminada es **password**. Debe crear un nuevo usuario administrador antes de implementar en producción y eliminar este usuario administrador predeterminado.

## Roles predeterminados

laravel_sanctum_authentication viene con estos roles de "superadministrador", "administrador", "gerente", "supervisor", y "usuario" listos para usar. Para obtener más información, abra la tabla de roles después de la inicialización de la base de datos, o abra laravel tinker y experimente con el modelo `Role`

```shell
php artisan tinker
```

Ejecute el siguiente comando

```php
>>> Role::select(['id','slug','name'])->get()
//O tambien
>>> Role::all(['id','name','slug'])
//o tambien
>>> Role::all()
```

## Documentación de Rutas

Antes de experimentar con los siguientes endpoints de la API, ejecute el servidor su proyecto laravel_sanctum_authentication usando el comando `php artesanal serve`. Para la siguiente parte de esta documentación, asumimos que laravel_sanctum_authentication está posicionado en http://localhost:8000

### Registro de Usuario

Puede realizar una peticion `HTTP POST` para crear/registrar un nuevo usuario en el siguiente endpoint. Los usuarios recién creados tendrán el rol de `usuario` por defecto.

```shell
http://localhost:8000/api/users
```

**Consumo y respuesta de la API**

Puede enviar un formulario multiparte o una carga JSON como este.

```json
{
    "name":"Example User",
    "email":"user@project.com",
    "password":"Alguna Contraseña Segura"
}
```

¡Genial! ¡Su usuario ha sido creado y ahora está listo para iniciar sesión!

Si este usuario ya existe, recibirá una respuesta 409 como esta

```json
{
    "error": 1,
    "message": "El usuario ya existe."
}
```

### Autenticación de Usuario/Inicio de sesión (Administrador)

¿Recuerdas que laravel_sanctum_authentication viene con el usuario administrador predeterminado? Puede iniciar sesión como administrador realizando una peticion `HTTP POST` a la siguiente ruta.

```shell
http://localhost:8000/api/login
```

**Consumo y respuesta de la API**

Puede enviar un formulario multiparte o una carga JSON como este.

```json
{
    "email":"admin@project.com",
    "password":"password"
}
```

Obtendrá una respuesta JSON con un token de usuario. Necesita este token de administrador para realizar cualquier peticion a otras rutas protegidas por la Ability de administrador.

```json
{
    "error": 0,
    "token": "1|se9wkPKTxevv9jpVgXN8wS5tYKx53wuRLqvRuqCR"
}
```

Para cualquier intento fallido, recibirá una respuesta de error 401.

```json
{
    "error": 1,
    "message": "Credenciales no válidas."
}
```

### Autenticación de Usuario/Inicio de sesión (otros roles)

Puede iniciar sesión como usuario haciendo una peticion `HTTP POST` a la siguiente ruta

```shell
http://localhost:8000/api/login
```

**Consumo y respuesta de la API**

Puede enviar un formulario multiparte o una carga JSON como este.

```json
{
    "email":"user@project.com",
    "password":"Alguna Contraseña Segura"
}
```

Obtendrá una respuesta JSON con un token de usuario. Necesita este token de usuario para realizar cualquier peticion a otras rutas protegidas por la Ability de usuario.

```json
{
    "error": 0,
    "token": "2|4eqxDZoRzwGzt15KzeIVSvpJKDUEnPlDAcYPzME"
}
```

Para cualquier intento fallido, recibirá una respuesta de error 401.

```json
{
    "error": 1,
    "message": "Credenciales no válidas."
}
```

### Lista de Usuarios (se requiere Ability de administrador)

Para listar los usuarios, realice una peticion `HTTP GET` a la siguiente ruta, con el token de administrador obtenido del inicio de sesión de administrador. Agregue este token como un "Bearer[API Token]" a su peticion de la API.

```shell
http://localhost:8000/api/users
```

**Consumo y respuesta de la API**

No se requiere una carga JSON o un formulario para esta peticion.

Obtendrá una respuesta JSON con todos los usuarios disponibles en su proyecto.

```json
[
    {
        "id": 1,
        "name": "Admin API Project",
        "email":"admin@project.com",
    },
    {
        "id": 2,
        "name": "Example User",
        "email":"user@project.com",
    }
]
```

Para cualquier intento fallido o token incorrecto, recibirá una respuesta de error 401.

```json
{
    "error": 1,
    "message": "No autorizado."
}
```

### Actualizar un Usuario (se requiere Ability de usuario/administrador)

Realice una solicitud `HTTP PUT` a la siguiente ruta para actualizar un usuario existente. Reemplace {userId} con el ID del usuario real. Debe incluir un token obtenido de la autenticación de usuario/administrador. Un token de administrador puede actualizar a cualquier usuario. Un token de usuario solo puede actualizar el usuario autenticado por este token.

```shell
http://localhost:8000/api/users/{userId}
```

Por ejemplo, para actualizar el usuario con ID 3, use este endpoint `http://localhost:8000/api/users/3`

**Consumo y respuesta de la API**

Puede incluir `name` o `email`, o ambos en su envio como formulario multiparte o datos JSON.

```json
{
    "name":"example3",
    "email":"user@example.project"
}
```

Recibirá el usuario actualizado si el token es válido.

```json
{
    "id": 3,
    "name":"example3",
    "email":"user@example.project"
}
```

Para cualquier intento fallido con un token no válido, recibirá una respuesta de error 401.

```json
{
    "error": 1,
    "message": "Credenciales no válidas."
}
```

Si un token de usuario intenta actualizar a cualquier otro usuario que no sea él mismo, se entregará una respuesta de error 409

```json
{
    "error": 1,
    "message": "No autorizado."
}
```

Para cualquier intento fallido con una 'ID de usuario' no válido, recibirá una respuesta de error 404 no encontrado. Por ejemplo, cuando intente eliminar un usuario inexistente con el ID 12, recibirá la siguiente respuesta.

```json
{
    "error": 1,
    "message": "No query results for model [App\\Models\\User] 12"
}
```

### Eliminar un Usuario (se requiere Ability de administrador)

Para eliminar un usuario existente, realice una solicitud `HTTP DELETE` a la siguiente ruta. Reemplace {userId} con el ID del usuario real.

```shell
http://localhost:8000/api/users/{userId}
```

Por ejemplo, para eliminar el usuario con el ID 2, use este endpoint .`http://localhost:8000/api/users/2`

**Consumo y respuesta de la API**

No se requiere carga JSON o formulario para esta peticion.

Si la solicitud es exitosa y el token del portador es válido, recibirá una respuesta JSON como esta.

```json
{
   "error": 0,
   "message": "Usuario eliminado"
}
```

Recibirá una respuesta de error 401 por cualquier intento fallido con un token no válido

```json
{
    "error": 1,
    "message": "Credenciales no válidas."
}
```

Para cualquier intento fallido con un 'ID de usuario' no válida, recibirá una respuesta de error 404 no encontrada. Por ejemplo, recibirá la siguiente respuesta cuando intente eliminar un usuario inexistente con el ID 12.

```json
{
    "error": 1,
    "message": "No query results for model [App\\Models\\User] 12"
}
```

### Cerrar sesión/Logout (cualquier rol)

Puede cerrar sesión con cualquier Ability haciendo una peticion `HTTP POST` a la siguiente ruta. Debe incluir un token obtenido del inicio de sesión.

```shell
http://localhost:8000/api/logout
```

Esto eliminara el token del usuario guardado en la base de datos. Recibirá una respuesta de éxito 200 sin ningún contenido.

### Lista de Roles (se requiere Ability de administrador)

Para listar los roles, realice una peticion `HTTP GET` a la siguiente ruta, con el token de administrador obtenido del inicio de sesión de administrador. Agregue este token como un "Bearer[API Token]" a su peticion de la API.

```shell
http://localhost:8000/api/roles
```

**Consumo y respuesta de la API**

No se requiere una carga JSON o un formulario para esta peticion.

Obtendrá una respuesta JSON con todos los roles disponibles en su proyecto.

```json
[
     {
        "id": 1,
        "name": "Super Administrador",
        "slug": "super-administrador"
    },
    {
        "id": 2,
        "name": "Administrador",
        "slug": "administrador"
    },
    {
        "id": 3,
        "name": "Gerente",
        "slug": "gerente"
    },
    {
        "id": 4,
        "name": "Supervisor",
        "slug": "supervisor"
    },
    {
        "id": 5,
        "name": "Usuario",
        "slug": "usuario"
    }
]
```

Para cualquier intento fallido o token incorrecto, recibirá una respuesta de error 401.

```json
{
    "error": 1,
    "message": "No autorizado."
}
```

### Agregar un Nuevo Rol (se requiere Ability de administrador)

Puede realizar una peticion `HTTP POST` para crear/registrar un nuevo rol en el siguiente endpoint.

```shell
http://localhost:8000/api/roles
```

Debe proporcionar el nombre del rol como `name`, y `slug` como nombre del rol, pero en minúsculas y con guión (en caso de tener mas de una palabra) en su envio como formulario multiparte o datos JSON.

```json
{
    "name":"Gerente Operativo",
    "slug":"gerente-operativo"
}
```

Obtendrá una respuesta JSON con este rol recién creado para una ejecución exitosa.

```json
{
    "id": 7,
    "name":"Gerente Operativo",
    "slug":"gerente-operativo"
}
```

Si este rol ya existe en el campo `slug`, recibirá un mensaje de error 409 como este.

```json
{
    "error": 1,
    "message": "El rol ya existe."
}
```

Para cualquier intento fallido o token incorrecto, recibirá una respuesta de error 401.

```json
{
    "message": "Unauthenticated."
}
```

### Actualizar un Rol (se requiere Ability de administrador)

Para actualizar un rol, realice una peticion `HTTP PUT` o `HTTP PATCH` a la siguiente ruta, con el token de administrador obtenido del inicio de sesión de administrador. Agregue este token como un "Bearer[API Token]" a su peticion de la API.

```shell
http://localhost:8000/api/roles/{roleId}
```

Por ejemplo, para actualizar el rol con el ID 3, use este endpoint

```shell
http://localhost:8000/api/roles/3
```

**Consumo y respuesta de la API**

Debe proporcionar el nombre del rol como `name` y/o el campo `slug` como nombre del rol, pero en minúsculas y con guión (en caso de tener mas de una palabra) en su envio como formulario multiparte o datos JSON.

```json
{
    "name":"Gerente General",
    "slug":"gerente-general"
}
```

Obtendrá una respuesta JSON con este rol actualizado para una ejecución exitosa.

```json
{
    "id": 3,
    "name":"Gerente General",
    "slug":"gerente-general"
}
```

Tenga en cuenta que no puede cambiar un slug de rol de "super-administrador" o "administrador" porque muchas rutas API de este proyecto requieren exclusivamente de estos dos roles para funcionar correctamente.

Para cualquier intento fallido o token incorrecto, recibirá una respuesta de error 401.

```json
{
    "message": "Unauthenticated."
}
```

### Eliminar un Rol (se requiere Ability de administrador)

Para eliminar un rol, realice una peticion `HTTP DELETE` a la siguiente ruta, con el token de administrador obtenido del inicio de sesión de administrador. Agregue este token como un "Bearer[API Token]" a su peticion de la API.

```shell
http://localhost:8000/api/roles/{roleId}
```

Por ejemplo, para eliminar el rol con el ID 3, use este endpoint `http://localhost:8000/api/roles/3`

**Consumo y respuesta de la API**

No se requiere carga JSON o formulario para esta peticion.

Obtendrá una respuesta JSON con este rol eliminado para una ejecución exitosa.

```json
{
    "error": 0,
    "message": "El rol ha sido eliminado."
}
```

Tenga en cuenta que no puede eliminar el rol `admin` porque muchas rutas API de este proyecto    requieren exclusivamente este rol para funcionar correctamente.

Si intenta eliminar el rol de administrador, recibirá la siguiente respuesta de error 422.

```json
{
    "error": 1,
    "message": "No puede eliminar este rol."
}
```

Para cualquier intento fallido o token incorrecto, recibirá una respuesta de error 401.

```json
{
    "message": "Unauthenticated."
}
```

### Lista de los Roles disponibles de un Usuario (se requiere Ability de administrador)

```shell
http://localhost:8000/api/users/{userId}/roles
```

Por ejemplo, para obtener todos los roles asignados al usuario con id 2, use este endpoint

```shell
http://localhost:8000/api/users/2/roles
```

**Consumo y respuesta de la API**

No se requiere una carga JSON o un formulario para esta peticion.

Para una ejecución exitosa, obtendrá una respuesta JSON que contiene al usuario con todos sus roles asignados.

```json
{
    "id": 2,
    "name": "Example User 2",
    "email": "example@user_two.project",
    "roles": [
        {
            "id": 2,
            "name": "Usuario",
            "slug": "usuario"
        },
        {
            "id": 3,
            "name": "Supervisor",
            "slug": "supervisor"
        }
    ]
}
```

Para cualquier intento fallido o token incorrecto, recibirá una respuesta de error 401.

```json
{
    "message": "Unauthenticated."
}
```

### Asignar un Rol a un Usuario (se requiere Ability de administrador)

Para asignar un rol a un usuario, realice una peticion `HTTP POST` a la siguiente ruta, con el token de administrador obtenido del inicio de sesión de administrador. Agregue este token como un "Bearer[API Token]" a su peticion de la API. Reemplace {userId} con un ID de usuario real

```shell
http://localhost:8000/api/users/{userId}/roles
```

Por ejemplo, para asignar un rol al usuario con ID 2, use este endpoint

```shell
http://localhost:8000/api/users/2/roles
```

**Consumo y respuesta de la API**

Debe proporcionar el campo `role_id` en su envio de formulario multiparte o datos JSON

```json
{
    "role_id":3 
}
```

Para una ejecución exitosa, obtendrá una respuesta JSON que contiene al usuario con todos sus roles asignados.

```json
{
    "id": 2,
    "name": "Example User 2",
    "email": "example@user_two.project",
    "roles": [
        {
            "id": 2,
            "name": "Usuario",
            "slug": "usuario"
        },
        {
            "id": 3,
            "name": "Supervisor",
            "slug": "supervisor"
        }
    ]
}
```

Observe que el usuario tiene un arreglo `Roles`, y el nuevo rol asignado está presente en el arreglo.

Tenga en cuenta que no tendrá ningún efecto si vuelve a asignar el mismo "rol" a un usuario.

Para cualquier intento fallido o token incorrecto, recibirá una respuesta de error 401.

```json
{
    "message": "Unauthenticated."
}
```

### Eliminar un Rol de un Usuario (se requiere Ability de administrador)

Para eliminar un rol de un usuario, realice una peticion `HTTP DELETE` a la siguiente ruta, con el token de administrador obtenido del inicio de sesión de administrador.

```shell
http://localhost:8000/api/users/{userId}/roles/{role}
```

Por ejemplo, para eliminar un rol con ID 3 del usuario con ID 2, use este endpoint

```shell
http://localhost:8000/api/users/2/roles/3
```

**Consumo y respuesta de la API**

No se requiere una carga JSON o un formulario para esta peticion.

Para una ejecución exitosa, obtendrá una respuesta JSON que contiene al usuario con todos los roles asignados.

```json
{
    "id": 2,
    "name": "Example User 2",
    "email": "example@user_two.project",
    "roles": [
        {
            "id": 2,
            "name": "Usuario",
            "slug": "usuario"
        },
    ]
}
```

Observe que el usuario tiene un arreglo `Roles` y el rol con ID 3 no está presente en esta arreglo.

Para cualquier intento fallido o token incorrecto, recibirá una respuesta de error 401.

```json
{
    "message": "Unauthenticated."
}
```

## Notas

### Nombre de Usuario y Contraseña de Administrador predeterminados

Cuando ejecuta los sembradores (seeders) de la base de datos, se crea un usuario administrador predeterminado con el nombre de usuario '**admin@project.com**' y su contraseña es '**password**'. Puede iniciar sesión como este usuario administrador predeterminado y usar el token de portador en las próximas peticiones a la API donde se requiera la Ability de administrador.

Cuando envíe su aplicación a producción, recuerde cambiar la contraseña de este usuario, el correo electrónico o simplemente cree un nuevo usuario administrador y elimine el predeterminado.

### Rol predeterminado para nuevos Usuarios

El rol de `usuario` se les asigna cuando se crea un nuevo usuario. Para cambiar este comportamiento, abra su archivo `.env` y establezca el valor de `DEFAULT_ROLE_SLUG` en cualquier `slug de rol` existente. Los nuevos usuarios tendrán ese rol por defecto. Por ejemplo, si desea que sus nuevos usuarios tengan un rol de `supervisor`, configure `DEFAULT_ROLE_SLUG=customer` en su archivo `.env`

Hay cinco slugs de roles predeterminados en el proyecto.

| Slug del Rol        | Nombre del Rol      |
| ------------------- | ------------------- |
| super-administrador | Super Administrador |
| administrador       | Administrador       |
| gerente             | Gerente             |
| supervisor          | Supervisor          |
| usuario             | Usuario             |

Agregue en el encabezado `Accept: application/json` en sus peticiones a la API (IMPORTANTE)

```shell
Accept: application/json
```

Genial, ahora sabes todo para comenzar a crear tu próximo proyecto de API Tokens con autenticacion utilizando Laravel Sanctum y sus habilidades. ¡Ha disfrutar!
