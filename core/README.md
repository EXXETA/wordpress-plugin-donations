# WWF-Banner-Core

This package contains common PHP code used by all WWF-Banner plugins.

*Note:* This code was extracted as there was only one other plugin using this core. Therefore you might find issues, problems
or missing features. That means: **If there is something wrong or missing, feel free to add or request it!**

## How-To use this package/How-To start a new shop plugin

Goal: You should use this package if you want to implement a WWF Germany shop-specific banner plugin.

NOTE: The following class and interface names are prefixed implicitly with the namespace `\exxeta\wwf\banner\`
which is the one defined by this package.

1. You have to derive from all Interfaces in this package. For convenience you can extend from the more 
generic *Abstract-* interface implementations.
2. Implement these classes in your target shop system plugin:
    * *CharityProductManager* extending from *AbstractCharityProductManager*
        * This manager-class stores the available products and provides methods to get the shop-specific objects and IDs.
        * Also provides methods for you to get the campaign information (e.g. description texts, slugs) from.
    * *SettingsManager* extending from *AbstractSettingsManager*
        * This manager-class stores all the settings and options this plugin can use and provides shop-specific ways to get and store these values.
    * *BannerHandler* implementing *BannerHandlerInterface*
        * A handler-class to customize the banner rendering in a shop-specific way, which is implemented in the `(Mini-)Banner` class.
    * *ReportHandler* implementing *BannerHandlerInterface*
        * A handler-class to customize the general report generation process, which is implemented in the `ReportGenerator` class.
3. Several code places need an instance of a *DonationPluginInterface*. This will help you:
    `new DonationPlugin(CharityProductManager::class, SettingsManager::class)` 
    (the arguments should be part of your plugin's namespace)
4. Your plugin will need to create the campaigns/products in the target shop system as real products and probably you want to add
product images as well (e.g. for the campaign-specific WWF coin-logo). In addition you'll probably need additional CSS styles or JavaScript and
in some cases it is useful to add the banner images as objects or "attachments" to the shop. Always ensure you have the permission to use all the assets.
5. Instantiate the `Banner` or the `Mini-Banner` class and call the `->render()` method to produce the markup which 
essentially is HTML and some "plain" JavaScript (which needs no frameworks and will work on any browser, even IE9+). 

## FAQ
#### How-To add this core package to a composer project?
At first create a new subdirectory in this repository on the top-level, create a new composer project and add this to your `composer.json`:
```json
{
...
  "repositories": [
    {
      "type": "path",
      "url": "../core",
      "options": {
        "symlink": false
      }
    }
  ]
...
}
```
Then, in your new project, do: `composer require exxeta/wwf-donations-shop-banner-core` and the project
 should have been provided with this core-package.

NOTE: The symlink-disable-option is needed on Windows OS because the symlink-feature won't work in combination with `docker-compose`.
You should remove the "options"-part if you are not on Windows or if you know what it means.

#### Are there any "reference implementations"?
Yes, but only one and it is the Wordpress banner plugin, also contained in this repository.

#### How-To use customize report templates?
You will need to overwrite the `DonationPlugin` class and provide custom paths to the new templates in the `includeContentTemplate`- and/or
the `includeReportTemplate`-method. A list of available template parameters can be seen by var_dumping the `$args` array inside 
your custom template.

#### How-To customize banner templates?
At this moment you will have to provide your custom `Banner` implementation. The class `MiniBanner` can be used as inspiration.
If you want to change some styling only, you can provide a custom css class which is added to the banner markup. 
Afterwards you can write custom style rules for it and customize the look and feel of the banner feature.

#### Is there a css structure/hierarchy/system of the banner markup?
Yes, but you have to study the implementation of the `render()`-methods of the `(Mini-)Banner` classes to get this information.
The top CSS classes are `.cart-donation-banner` (for the default banner) and `.cart-donation-mini-banner` (for the smaller one).
You may also want to look at the SCSS of the WordPress plugin [here](https://github.com/EXXETA/wordpress-plugin-donations/blob/master/wwf-donations-plugin/styles/banner.scss) which simply can be re-used.

#### Is there a (visual) style guide?
No, not in a written form. See the previously linked SCSS file for the WordPress plugin and you are strongly encouraged to use SCSS and a style-preprocessor.
Also note, that your styling rules should never depend on specific Themes/CSS Frameworks, as long as you have no good reason to do this. 

## Testing
Execute this command to run the unit tests of this package.
 
`$ ./core/vendor/bin/phpunit core/test/`

## TODO
- How to handle assets (images + styles + scripts) in a "core"-way?
- Extract information texts to core, too! + documentation
- Add general plugin description (diagram?)
- Customize build script and main project's README
- Extract SCSS to core + documentation
- Run code inspection + static code analysis tools