/**
 * Script used in :
 * - @AppBundle/location/create.html.twig
 * - @AppBundle/location/edit.html.twig
 *
 * @author Vlad Riabchenko <contact@vria.eu>
 */
$(document).ready(function() {
    var $locationCountry = $('#location_country');
    var $locationRegion = $('#location_region');
    var $locationCity = $('#location_city');

    /**
     * When the country is changed this function clears all regions and cities.
     * Then the regions list is repopulated with the options corresponding to
     * chosen country.
     */
    $locationCountry.change(function() {
        // Clear the regions and cities lists.
        $locationRegion.find('option[value!=""]').remove();
        $locationCity.find('option[value!=""]').remove();

        // Get regions corresponding to chosen country.
        $.ajax({
            url: Routing.generate('location_get_regions_for_country', {id: $locationCountry.val()}),
            method: 'GET',
            success: function (data) {
                // Repopulate regions list
                data.regions.forEach(function(region) {
                    $locationRegion.append('<option value="' + region.id + '">' + region.name + '</option>');
                });
            }
        });
    });

    /**
     * When the region is changed this function clears current cities.
     * Then the cities list gets repopulated with the options corresponding
     * to the chosen region.
     */
    $locationRegion.change(function() {
        // Clear the cities list.
        $locationCity.find('option[value!=""]').remove();

        // Get cities corresponding to the chosen region.
        $.ajax({
            url: Routing.generate('location_get_cities_for_region', {id: $locationRegion.val()}),
            method: 'GET',
            success: function (data) {
                // Repopulate cities list
                data.cities.forEach(function(city) {
                    $locationCity.append('<option value="' + city.id + '">' + city.name + '</option>');
                });
            }
        });
    });
});
