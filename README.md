bono-component
==============

Bono Component is a general component for [Bono](https://github.com/xinix-technology/bono) to generate HTML code.

## Usage
```php
<?php
use \ROH\BonoComponent\PlainTable PlainTable;
use \Bono\App;

$_app = App::getInstance();
$_controller = $_app->controller;
$_table = new PlainTable($_controller->clazz);
?>

<h2><?php echo $_controller->clazz ?></h2>

<?php echo $_table->show($entries) ?>
```
