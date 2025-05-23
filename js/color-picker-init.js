jQuery(document).ready(function($){
    // Initialize the color picker for all elements with the class 'admin-menu-color-field'
    // We need to fetch the default colors from the PHP's registered settings data
    // This data is usually available globally in the admin if the Settings API is used correctly.

    // A map to hold default colors for each option name
    var defaultColors = {
        'amcm_background_color': '#23282d', // Default WordPress admin background
        'amcm_text_color': '#a7aaad',      // Default WordPress admin text
        'amcm_hover_background_color': '#0073aa', // Default WordPress admin hover background
        'amcm_hover_text_color': '#ffffff', // Default WordPress admin hover text
        'amcm_current_item_background_color': '#191e23', // Default WordPress admin current item background
        'amcm_current_item_text_color': '#ffffff', // Default WordPress admin current item text
        'amcm_admin_bar_background_color': '#23282d', // Default admin bar background
        'amcm_admin_bar_text_color': '#eeeeee', // Default admin bar text color
        'amcm_admin_bar_hover_color': '#0073aa' // Default admin bar hover color
    };

    $('.admin-menu-color-field').each(function() {
        var $this = $(this);
        var optionName = $this.attr('name'); // Get the name attribute of the input field
        var defaultColor = defaultColors[optionName] || ''; // Get the default from our map, or empty string

        $this.wpColorPicker({
            defaultColor: defaultColor, // Set the default color for the picker UI
            // The 'clear' option is omitted, so it will perform its default behavior (clear the field).
        });
    });
});