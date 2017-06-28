Strukt Generator
================

[![Build Status](https://travis-ci.org/pitsolu/strukt-generator.svg?branch=master)](https://packagist.org/packages/strukt/generator)
[![Latest Stable Version](https://poser.pugx.org/strukt/generator/v/stable)](https://packagist.org/packages/strukt/generator)
[![Total Downloads](https://poser.pugx.org/strukt/generator/downloads)](https://packagist.org/packages/strukt/generator)
[![Latest Unstable Version](https://poser.pugx.org/strukt/generator/v/unstable)](https://packagist.org/packages/strukt/generator)
[![License](https://poser.pugx.org/strukt/generator/license)](https://packagist.org/packages/strukt/generator)

## Intro
Package for generating class files

## sgf Compiler

Sample Template

```
@ns:Payroll\AuthModule\Router
@class:Role
@inherit:\App\Data\Router
@descr

    Router for roles

    @author: Moderator <pitsolu@gmail.com>
@descr

@param:public>static>name#string="Payroll\AuthModule\Router\Role"

@method:findRoleById#Strukt\Rest\ResposeType\JsonResponse@param:id#integer
@body://
@descr:@Route:/role/{id:int}
@descr:@Method:POST
@descr
    Blah
    Blah
    Blah
@descr

@method:deleteByRoleId#Strukt\Rest\ResposeType\JsonResponse@param:id
@body://
@descr:@Route:/role/{id:int}
@descr:@Method:DELETE
@descr:Delete Role

@method:findAll#Strukt\Rest\ResposeType\JsonResponse
@body:// To be implemented
@descr:@Route:/role/all
@descr:@Method:GET|POST
@descr:Find All

@method:addRolePermission#string@param:role_id#integer|perm_id#integer
@body
        $rolePerm = \Payroll\AuthModule\Controller\Role::addPerm($role_id, $perm_id);

        return "success";
@body
@descr:@Route:/role/{role_id:int}/add/perm/{perm_id:int}
@descr:@Method:POST
@descr: Role Add Permission
```

Code for compiling

```php
$sgfRoleController = \Strukt\Fs::cat("fixtures/root/sgf/app/src/Payroll/AuthModule/Controller/Role.sgf");
        
$parser = new \Strukt\Generator\Parser($sgfRoleController);
$compiler = new \Strukt\Generator\Compiler($parser, array(

    "excludeMethodParamTypes"=>array(

        "string",
        "integer",
        "double",
        "float"
    )
));

exit($compiler->compile());
```

Result: [See Here](https://github.com/pitsolu/strukt-generator/blob/master/fixtures/root/app/src/Payroll/AuthModule/Router/Role.php)


## Annotations

Annotation supported format:

```php
/**
* @Route(/)
*/
class DefaultController{

    /**
    * @Route(/hello/{to:alpha})
    * @Method(POST, GET)
    * @Provides(application/html) 
    */
    function hello($to){ ...

    /**
    * @Route(/login)
    * @Method(GET)
    * @Secure(username=test, password=test)
    * @Expects(username,password)
    *
    * note the below will not be parsed
    * @param str $username
    * @param str $password
    */
    function login($username, $password){ ...
```

Run parser:

```php
$parser = new \Strukt\Annotation\Parser\Basic(new \ReflectionClass("\Controller\DefaultController"));
print_r($parser->getAnnotations());
```

Expected output:

```php
Array
(
    [class] => Array
        (
            [Route] => Array
                (
                    [name] => Route
                    [item] => /
                )

        )
    [methods] => Array
        (
            [hello] => Array
                (
                    [Route] => Array
                        (
                            [name] => Route
                            [item] => /hello/{to:alpha}
                        )

                    [Method] => Array
                        (
                            [name] => Method
                            [items] => Array
                                (
                                    [0] => POST
                                    [1] => GET
                                )

                        )

                    [Provides] => Array
                        (
                            [name] => Provides
                            [item] => application/html
                        )

                )

            [login] => Array
                (
                    [Route] => Array
                        (
                            [name] => Route
                            [item] => /login
                        )

                    [Method] => Array
                        (
                            [name] => Method
                            [item] => GET
                        )

                    [Secure] => Array
                        (
                            [name] => Secure
                            [items] => Array
                                (
                                    [username] => test
                                    [password] => test
                                )

                        )

                    [Expects] => Array
                        (
                            [name] => Expects
                            [items] => Array
                                (
                                    [0] => username
                                    [1] => password
                                )

                        )

                )

        )

    [class_name] => Controller\DefaultController
)
```