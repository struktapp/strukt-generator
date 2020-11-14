Strukt Generator
===

[![Build Status](https://travis-ci.org/pitsolu/strukt-generator.svg?branch=master)](https://packagist.org/packages/strukt/generator)
[![Latest Stable Version](https://poser.pugx.org/strukt/generator/v/stable)](https://packagist.org/packages/strukt/generator)
[![Total Downloads](https://poser.pugx.org/strukt/generator/downloads)](https://packagist.org/packages/strukt/generator)
[![Latest Unstable Version](https://poser.pugx.org/strukt/generator/v/unstable)](https://packagist.org/packages/strukt/generator)
[![License](https://poser.pugx.org/strukt/generator/license)](https://packagist.org/packages/strukt/generator)

## Intro

Simple package for generating templates and reading annotations.

## Templator

```php
$data = array(

    "title" => "The Title",
    "subtitle" => "Subtitle",
    "footer" => "Foot",
    "people" => array(
        
        array("name" => "Steve","surname" => "Johnson"),
        array("name" => "James", "surname" => "Johnson"),
        array("name" => "Josh", "surname" => "Smith")
    ),
    "page" => "Home"
);

$tpl = "<html>
<title>{{title}}</title>
<body>
<h1>{{subtitle}}</h1>
{{begin:people}}
<b>{{name}}</b> {{surname}}<br />
{{end:people}}
<br /><br />
<i>{{footer}}</i>
</body>
</html>";

$output = Strukt\Templator::create($tpl, $data);
```

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

## Reflector

```php
// $r = Strukt\Ref::createFrom(new Payroll\User);
$r = Strukt\Ref::create(Payroll\User::class);
$r->getRef();//ReflectionClass
//$r->noMake();//newInstanceWithoutConstructor
$r->make("pitsolu");//newInstanceArgs
$r->getInstance();//InstanceOf Payroll\AuthModule\Model\User
$r->prop("id")->getRef();//ReflectionProperty
$r->prop("id")->set(1);
$r->prop("id")->get();//1
$r->method("getUsername")->invoke();//pitsolu
$r->method("getUsername")->getRef(); //ReflectionMethod
$r->method("getUsername")->getClosure();//Closure
Strukt\Ref::func("array_sum")->invoke([1,2]);//3
Strukt\Ref::func("array_sum")->getRef();//ReflectionFunction
```
