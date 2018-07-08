/* initialize here the select2 */
jQuery(function(){
    jQuery('.custom_field_cpt_select').each( function(){
        var currentCptSelect = this;
        jQuery( currentCptSelect ).select2();
    });
});
