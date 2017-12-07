<?php
require 'vendor/autoload.php';
$loader=new Twig_Loader_Filesystem('views');
$twig=new Twig_Environment ($loader);


$md5Filter=new Twig_SimpleFilter('md5',function($string){
	return md5($string);
});

$twig->addFilter($md5Filter);

//Set custom settings for twig
$lexer=new Twig_Lexer($twig,array(
		'tag_block'=> array('{','}'),
		'tag_variable'=> array('{{','}}')
));

$twig->setLexer($lexer);

echo $twig->render('web.php',array(
'name'=>'Maksim',
'age'=>52,

));