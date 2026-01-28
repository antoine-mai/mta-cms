---
description: PHP File Head Structure Template
---

This workflow defines the standard header (head) structure for PHP files in this project. Use this template when creating new PHP controller files.

## PHP Header Template

```php
<?php declare(strict_types=1); namespace App\Controller{{SUB_NAMESPACE}};
/**
 * 
**/
use {{USE_STATEMENTS}}
/**
 * 
**/
class {{CLASS_NAME}} extends {{PARENT_CLASS}}
{
    // Implementation
}
```

## Rules for New PHP Files

1.  **Single-line Header**: The first line must include `<?php`, `declare(strict_types=1);`, and `namespace ...` all on line 1.
2.  **Docblocks**: Use the following format for empty docblocks before `use` statements and class definitions:
    ```php
    /**
     * 
    **/
    ```
3.  **Fully Qualified Use Statements**: Prefix `use` statements with a backslash if they start with a root namespace like `\Symfony`.
4.  **Formatting**: Ensure there is balanced whitespace around the docblocks and use statements as seen in existing files.

### Example for Admin Controller:

```php
<?php declare(strict_types=1); namespace App\Controller\Admin;
/**
 * 
**/
use \Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use \Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\Routing\Attribute\Route;
/**
 * 
**/
class NewController extends AbstractController
{
    // ...
}
```
