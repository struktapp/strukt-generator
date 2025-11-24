<?php

$data = array(

    "title" => "The Title",
    "subtitle" => "Subtitle",
    "footer" => "Foot",
    "people" => array(
        array("name" => "Steve","surname" => "Johnson"),
        array("name" => "James", "surname" => "Johnson"),
        array("name" => "Josh", "surname" => "Smith")
    ),
    "enable_footer" => true,
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
{if enable_footer}
<i>{{footer}}</i>
{/if}
</body>
</html>";

$expected = "<html>
<title>The Title</title>
<body>
<h1>Subtitle</h1>
<b>Steve</b> Johnson<br />
<b>James</b> Johnson<br />
<b>Josh</b> Smith<br />
<br /><br />
<i>Foot</i>
</body>
</html>";


test("template", function() use($tpl, $data, $expected){

	$output = template($tpl, $data);
	expect($output)->toBe($expected);
});