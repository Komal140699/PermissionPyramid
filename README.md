What is in this project :
    This project is an illustration to laravel-spatie [Role and permission management]
    In this project i have taken users with different roles ie. admin, dept_head, employee .
    
    Admin Role :
    Admin can create employees,delete,view employees .
    Admin can also view task based report of employees .

    Department Head :
    Department Head can create tasks , allocate tasks to employees and view all tasks created.
    
    Employee :
    Employee can only view tasks allocated to him.


Technology used :
    I have used Laravel 10 in this project.
    Laravel spatie version 6.2 is used in this project .
    Jwt authentication version 2.1 is used in this project .


Here are the steps for installation for our role and permission example-app :
    1. Clone this project.
    2. Run  "composer install" command.
    3. Make an .env file using .env.example file.
    3. Run  "php artisan jwt:secret" to generate secret key for JWT authentication and copy paste the secret key in .env file .
    4. Run "php artisan view:clear ; php artisan route:clear ; php artisan config:clear ; php artisan cache:clear;" to clear cache .
    5. Make new database named "role_management".
    6. Now Run "php artisan migrate" command. 
    7. Then Run "php artisan db:seed --class=RolesAndPermissionSeeder" command to import default roles and permissions.
    8. Now Import Collection and Environment in your Postman workspace and you are all ready to run the APIs.


Admin Credentials:
    username: superadmin@ultivic.com
    password: superadmin@123
