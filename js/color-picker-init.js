jQuery(document).ready(function($){
    // abmcm_data object is localized from PHP via wp_localize_script
    // It should contain defaultColors map.
    if ( typeof abmcm_data !== 'undefined' && typeof abmcm_data.defaultColors !== 'undefined' ) {
        var defaultColorsMap = abmcm_data.defaultColors;

        // Initialize the color picker for each element with the class 'admin-menu-color-field'
        $('.admin-menu-color-field').each(function() {
            var $this = $(this);
            var optionName = $this.attr('name');

            // Get the default color from the localized map based on the input's name.
            var defaultColorForPicker = defaultColorsMap[optionName] || ''; // Use empty string if no default is found

            $this.wpColorPicker({
                defaultColor: defaultColorForPicker // This sets the default color swatch in the picker UI
            });
        });
    } else {
        // Fallback or debug for when abmcm_data is not available (shouldn't happen on settings page)
        console.warn('ABMCM: abmcm_data or defaultColors not found in JavaScript.');
        // Initialize without defaultColor option
        $('.admin-menu-color-field').wpColorPicker();
    }
});