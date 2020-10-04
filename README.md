# Presstomizer
Build Custom WordPress Customizer Instances

This class allows you to extend the customizer to provide customization options for your custom WordPress plugins.
It removes all third-party panels/sections and provides a way to display both the frontend and backend of your custom
customizer screen.

## Installation

- Clone this repo.
- Copy the presstomizer/class-presstomizer.php to your theme and prefix the class to prevent conflicts with other plugins using the class.
- Include the class before the init hook.

## Usage

You need to instantiate the class for each custom customizer screens that you need to create. For each instance, pass in a unique id to identify your instance with.

The best way to come up with an id name is "{plugin_prefix}_{functionality}"

**Example**

```php

    // Instantiate the customizer.
    $my_customizer = new presstomizer( 'email_customizer_customize' );
```