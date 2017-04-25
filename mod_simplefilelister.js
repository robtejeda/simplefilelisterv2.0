var curPageURL = window.location.href;

if (curPageURL.indexOf(".php?") > 0) {
    curPageURL += "&";
} else {
    curPageURL += "?";
}


(function(jQuery) {

    // wait till the DOM is loaded
    jQuery(document).ready(function() {

        var curBrowseDir = getCurBrowserDir();

        function getCurBrowserDir() {

            var location = getCurSection();

            var pathWithoutLastPiece = window.sfl_dirlocation.substring(0, window.sfl_dirlocation.lastIndexOf('/'));
            curBrowseDir = (pathWithoutLastPiece+'/'+location.substring(location.indexOf('#')+1)).replace('.','');

            return curBrowseDir;
        };

        function getCurSection() {

            var location = window.location.href;
            location = location.substring(location.indexOf('#')+1);
            return location;
        }

        /**
         * [Delete file button]
         */
        jQuery('.sfl_btnDelete').unbind('click').click( function(evt) {

            evt.preventDefault();
            evt.stopPropagation();

            var del = this.rel;
            var params = '&sflDelete=' + del;
            
            var msg = del.split('**');

            if (confirm('File Deleted \n(' + msg[1] + ')')) {

                //O3 CUSTOM CODE
                var params = (jQuery(this).attr("rel")).split("**");
                var thisLink = jQuery(this);


                jQuery.post("/modules/mod_simplefilelisterv1.0/rm_file.php",{
                    path:params[0],
                    name:params[1]
                })
                .done(function(data){
                    thisLink.parent().parent().fadeOut(500, function(){
                        thisLink.parent().parent().parent().remove();
                        return false;
                    });
                });
            }

            return false;
        });
    });
})(jQuery);