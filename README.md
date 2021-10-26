# Observer pattern in Symfony forms

[![Build Status](https://travis-ci.org/vria/observer-pattern-symfony-forms.svg?branch=master)](https://travis-ci.org/vria/observer-pattern-symfony-forms)

The purpose of this project is to demonstrate the use of the [form events].
Read a [detailed description] in my blog.

This application allows you to manage locations. [LocationController] is used
to:
- [list locations],
- [create location],
- [edit location].

For simplicity, there is a bare minimum of information in each entity:
- [Location] contains only a city,
- [City] contains its name and a reference to the region in which it is located,
- [Region] has a name and belongs to a country,
- [Country] is described by its name.

The key element of application is [LocationType] form type that is used for both
creating and editing a location. This form provides the following fields:

- `country` is a list of all countries
- `region` is a list of all regions of the **selected** country. If the country 
  is not selected this list should be empty.
- `city` - a list of all cities of the **selected** region. If the region is not
  selected this list should be empty.

When a country is selected, the list of regions is updated by [JavaScript code]
to contain only the regions of a chosen country. For example if *Germany* is 
selected the region list must contain only *Baden-Württemberg*, *Bavaria* and
other *german* regions. The same happens when a region changes: the list of
cities is updated to contain only the cities of chosen region.

## Install
```
composer install
bin/console doctrine:database:create
bin/console doctrine:schema:update --force
bin/console doctrine:fixtures:load -n
```

## Run in docker
```
cd docker
docker-compose up -d
```

[form events]: https://symfony.com/doc/current/form/events.html
[LocationController]: src/Controller/LocationController.php
[list locations]: src/Controller/LocationController.php#L44
[create location]: src/Controller/LocationController.php#L66
[edit location]: src/Controller/LocationController.php#L102
[Location]: src/Entity/Location.php
[City]: src/Entity/City.php
[Region]: src/Entity/Region.php
[Country]: src/Entity/Country.php
[LocationType]: src/Form/Type/LocationType.php
[JavaScript code]: public/js/location_form.js
