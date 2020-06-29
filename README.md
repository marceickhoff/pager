# Pager

Pager is a minimal PHP framework to easily build multilingual file-based websites. No database required. Perfectly fit for smaller projects or as a base to extend from for larger ones.

## Requirements

* PHP 5.6 or later
* Apache web server
* Webspace that allows access to non-public directories

## Quick start

You can set up Pager in 3 easy steps:

1. Fork the repository
2. Clone the forked repository
3. Set up Apache to point to the `public` directory

To start building your website, let's look at the two main concepts of Pager:

### Templates

The `/templates` directory contains reusable templates and template parts. All these files together make up your website's theme. By default, you see a `default.php` template and the `header.php` and `footer.php` template parts. You can create as many templates and template parts as you like.

*Learn more about [Templates](https://github.com/marceickhoff/Pager#template).*

#### Assets

All publicly available assets like stylesheets and scripts need to be inside the `/public` directory. That means that if you use CSS/JS compilers, you should set their outputs to somewhere inside there. If you want to use a library like Bootstrap, put it here.

*Learn more about [Assets](https://github.com/marceickhoff/Pager#assets).*

### Content

The `/content` directory contains sub-directories named according to [RFC 5646](https://gist.github.com/msikma/8912e62ed866778ff8cd). These are your website's supported languages. By default you see only an `en` directory for the English language. The Localization Manager always tries to find the best language match based on the browser's `Accept-Language` HTTP header. If you only support `en` than this will be used for every visitor.

*Learn more about [Requests](https://github.com/marceickhoff/Pager#request) and [Localization](https://github.com/marceickhoff/Pager#localization).*

#### Page files

Inside the language sub-directories you'll have your page content files. These files contain the actual content of your website. Page content is embedded into the theme via the `Content::get()` method in a template. For details see below.

*Learn more about [Content](https://github.com/marceickhoff/Pager#content)*.

You can set page-specific meta information via the `Meta::set()` method and embed custom assets (script, stylesheets, etc.) via the `Asset::add()` method. For details see below.

*Learn more about [Meta information](https://github.com/marceickhoff/Pager#meta) and [Assets](https://github.com/marceickhoff/Pager#assets).*

Page files are parsed before the template so you can even individually change the used template for each page via the `Template::set()` method.

You can also create custom directory structures inside the language sub-directory to enable sub-page structures on your website. Each directory can contain a `index.php` file that is used if the directory is requested directly.

##### Error pages

Inside your language sub-directory you'll see another sub-directory. It's the `_error` directory. It contains page content files for different HTTP status codes. By default, there is already a `404.php` file that is included if the servers HTTP response code is 404 (page not found). It works the same way as a regular page content file.

#### Directory files

Inside each directory inside the language sub-directory can (but doesn't have to) be a `_directory.php` file that contains defaults like meta information or assets for its particular directory. These configurations will be applied to all subdirectories.

There should always be a `_directory.php` file in the language root that acts as a default for all pages.

You can also put additional configurations here.

*Learn more about [Config](https://github.com/marceickhoff/Pager#config).*

## Class reference

### Assets

Use the Asset Manager `Assets` if you want to embed certain assets like stylesheets or scripts for specific pages that you don't want to embed globally into the template.

Where you store your assets is up to you. Pager doesn't enforce a specific project structure.

Assets always belong to a certain **section**. Use sections to control where your assets are embedded. There are no predefined sections. If you don't specify a section when you `add()` an asset it will default to `head`. It is good practice to support `head` and `foot` sections in your template.

#### Examples

A good place to use the `add()` method is in a specific page or `_config.php` file, for example.

```php
// Add "/theme/css/additional.css" to "head" section
Assets::add('<link rel="stylesheet" href="/theme/css/additional.css">');

// Add "specific.js" to "foot" section
Assets::add('<script type="text/javascript" src="specific.js"></script>', 'foot');
```

Use the `get()` method in your template (or wherever you want to embed scripts):

```html
<head>
    ...
    <!-- Assets in "head" section -->
    <?php echo Assets::get('head'); ?>
</head>
<body>
    ...
    <!-- Assets in "foot" section -->
    <?php echo Assets::get('foot'); ?>
</body>
```

### Config

The Config Manager `Config` is used to store and read system configurations. You can either `get()` or `set()` configurations.

#### Examples

```php
// Set "example_config_1" to true
Config::set('example_config_1', true);

// You can also set multiple configurations at once
Config::set([
    'example_config_2' => 'something',
    'example_config_3' => 123
]);
```

The `get()` method returns the previously `set()` configurations:

```php
// Get "example_config_1"
Config::get('example_config_1'); // true

// Get "example_config_2" and "example_config_3"
Config::get('example_config_2'); // "something"
Config::get('example_config_3'); // 123
```

#### Pager configurations

Pager currently uses the following configurations. These are made in the main config file `config.php`.

Name | Type | Default | Description
--- | --- | --- | ---
`default_localization` | string | `en` | Language tag in [RFC 5646 format](https://gist.github.com/msikma/8912e62ed866778ff8cd) (must exists as a directory in `/content`)-
`default_localization_redirect` | bool | `false` | Enforce use of language subdirectory in URL (e.g. `/en/`) with automatic redirect even if it's the default language.
`custom_routes` | array | `[]` | Holds information about custom routes. Learn more about [custom routes](https://github.com/marceickhoff/Pager#router).

You are encouraged to use the Config Manager for your own extensions.

### Content

The Content Manager `Content` is responsible for page content. Page content is either provided by a physical page file inside the `/content` directory or by a [custom route](https://github.com/marceickhoff/Pager#router).

The page file will always be parsed **before** the template. This enables you to add assets and configurations and even change or disable the template from inside a page file. The output of the page file will be caught in a buffer and can be retrieved by using the `get()` method.

You can also replace all of the previous content output by an error file using the `error()` method. For an example see below.

Note that any file or directory starting with an underscore will be ignored and treated like a 404 error. This is to prevent users from directly accessing default configurations files, the `_error` directory or any other file or directory that should not be directly accessed.

#### Examples

```php
// Overrides the page file that will be included (has to be called before Content::get() to take effect)
Content::set('en/something-else.php');

// Returns the output of a route method or page file
Content::get(); // E.g. "<main><h1>Hello World!</h1></main>"

// Get the page file based on the request and the localization
Content::file(); // E.g. "pages/en/index.php"

// Replaces all previous content with error page file and terminates.
Content::error(404); // Shows 404 error page
```

### Localization

The Localization Manager `Localization` handles languages and regions. It's job is mainly to negotiate the best content language based on the `Accept-Language` request HTTP header sent by the browser and the available localizations of your website.

The `Accept-Language` header usually contains something like `de-DE, de;q=0.9, en;q=0.8`. This represents the user's preferred content language. This user, for example, wants content to be German (Germany), any German or any English in this order.

#### Supporting multiple languages

To support different languages on your website you need to create according subdirectories in your `/content` directory.
Name these according to [RFC 5646](https://gist.github.com/msikma/8912e62ed866778ff8cd) (e.g. `de` for German or `en-GB` for British English). You can (but don't have to) specify a region to provide different content based on a user's region. The Localization manager will negotiate the best fitting localization. If nothing can be found, it will use the default localization set in the configuration. If your website's default language is not English you should consider offering a way so that users can manually select the best fitting localization.

#### Examples

```php
// Get (or negotiate if not set) localization in RFC 5646 format
Localization::get(); // E.g. "en-GB"

// Get (or negotiate if not set) language code
Localization::get_language(); // E.g. "en"

// Get (or negotiate if not set) region code
Localization::get_region(); // E.g. "GB"

// Check if current localization is same as default (to get default localization use Config::get('default_localization'))
Localization::is_default(); // E.g. true

// Lists all available localizations (i.e. directories in /content)
Localization::get_supported(); // E.g. ["en-GB", "en-US", "de"]

// Overrides current localization (i.e. manually changes the language and/or region)
Localization::override('de');
```

### Meta

The Meta Manager `Meta` is used to store and read meta information. You can either `get()` or `set()` meta data. There are no predefined meta keys. You are free to name them however you want.

#### Examples

```php
// Set website_title to "My new website"
Meta::set('website_title', 'My new website');

// You can also set multiple meta data at once
Meta::set([
    'title' => 'New page',
    'favicon' => Config::get('base_path').'/favicon.png',
    'robots' => 'index,follow',
    'description' => 'This is a new website built with Pager.'
]);
```

The `get()` method returns the previously `set()` meta information. Use it in your template:

```html
<head>
    <!-- Title -->
    <title><?php echo Meta::get('website_title'); ?> | <?php echo Meta::get('title'); ?></title>

    <!-- Other meta data -->
    <link href="<?php echo Meta::get('favicon'); ?>" rel="icon" type="image/png">
    <meta name="robots" content="<?php echo Meta::get('robots'); ?>">
    <meta name="description" content="<?php echo Meta::get('description'); ?>">
</head>
```

### Request

The Request Manager `Request` parses the users request URI. It also force-overrides the localization if the first subdirectory of the request is a supported localization (`https://domain.tld/en/some/request` forces English localization if supported). The request will always be without the localization subdirectory.

If the request ends with a slash like `https://domain.tld/some/request/` the request automatically becomes `some/request/index`. Otherwise it would be `some/request`. This behaviour enables the distinction of physical pages from subdirectories with the same name (`/request/` gets directory, `/request` gets page).

If a page is requested but only a accordingly named directory exists it becomes the directory (and vice versa). That means that `https://domain.tld/some/request` becomes `some/request/index` if only a directory named `request` exists but not a page.

#### Examples

```php
// Get the current request as an array and override localization if appropriate
Request::get(); // E.g. ["index"], ["some-page"], ["sub", "some-page"], ["sub", "some-dir", "index"], etc.

// Calls Request::get() an concatenates result with slashes
Request::get_string(); // E.g. "index", "some-page", "sub/some-page", "sub/some-dir/index", etc.

// Checks if the current request starts with a specific string. This is useful for creating highlighted states in navigation menus.
Request::starts_with('sub/some-page'); // Returns boolean
```

### Router

The `Router` manages custom dynamic routes that don't require corresponding files in the `/content` directory. This is useful if you want to dynamically fetch data from a database, like blog posts.

You can either use the default [configuration](https://github.com/marceickhoff/Pager#config) key `custom_routes` or the Router's `add()` method to define custom routes. Each route has a callable attached to it that will be called if the current request matches the custom route. The first matching route will be chosen and custom routes will be chosen over physical page files.

You can create placeholders for parameters with curly brackets like `post/{id}` for example. These parameters will be sanitized and passed to the attached callable.

The `Router` also includes a small set of utility methods. This includes `url()`, `redirect()` and `refresh()`. For examples see below.

#### Examples

##### Example.class.php
```php
class Example {
    public static function show($id) {
        echo 'The parameter is '.$id.'!';
    }
}
```

##### Defining custom routes via Config
```php
include 'some/path/Example.class.php';

Config::set('custom_routes', [
    //Route with 2 paramters and anonymous function
    'example/{param1}/{param2}' => function($p1, $p2) { echo $p1.', '.$p2; },

    //Route with 1 paramter and method (see below)
    'example/{id}' => [Example::class, 'show']
]);
```

##### Defining custom routes via method
```php
include 'some/path/Example.class.php';

//Single route
Router::add('example/{param1}/{param2}', function($p1, $p2) { echo $p1.', '.$p2; });
Router::add('example/{id}', [Example::class, 'show']);

//Multiple at once
Router::add([
    'example/{param1}/{param2}' => function($p1, $p2) { echo $p1.', '.$p2; },
    'example/{id}' => [Example::class, 'show']
]);
```

##### Utility methods
```php
Router::url('example/page'); //E.g. "/your/installation/path/example/page"
Router::url('example/page', true); //E.g. "https://example.com/your/installation/path/example/page"

Router::redirect('https://example.com/target'); //Redirects to "https://example.com/target"

Router::refresh(); //Refreshes the page
```


### Sitemap

Work in progress.

### Template

The Template Manager `Template` is responsible for creating templates. A template is part of a theme and controls the arrangement of different template parts. You can have multiple templates available but only use one at a time. Templates are stored inside the `/templates` directory and can be named, pre- or suffixed however you want.

The `build()` method is also the entry point to the framework and is called in the main `index.php` file.

#### Examples

```php
// Initializes the master template
Template::init();

// Sets the template to "default"
// If the template is set to an empty value (null, 0, false, empty string, etc.), the above mentioned init() method will directly include the content file without a template
Template::set('default');

// Checks if a template is set
Template::is_set(); //E.g. true

// Gets the current template name
Template::get(); //E.g. "default"

// Returns the current template file or null if no template is set
Template::file(); //E.g. "templates/default.php"

// Includes a template part
Template::part('header'); //Includes "templates/header.php"

// You can also use directory structures in names
Template::set('sub/template'); //Sets template to "templates/sub/template.php"
Template::part('sub/part'); //Includes "templates/sub/part.php"
```
