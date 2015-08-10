# Symfony Barcode Generator
Symfony 2 Barcode generator bundle by CodeItNow.IN

# Uses:
```php
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;

$barcode = new BarcodeGenerator();
$barcode->setText("0123456789");
$barcode->setType(BarcodeGenerator::Code128);
$barcode->setScale(2);
$barcode->setThickness(25);
$code = $barcode->generate();

echo '<img src="data:image/png;base64,'.$code.'" />';
```
