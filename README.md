Strukt Generator
================

[![Latest Stable Version](https://poser.pugx.org/strukt/generator/v/stable)](https://packagist.org/packages/strukt/generator)
[![Total Downloads](https://poser.pugx.org/strukt/generator/downloads)](https://packagist.org/packages/strukt/generator)
[![Latest Unstable Version](https://poser.pugx.org/strukt/generator/v/unstable)](https://packagist.org/packages/strukt/generator)
[![License](https://poser.pugx.org/strukt/generator/license)](https://packagist.org/packages/strukt/generator)

## Intro
Package for generating class files

## SGF Compiler

Coming Soon ... 

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