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

        jQuery('#sfl_ARefresh').live('click', function() {

            var params = '&sflDir=' + getCurBrowserDir();

            jQuery('#div_sflcontent')
                .css('text-align', 'center');

            jQuery('#div_sflcontent')
                .html('')
                .append('<img style="position: relative; top: 50px;" src="/modules/mod_simplefilelisterv1.0/images/ajax-loader.gif" />')
                .fadeIn(700, function() {
                });

            jQuery.ajax({
                type: 'GET',
                url: curPageURL,
                data: 'sflaction=dir' + params,
                cache: false,
                success: function(data) {
                    jQuery('#div_sflcontent')
                        .css('text-align', 'left');

                    jQuery('#div_sflcontent')
                        .html('')
                        .append(data);
                }
            });

            return false;
        });

        jQuery('.sfl_btnBrowseDir').live('click', function() {

            var dir = this.rel;
            var params = '&sflDir=' + dir;
            curBrowseDir = dir;

            jQuery('#div_sflcontent')
                .css('text-align', 'center');

            jQuery('#div_sflcontent')
                .html('')
                .append('<img style="position: relative; top: 50px;" src="/modules/mod_simplefilelisterv1.0/images/ajax-loader.gif" />')
                .fadeIn(700, function() {
                    //$('#div_sflcontent').append("DONE!");
                });

            jQuery.ajax({
                type: 'GET',
                url: curPageURL,
                data: 'sflaction=dir' + params,
                cache: false,
                success: function(data) {
                    jQuery('#div_sflcontent').css('text-align', 'left');
                    jQuery('#div_sflcontent').html('').append(data);
                }
            });

            return false;
        });

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

                jQuery.post("external/rmFile.php",{
                    path:params[0],
                    name:params[1]
                })
                .done(function(data){
                    thisLink.parent().parent().fadeOut(500, function(){
                        thisLink.parent().parent().remove();
                        return false;
                    });
                });
            }

            return false;
        });

        jQuery('#sfl_ASortDesc').live('click', function() {

            var params = '&sflSort=desc&sflDir=' + getCurBrowserDir();

            if (document.getElementById("sflSortDesc").className == "") 
                return false;

            document.getElementById("sflSortAsc").className = "sfl_shadow";
            document.getElementById("sflSortDesc").className = "";

            jQuery('#div_sflcontent')
                .css('text-align', 'center');

            jQuery('#div_sflcontent')
                .html('')
                .append('<img style="position: relative; top: 50px;" src="/modules/mod_simplefilelisterv1.0/images/ajax-loader.gif" />')
                .fadeIn(700, function() {
                    //$('#div_sflcontent').append("DONE!");
                });

            jQuery.ajax({
                type: 'GET',
                url: curPageURL,
                data: 'sflaction=sort' + params,
                cache: false,
                success: function(data) {
                    jQuery('#div_sflcontent').css('text-align', 'left');
                    jQuery('#div_sflcontent').html('').append(data);
                }
            });

            return false;
        });


        jQuery('#sfl_ASortAsc').live('click', function() {

            var params = '&sflSort=asc&sflDir=' + getCurBrowserDir();

            if (document.getElementById("sflSortAsc").className == "") 
                return false;

            document.getElementById("sflSortAsc").className = "";
            document.getElementById("sflSortDesc").className = "sfl_shadow";

            jQuery('#div_sflcontent')
                .css('text-align', 'center');

            jQuery('#div_sflcontent')
                .html('')
                .append('<img style="position: relative; top: 50px;" src="/modules/mod_simplefilelisterv1.0/images/ajax-loader.gif" />')
                .fadeIn(700, function() {
                    //$('#div_sflcontent').append("DONE!");
                });

            jQuery.ajax({
                type: 'GET',
                url: curPageURL,
                data: 'sflaction=sort' + params,
                cache: false,
                success: function(data) {
                    jQuery('#div_sflcontent').css('text-align', 'left');
                    jQuery('#div_sflcontent').html('').append(data);
                }
            });

            return false;
        });

        jQuery('#sfl_btnNext').click(function(evt) {

            var nextVal = document.getElementById('sflNextVal').value;
            var params = '&sflNext=' + nextVal + '&sflDir=' + getCurBrowserDir();
            var containerId = '#'+getCurSection()+' .sfl_content table tbody';

            jQuery(containerId)
                .html('')
                .append('<img style="position: relative; top: 50px;" src="/modules/mod_simplefilelisterv1.0/images/ajax-loader.gif" />')
                .fadeIn(700);

            jQuery.ajax({
                type: 'GET',
                url: curPageURL,
                data: 'sflaction=next' + params,
                cache: false,
                success: function(data) {

                    jQuery(containerId).html('').append(data);
                }
            });

            return false;
        });

        jQuery('#sfl_btnPrev').live('click', function() {

            var params = '';
            var prevVal = document.getElementById('sflPrevVal').value;

            if (prevVal+0 > -1) 
                params = '&sflPrevious=' + prevVal + '&sflDir=' + getCurBrowserDir();

            jQuery('#div_sflcontent')
                .css('text-align', 'center');

            jQuery('#div_sflcontent')
                .html('')
                .append('<img style="position: relative; top: 50px;" src="/modules/mod_simplefilelisterv1.0/images/ajax-loader.gif" />')
                .fadeIn(700, function() {
                  //$('#div_sflcontent').append("DONE!");
                });

            jQuery.ajax({
                type: 'GET',
                url: curPageURL,
                data: 'sflaction=prev' + params,
                cache: false,
                success: function(data) {
                    jQuery('#div_sflcontent').html('').append(data);
                }
            });

            return false;
        });
    });
})(jQuery);