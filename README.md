Getting Started With PNSeoBundle for multi languages web apps
==================================

### Prerequisites
1. Symfony 3.4
2. [PNLocaleBundle](https://github.com/PerfectNeeds/locale-bundle)
3. [PNServiceBundle](https://github.com/PerfectNeeds/service-bundle)

Translations
======

If you wish to use default texts provided in this bundle, you have to make
sure you have translator enabled in your config.

```yaml
    # app/config/config.yml

parameters:
    # default locale 
    locale: en 
    # all locales separated by |
    app.locales: en|ar| 
    
framework:
    translator: ~
    default_locale: '%locale%'
```

Installation
------------

Installation is a quick (I promise!) 6 step process:

1. Download PNSeoBundle using composer
2. Enable the Bundle in AppKernel
3. Create your Seo class
4. Configure the PNSeoBundle
5. Import PNSeoBundle routing
6. Update your database schema
----

### Step 1: Download PNSeoBundle using composer
Require the bundle with composer:
```sh
$ composer require perfectneeds/seo-multi-lang-bundle "1.*"
```
### Step 2: Enable the Bundle in AppKernel
Require the bundle with composer:
```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new VM5\EntityTranslationsBundle\VM5EntityTranslationsBundle(),
        new PN\SeoBundle\PNSeoBundle(),
        new \PN\LocaleBundle\PNLocaleBundle(),
        new \PN\ServiceBundle\PNServiceBundle(),
        // ...
    );
}
```

### Step 3: Create your Seo class
The goal of this bundle is to persist some `Seo` class to a database. Your first job, then, is to create the
`Seo` class for your application. This class can look and act however
you want: add any properties or methods you find useful. This is *your*
`Seo` class.

The bundle provides base classes which are already mapped for most
fields to make it easier to create your entity. Here is how you use it:

1.  Extend the base `Seo` class (from the `Entity` folder if you are
    using any of the doctrine variants)
2.  Map the `id` field. It must be protected as it is inherited from the
    parent class.

#### Caution!

When you extend from the mapped superclass provided by the bundle, don't redefine the mapping for the other fields as it is provided by the bundle.

In the following sections, you'll see examples of how your User class should look, depending on how you're storing your users (Doctrine ORM).

##### Note

The doc uses a bundle named `SeoBundle`. However, you can of course place your seo class in the bundle you want.

###### Caution!

If you override the __construct() method in your Seo class, be sure to call parent::__construct(), as the base Seo class depends on this to initialize some fields.


#### Doctrine ORM User class

If you're persisting your seo via the Doctrine ORM, then your `Seo` class should live in the Entity namespace of your bundle and look like this to start:

*You can add all relations between other entities in this class

```php
<?php
// src/PN/Bundle/SeoBundle/Entity/Seo.php

namespace PN\Bundle\SeoBundle\Entity;

use Doctrine\ORM\Mapping\UniqueConstraint;
use Doctrine\ORM\Mapping as ORM;
use PN\SeoBundle\Entity\Seo as BaseSeo;
use PN\SeoBundle\Model\SeoTrait;

/**
 * Seo
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table("seo", uniqueConstraints={@UniqueConstraint(name="slug_unique", columns={"slug", "seo_base_route_id"})})
 * @ORM\Entity(repositoryClass="PN\Bundle\SeoBundle\Repository\SeoRepository")
 */
class Seo extends BaseSeo {

    use SeoTrait;
    
    /**
     * @ORM\OneToOne(targetEntity="\PN\Bundle\CMSBundle\Entity\DynamicPage", mappedBy="seo")
     */
    protected $dynamicPage;
    
    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
```

```php
<?php
// src/PN/Bundle/SeoBundle/Entity/Translation/SeoTranslation.php

namespace PN\Bundle\SeoBundle\Entity\Translation;

use Doctrine\ORM\Mapping as ORM;
use PN\SeoBundle\Entity\Translation\SeoTranslation as BaseSeoTranslation;

/**
 * @ORM\Entity
 * @ORM\Table(name="seo_translations")
 */
class SeoTranslation extends BaseSeoTranslation {

    /**
     * @var
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="PN\Bundle\SeoBundle\Entity\Seo", inversedBy="translations")
     * @ORM\JoinColumn(name="translatable_id", referencedColumnName="id")
     */
    protected $translatable;

}
```
### Step 4: Configure the PNSeoBundle
Add the following configuration to your config.yml file according to which type of datastore you are using.

```ymal
# app/config/config.yml 

doctrine:
   orm:
        # search for the "ResolveTargetEntityListener" class for an article about this
        resolve_target_entities: 
            VM5\EntityTranslationsBundle\Model\Language: PN\LocaleBundle\Entity\Language

pn_seo:
    seo_class: PN\Bundle\SeoBundle\Entity\Seo
```

### Step 5: Import PNSeoBundle routing files

```ymal
# app/config/routing.yml 

pn_seo:
    resource: "@PNSeoBundle/Resources/config/routing.yml"
```

### Step 6: Update your database schema
Now that the bundle is configured, the last thing you need to do is update your database schema because you have added a new entity, the `Seo` class which you created in Step 3.

```sh
$ php bin/console doctrine:schema:update --force
```

------
# How to use PNSeoBundle
