
/**
 * Nwdthemes Standalone Slider Revolution
 *
 * @package     StandaloneRevslider
 * @author		Nwdthemes <mail@nwdthemes.com>
 * @link		http://nwdthemes.com/
 * @copyright   Copyright (c) 2015. Nwdthemes
 * @license     http://themeforest.net/licenses/terms/regular
 */

var Language = new function(){
    
    var t = this;
    
    t.init = function() {
        jQuery('#language').on('change', changeLanguage);
    }
    
    var changeLanguage = function() {
        var newArgs = ['lang=' + jQuery(this).val()];
        var arrLocation = document.location.href.split('?');
        if (arrLocation.length == 2)
        {
            var oldArgs = arrLocation[1].split('&');
            jQuery.each(oldArgs, function(key, arg) {
                var arrArg = arg.split('=');
                if (arrArg[0] != 'lang')
                {
                    newArgs.push(arg);
                }
            });
            
        }
        document.location.href = arrLocation[0] + '?' + newArgs.join('&');
    }

}

jQuery(document).ready(Language.init);